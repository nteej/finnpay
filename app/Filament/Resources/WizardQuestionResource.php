<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WizardQuestionResource\Pages;
use App\Models\WizardQuestion;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class WizardQuestionResource extends Resource
{
    protected static ?string $model = WizardQuestion::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static \UnitEnum|string|null $navigationGroup = 'Configuration';
    protected static ?string $navigationLabel = 'Wizard Questions';
    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Question')->schema([
                TextInput::make('section')
                    ->label('Section / Step name')
                    ->placeholder('e.g. Business Info, Payment Preferences')
                    ->default('General')
                    ->required()
                    ->maxLength(100),

                Textarea::make('question_text')
                    ->label('Question')
                    ->required()
                    ->rows(2)
                    ->columnSpanFull(),

                TextInput::make('helper_text')
                    ->label('Helper text')
                    ->placeholder('Optional hint shown below the field')
                    ->columnSpanFull(),

                Select::make('type')
                    ->label('Answer type')
                    ->options(WizardQuestion::TYPES)
                    ->default('text')
                    ->required()
                    ->live(),

                TagsInput::make('options')
                    ->label('Options')
                    ->placeholder('Add option…')
                    ->helperText('Press Enter after each option')
                    ->visible(fn (Get $get) => in_array($get('type'), ['select', 'radio', 'checkbox']))
                    ->columnSpanFull(),

                TextInput::make('sort_order')
                    ->label('Order')
                    ->numeric()
                    ->default(0),

                Toggle::make('is_required')->label('Required')->default(true),
                Toggle::make('is_active')->label('Active')->default(true),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')->label('#')->sortable()->width(40),
                Tables\Columns\TextColumn::make('section')->badge()->color('gray')->sortable(),
                Tables\Columns\TextColumn::make('question_text')->label('Question')->limit(70)->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->formatStateUsing(fn ($state) => WizardQuestion::TYPES[$state] ?? $state)
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'select', 'radio', 'checkbox' => 'info',
                        'boolean' => 'success',
                        default   => 'gray',
                    }),
                Tables\Columns\IconColumn::make('is_required')->label('Req.')->boolean(),
                Tables\Columns\IconColumn::make('is_active')->label('Active')->boolean(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->actions([EditAction::make(), DeleteAction::make()])
            ->emptyStateHeading('No wizard questions yet')
            ->emptyStateDescription('Create questions to build the freelancer onboarding wizard.');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListWizardQuestions::route('/'),
            'create' => Pages\CreateWizardQuestion::route('/create'),
            'edit'   => Pages\EditWizardQuestion::route('/{record}/edit'),
        ];
    }
}
