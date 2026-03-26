<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReleasePackageResource\Pages;
use App\Models\ReleasePackage;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ReleasePackageResource extends Resource
{
    protected static ?string $model = ReleasePackage::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-squares-2x2';
    protected static \UnitEnum|string|null $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Release Packages';
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Package Details')->schema([
                TextInput::make('name')->required()->maxLength(50),
                TextInput::make('slug')->required()->maxLength(50)
                    ->helperText('Lowercase, no spaces. e.g. starter'),
                Textarea::make('description')->rows(2)->columnSpanFull(),
                Select::make('color')
                    ->options(['slate' => 'Slate (Grey)', 'indigo' => 'Indigo (Blue)', 'amber' => 'Amber (Gold)'])
                    ->required(),
                TextInput::make('sort_order')->numeric()->default(0),
            ])->columns(2),

            Section::make('Release Schedule')->schema([
                TextInput::make('releases_per_month')
                    ->label('Releases Per Month')
                    ->numeric()->minValue(1)->maxValue(2)->required()
                    ->helperText('1 = once/month, 2 = twice/month'),
                TextInput::make('release_day_1')
                    ->label('First Release Day')
                    ->numeric()->minValue(1)->maxValue(28)->required(),
                TextInput::make('release_day_2')
                    ->label('Second Release Day')
                    ->numeric()->minValue(1)->maxValue(28)
                    ->helperText('Required if releases per month = 2'),
                TextInput::make('minimum_balance_lkr')
                    ->label('Minimum Balance (LKR)')
                    ->numeric()->minValue(0)->required()
                    ->helperText('0 = no minimum'),
                Toggle::make('allow_manual_release')->label('Allow Manual Release'),
                Toggle::make('is_active')->label('Active')->default(true),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('releases_per_month')->label('Releases/Month')
                    ->formatStateUsing(fn ($state) => $state === 1 ? 'Once' : 'Twice'),
                Tables\Columns\TextColumn::make('release_day_1')->label('Day 1')
                    ->formatStateUsing(fn ($state) => \ordinal_suffix((int) $state)),
                Tables\Columns\TextColumn::make('release_day_2')->label('Day 2')
                    ->formatStateUsing(fn ($state) => $state ? \ordinal_suffix((int) $state) : '—'),
                Tables\Columns\TextColumn::make('minimum_balance_lkr')->label('Min. Balance')
                    ->formatStateUsing(fn ($state) => $state > 0 ? 'LKR ' . number_format($state) : 'None'),
                Tables\Columns\IconColumn::make('allow_manual_release')->label('Manual')->boolean(),
                Tables\Columns\TextColumn::make('user_packages_count')->label('Subscribers')
                    ->counts('userPackages'),
                Tables\Columns\IconColumn::make('is_active')->label('Active')->boolean(),
            ])
            ->reorderable('sort_order')
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListReleasePackages::route('/'),
            'create' => Pages\CreateReleasePackage::route('/create'),
            'edit'   => Pages\EditReleasePackage::route('/{record}/edit'),
        ];
    }
}
