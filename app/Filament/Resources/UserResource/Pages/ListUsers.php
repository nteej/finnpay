<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all'     => Tab::make('All')->badge(fn () => \App\Models\User::where('is_admin', false)->count()),
            'pending' => Tab::make('Pending Verification')
                ->badge(fn () => \App\Models\User::where('is_verified', false)->where('is_admin', false)->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_verified', false)->where('is_admin', false)),
            'verified' => Tab::make('Verified')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_verified', true)->where('is_admin', false)),
        ];
    }
}
