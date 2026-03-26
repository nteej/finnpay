<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReleaseCycleSettingResource\Pages;
use App\Models\ReleaseCycleSetting;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ReleaseCycleSettingResource extends Resource
{
    protected static ?string $model = ReleaseCycleSetting::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-calendar-days';
    protected static \UnitEnum|string|null $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Release Cycle';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Release Schedule')
                ->description('Define when payments are automatically released to freelancers each month.')
                ->schema([
                    TextInput::make('release_day_1')
                        ->label('First Release Day')
                        ->helperText('Day of month (1-28). Default: 1st')
                        ->numeric()->minValue(1)->maxValue(28)->required(),
                    TextInput::make('release_day_2')
                        ->label('Second Release Day')
                        ->helperText('Day of month (1-28). Default: 16th')
                        ->numeric()->minValue(1)->maxValue(28)->required(),
                    Toggle::make('allow_manual_release')
                        ->label('Allow Manual Release')
                        ->helperText('Freelancers can trigger a release outside the schedule'),
                    TextInput::make('minimum_balance_lkr')
                        ->label('Minimum Balance (LKR)')
                        ->helperText('Minimum LKR balance required to trigger a release. 0 = no minimum')
                        ->numeric()->minValue(0)->required(),
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
                Tables\Columns\TextColumn::make('release_day_1')
                    ->label('Release Day 1')
                    ->formatStateUsing(fn ($state) => ordinal((int) $state) . ' of month'),
                Tables\Columns\TextColumn::make('release_day_2')
                    ->label('Release Day 2')
                    ->formatStateUsing(fn ($state) => ordinal((int) $state) . ' of month'),
                Tables\Columns\IconColumn::make('allow_manual_release')->label('Manual Allowed')->boolean(),
                Tables\Columns\TextColumn::make('minimum_balance_lkr')
                    ->label('Min. Balance')
                    ->formatStateUsing(fn ($state) => $state > 0 ? 'LKR ' . number_format($state) : 'None'),
                Tables\Columns\TextColumn::make('updated_at')->label('Last Updated')->dateTime('d M Y, H:i'),
            ])
            ->actions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListReleaseCycleSettings::route('/'),
            'create' => Pages\CreateReleaseCycleSetting::route('/create'),
            'edit'   => Pages\EditReleaseCycleSetting::route('/{record}/edit'),
        ];
    }
}

if (!function_exists('App\Filament\Resources\ordinal')) {
    function ordinal(int $n): string
    {
        $s = ['th', 'st', 'nd', 'rd'];
        $v = $n % 100;
        return $n . ($s[($v - 20) % 10] ?? $s[$v] ?? $s[0]);
    }
}
