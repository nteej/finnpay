<?php

namespace App\Filament\Resources\ReleaseCycleSettingResource\Pages;

use App\Filament\Resources\ReleaseCycleSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReleaseCycleSetting extends EditRecord
{
    protected static string $resource = ReleaseCycleSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
