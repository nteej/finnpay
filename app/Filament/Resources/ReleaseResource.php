<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReleaseResource\Pages;
use App\Models\ExchangeRate;
use App\Models\Release;
use App\Models\Transaction;

use Filament\Actions\Action as FilamentAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
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
                Tables\Columns\TextColumn::make('period_start')->label('Period Start')->date('d M Y'),
                Tables\Columns\TextColumn::make('period_end')->label('Period End')->date('d M Y'),
                Tables\Columns\TextColumn::make('transaction_count')->label('Txns'),
                Tables\Columns\TextColumn::make('total_lkr')
                    ->label('Total LKR')
                    ->formatStateUsing(fn ($state) => 'LKR ' . number_format($state, 2))
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('bank_name')->label('Bank')->default('—'),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->colors([
                        'warning' => 'scheduled',
                        'primary' => 'processing',
                        'success' => 'completed',
                        'danger'  => 'failed',
                    ]),
                Tables\Columns\TextColumn::make('processed_at')->label('Processed')->dateTime('d M Y, H:i')->default('—'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['scheduled' => 'Scheduled', 'processing' => 'Processing', 'completed' => 'Completed', 'failed' => 'Failed']),
            ])
            ->actions([
                FilamentAction::make('process')
                    ->label('Process Release')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Process this Release')
                    ->modalDescription('This will mark the release as completed and update all linked transactions.')
                    ->visible(fn (Release $record) => $record->status === 'scheduled')
                    ->action(function (Release $record) {
                        DB::transaction(function () use ($record) {
                            $record->update([
                                'status'       => 'completed',
                                'processed_at' => now(),
                                'exchange_rate_usd_lkr' => ExchangeRate::getRate('USD'),
                                'exchange_rate_eur_lkr' => ExchangeRate::getRate('EUR'),
                            ]);
                            Transaction::where('release_id', $record->id)
                                ->update(['status' => 'released']);
                        });
                        Notification::make()->title('Release processed successfully')->success()->send();
                    }),

                FilamentAction::make('admin_release')
                    ->label('Force Release for User')
                    ->icon('heroicon-o-bolt')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn (Release $record) => $record->status !== 'completed')
                    ->action(function (Release $record) {
                        $record->update(['status' => 'completed', 'processed_at' => now()]);
                        Notification::make()->title('Release force-completed by admin')->warning()->send();
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
