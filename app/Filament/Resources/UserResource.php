<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\OnboardingWizard;
use App\Models\ReleasePackage;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\Action as FilamentAction;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-users';
    protected static \UnitEnum|string|null $navigationGroup = 'User Management';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Personal Details')->schema([
                TextInput::make('name')->required()->maxLength(255),
                TextInput::make('email')->email()->required()->unique(ignoreRecord: true),
                TextInput::make('phone')->tel(),
                TextInput::make('freelancer_id')->disabled()->dehydrated(false),
            ])->columns(2),

            Section::make('Access & Status')->schema([
                Toggle::make('is_verified')->label('Verified')->helperText('Allow access to the freelancer portal'),
                Toggle::make('is_active')->label('Active'),
                Toggle::make('is_admin')->label('Admin Access')->helperText('Grants access to this admin panel'),
                Textarea::make('rejection_reason')->label('Rejection Reason')->rows(2)->columnSpanFull(),
            ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('freelancer_id')->label('ID')->searchable()->fontFamily('mono'),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('phone')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_verified')->label('Verified')->boolean(),
                Tables\Columns\IconColumn::make('is_active')->label('Active')->boolean(),
                Tables\Columns\IconColumn::make('is_admin')->label('Admin')->boolean(),
                Tables\Columns\TextColumn::make('package')
                    ->label('Package')
                    ->getStateUsing(fn (User $r) => $r->activePackage()?->name ?? '—')
                    ->badge(),
                Tables\Columns\TextColumn::make('onboarding_status')
                    ->label('Onboarding')
                    ->getStateUsing(fn (User $r) => match($r->onboardingStatus()) {
                        'no_wizard'           => 'No Link Sent',
                        'wizard_pending'      => 'Awaiting Answers',
                        'pending_package'     => 'Needs Package',
                        'active'              => 'Active',
                        'pending_verification'=> 'Unverified',
                        default               => '—',
                    })
                    ->badge()
                    ->color(fn (string $state) => match($state) {
                        'Active'          => 'success',
                        'Needs Package'   => 'warning',
                        'Awaiting Answers'=> 'info',
                        'No Link Sent'    => 'gray',
                        default           => 'gray',
                    }),
                Tables\Columns\TextColumn::make('transactions_count')->label('Txns')->counts('transactions'),
                Tables\Columns\TextColumn::make('verified_at')->label('Verified At')->dateTime('d M Y')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('created_at')->label('Registered')->date('d M Y')->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_verified')->label('Verification Status')
                    ->trueLabel('Verified')->falseLabel('Pending'),
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                FilamentAction::make('verify')
                    ->label('Verify')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (User $record) => ! $record->is_verified && ! $record->is_admin)
                    ->action(function (User $record) {
                        $record->update([
                            'is_verified'      => true,
                            'verified_at'      => now(),
                            'verified_by'      => auth()->id(),
                            'rejection_reason' => null,
                        ]);
                        Notification::make()->title('User verified successfully')->success()->send();
                    }),

                FilamentAction::make('reject')
                    ->label('Revoke')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (User $record) => $record->is_verified && ! $record->is_admin)
                    ->form([
                        Textarea::make('rejection_reason')
                            ->label('Reason for rejection')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (User $record, array $data) {
                        $record->update([
                            'is_verified'      => false,
                            'verified_at'      => null,
                            'rejection_reason' => $data['rejection_reason'],
                        ]);
                        Notification::make()->title('User access revoked')->warning()->send();
                    }),

                FilamentAction::make('sendWizardLink')
                    ->label('Send Wizard Link')
                    ->icon('heroicon-o-link')
                    ->color('info')
                    ->visible(fn (User $record) => $record->is_verified && ! $record->is_admin)
                    ->fillForm(function (User $record) {
                        $wizard = $record->onboardingWizard()->firstOrCreate(
                            ['user_id' => $record->id],
                            ['token' => Str::random(64), 'sent_at' => now()]
                        );
                        if (! $wizard->wasRecentlyCreated && ! $wizard->sent_at) {
                            $wizard->update(['sent_at' => now()]);
                        }
                        return ['wizard_link' => route('onboarding.show', $wizard->token)];
                    })
                    ->form([
                        TextInput::make('wizard_link')
                            ->label('Onboarding Link')
                            ->readOnly()
                            ->helperText('Copy this link and send it to the freelancer to complete their onboarding questionnaire.'),
                    ])
                    ->modalSubmitActionLabel('Done')
                    ->action(fn () => null),

                FilamentAction::make('viewWizardAnswers')
                    ->label('View Answers')
                    ->icon('heroicon-o-document-text')
                    ->color('gray')
                    ->visible(fn (User $record) => $record->onboardingWizard?->isCompleted())
                    ->modalContent(function (User $record) {
                        $wizard = $record->onboardingWizard->load('responses.question');
                        return view('filament.wizard-responses', compact('wizard'));
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),

                FilamentAction::make('assign_package')
                    ->label('Assign Package')
                    ->icon('heroicon-o-squares-2x2')
                    ->color('warning')
                    ->visible(fn (User $record) => ! $record->is_admin && $record->onboardingWizard?->isCompleted() && ! $record->activePackage())
                    ->form([
                        Select::make('release_package_id')
                            ->label('Package')
                            ->options(ReleasePackage::where('is_active', true)->pluck('name', 'id'))
                            ->required(),
                    ])
                    ->action(function (User $record, array $data) {
                        $record->userPackages()->where('is_active', true)->update(['is_active' => false]);
                        $record->userPackages()->create([
                            'release_package_id' => $data['release_package_id'],
                            'started_at'         => now(),
                            'locked_until'       => now()->addMonths(3),
                            'is_active'          => true,
                            'changed_by'         => auth()->id(),
                        ]);
                        Notification::make()->title('Package assigned')->success()->send();
                    }),

                EditAction::make(),
            ])
            ->bulkActions([
                BulkAction::make('verify_selected')
                    ->label('Verify Selected')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function ($records) {
                        $records->each(fn (User $u) => $u->update([
                            'is_verified' => true,
                            'verified_at' => now(),
                            'verified_by' => auth()->id(),
                        ]));
                        Notification::make()->title('Users verified')->success()->send();
                    }),
                DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = User::where('is_verified', false)->where('is_admin', false)->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
