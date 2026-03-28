<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExchangeRateResource\Pages;
use App\Models\ExchangeRate;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ExchangeRateResource extends Resource
{
    protected static ?string $model = ExchangeRate::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-currency-dollar';
    protected static \UnitEnum|string|null $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Exchange Rates';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Rate Configuration')->schema([
                Select::make('currency_from')
                    ->label('From Currency')
                    ->options(['USD' => 'USD — US Dollar', 'EUR' => 'EUR — Euro'])
                    ->required(),
                Select::make('currency_to')
                    ->label('To Currency')
                    ->options(['LKR' => 'LKR — Sri Lankan Rupee'])
                    ->default('LKR')
                    ->required(),
                TextInput::make('buy_rate')
                    ->label('Buy Rate')
                    ->numeric()
                    ->step(0.0001)
                    ->required()
                    ->helperText('e.g. 310.6542 means 1 USD = 310.6542 LKR (buy)'),
                TextInput::make('sell_rate')
                    ->label('Sell Rate')
                    ->numeric()
                    ->step(0.0001)
                    ->helperText('e.g. 318.1938 means 1 USD = 318.1938 LKR (sell)'),
                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
                Textarea::make('notes')
                    ->label('Notes')
                    ->rows(2)
                    ->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('currency_from')->label('From')->badge()->color('info'),
                Tables\Columns\TextColumn::make('currency_to')->label('To')->badge()->color('success'),
                Tables\Columns\TextColumn::make('buy_rate')
                    ->label('Buy Rate')
                    ->formatStateUsing(fn ($state, $record) => "1 {$record->currency_from} = {$state} {$record->currency_to}")
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('sell_rate')
                    ->label('Sell Rate')
                    ->formatStateUsing(fn ($state, $record) => "1 {$record->currency_from} = {$state} {$record->currency_to}"),
                Tables\Columns\IconColumn::make('is_active')->label('Active')->boolean(),
                Tables\Columns\TextColumn::make('updatedBy.name')->label('Updated By')->default('—'),
                Tables\Columns\TextColumn::make('updated_at')->label('Last Updated')->dateTime('d M Y, H:i')->sortable(),
            ])
            ->actions([
                EditAction::make()
                    ->mutateFormDataUsing(function (array $data) {
                        $data['updated_by'] = auth()->id();
                        return $data;
                    }),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListExchangeRates::route('/'),
            'create' => Pages\CreateExchangeRate::route('/create'),
            'edit'   => Pages\EditExchangeRate::route('/{record}/edit'),
        ];
    }
}
