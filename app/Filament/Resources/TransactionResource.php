<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Transaction;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
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
        return $schema->components([
            Section::make('Payer')->schema([
                TextInput::make('payer_name')->label('Payer Name')->disabled(),
                TextInput::make('payer_email')->label('Payer Email')->disabled(),
            ])->columns(2),

            Section::make('Payment Details')->schema([
                TextInput::make('currency_type')->label('Currency')->disabled(),
                TextInput::make('status')->label('Status')->disabled(),
                DatePicker::make('received_at')->label('Received At')->disabled(),
                TextInput::make('amount_usd')->label('Gross USD')->prefix('$')->disabled(),
                TextInput::make('amount_eur')->label('Gross EUR')->prefix('€')->disabled(),
                TextInput::make('fee_usd')->label('Fee USD')->prefix('$')->disabled(),
                TextInput::make('fee_eur')->label('Fee EUR')->prefix('€')->disabled(),
                TextInput::make('final_usd')->label('Net USD')->prefix('$')->disabled(),
                TextInput::make('final_eur')->label('Net EUR')->prefix('€')->disabled(),
                TextInput::make('lkr_rate')->label('LKR Rate')->disabled(),
                TextInput::make('final_lkr')->label('Final LKR')->prefix('LKR')->disabled(),
            ])->columns(3),

            Section::make('Reference & Release')->schema([
                TextInput::make('paymentReference.reference_number')->label('Payment Reference')->disabled(),
                TextInput::make('paypal_transaction_id')->label('PayPal Transaction ID')->disabled(),
            ])->columns(2),
        ]);
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
