<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentReferenceResource\Pages;
use App\Models\PaymentReference;

use Filament\Actions\Action as FilamentAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentReferenceResource extends Resource
{
    protected static ?string $model = PaymentReference::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-qr-code';
    protected static \UnitEnum|string|null $navigationGroup = 'Payments';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = 'References';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference_number')->label('Reference')->fontFamily('mono')->searchable(),
                Tables\Columns\TextColumn::make('user.name')->label('Freelancer')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('title')->label('Title')->limit(35),
                Tables\Columns\TextColumn::make('currency')->badge(),
                Tables\Columns\TextColumn::make('amount_requested')
                    ->label('Requested')
                    ->formatStateUsing(fn ($state, $record) => $state ? ($record->currency === 'EUR' ? '€' : '$') . number_format($state, 2) : '—'),
                Tables\Columns\TextColumn::make('transactions_count')->label('Payments')->counts('transactions'),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->colors([
                        'success' => 'active',
                        'primary' => 'paid',
                        'warning' => 'expired',
                        'danger'  => 'cancelled',
                    ]),
                Tables\Columns\TextColumn::make('created_at')->label('Created')->date('d M Y')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['active' => 'Active', 'paid' => 'Paid', 'expired' => 'Expired', 'cancelled' => 'Cancelled']),
            ])
            ->defaultSort('created_at', 'desc')
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
            'index' => Pages\ListPaymentReferences::route('/'),
        ];
    }
}
