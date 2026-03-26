<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Transaction;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Table;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-banknotes';
    protected static \UnitEnum|string|null $navigationGroup = 'Payments';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('received_at')->label('Date')->date('d M Y')->sortable(),
                Tables\Columns\TextColumn::make('user.name')->label('Freelancer')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('payer_name')->label('Payer')->searchable(),
                Tables\Columns\TextColumn::make('currency_type')->label('Currency')->badge(),
                Tables\Columns\TextColumn::make('display_amount')->label('Net Amount')->weight('bold'),
                Tables\Columns\TextColumn::make('final_lkr')
                    ->label('LKR')
                    ->formatStateUsing(fn ($state) => $state ? 'LKR ' . number_format($state, 2) : '—'),
                Tables\Columns\TextColumn::make('paymentReference.reference_number')
                    ->label('Reference')
                    ->fontFamily('mono')
                    ->default('—'),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->colors([
                        'warning' => 'pending',
                        'primary' => 'cleared',
                        'success' => 'released',
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['pending' => 'Pending', 'cleared' => 'Cleared', 'released' => 'Released']),
                Tables\Filters\SelectFilter::make('currency_type')
                    ->label('Currency')
                    ->options(['USD' => 'USD', 'EUR' => 'EUR']),
            ])
            ->defaultSort('received_at', 'desc')
            ->actions([
                ViewAction::make(),
            ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
        ];
    }
}
