<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReleaseResource\Pages;
use App\Models\ExchangeRate;
use App\Models\Release;
use App\Models\Transaction;

use Filament\Actions\Action as FilamentAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReleaseResource extends Resource
{
    protected static ?string $model = Release::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-arrow-down-on-square';
    protected static \UnitEnum|string|null $navigationGroup = 'Payments';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('release_code')->label('Code')->fontFamily('mono')->searchable(),
                Tables\Columns\TextColumn::make('user.name')->label('Freelancer')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('claimed_at')->label('Claimed')->dateTime('d M Y, H:i')->default('—'),
                Tables\Columns\TextColumn::make('transaction_count')->label('Txns'),
                Tables\Columns\TextColumn::make('total_lkr')
                    ->label('Total LKR')
                    ->formatStateUsing(fn ($state) => 'LKR ' . number_format($state, 2))
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('bank_name')->label('Bank')->default('—'),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->colors([
                        'warning' => 'pending_approval',
                        'gray'    => 'scheduled',
                        'primary' => 'processing',
                        'success' => 'completed',
                        'danger'  => fn ($state) => in_array($state, ['failed', 'rejected']),
                    ]),
                Tables\Columns\TextColumn::make('processed_at')->label('Processed')->dateTime('d M Y, H:i')->default('—'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending_approval' => 'Pending Approval',
                        'scheduled'        => 'Scheduled',
                        'processing'       => 'Processing',
                        'completed'        => 'Completed',
                        'rejected'         => 'Rejected',
                        'failed'           => 'Failed',
                    ]),
            ])
            ->actions([
                // Approve a pending claim
                FilamentAction::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Approve Payment Claim')
                    ->modalDescription('This will mark the claim as completed and release the funds to the freelancer\'s bank account.')
                    ->visible(fn (Release $record) => $record->status === 'pending_approval')
                    ->action(function (Release $record) {
                        DB::transaction(function () use ($record) {
                            $usdRate = ExchangeRate::getRate('USD');
                            $eurRate = ExchangeRate::getRate('EUR');

                            $record->update([
                                'status'                => 'completed',
                                'exchange_rate_usd_lkr' => $usdRate,
                                'exchange_rate_eur_lkr' => $eurRate,
                                'approved_by'           => Auth::id(),
                                'approved_at'           => now(),
                                'processed_at'          => now(),
                            ]);

                            Transaction::where('release_id', $record->id)
                                ->update(['status' => 'released']);
                        });
                        Notification::make()->title('Claim approved — funds released to freelancer')->success()->send();
                    }),

                // Reject a pending claim
                FilamentAction::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        Textarea::make('rejection_reason')
                            ->label('Reason for rejection')
                            ->required()
                            ->rows(3)
                            ->placeholder('Explain why this claim is being rejected…'),
                    ])
                    ->modalHeading('Reject Payment Claim')
                    ->visible(fn (Release $record) => $record->status === 'pending_approval')
                    ->action(function (Release $record, array $data) {
                        DB::transaction(function () use ($record, $data) {
                            // Unlink transactions so they remain cleared and claimable again
                            Transaction::where('release_id', $record->id)
                                ->update(['release_id' => null]);

                            $record->update([
                                'status'           => 'rejected',
                                'rejected_by'      => Auth::id(),
                                'rejected_at'      => now(),
                                'rejection_reason' => $data['rejection_reason'],
                            ]);
                        });
                        Notification::make()->title('Claim rejected')->danger()->send();
                    }),

                ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReleases::route('/'),
        ];
    }
}
