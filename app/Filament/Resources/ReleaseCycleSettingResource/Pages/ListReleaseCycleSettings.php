<?php

namespace App\Filament\Resources\ReleaseCycleSettingResource\Pages;

use App\Filament\Resources\ReleaseCycleSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReleaseCycleSettings extends ListRecords
{
    protected static string $resource = ReleaseCycleSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()->label('Add Configuration')];
    }
}
