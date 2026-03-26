<?php

namespace App\Filament\Resources\ReleasePackageResource\Pages;

use App\Filament\Resources\ReleasePackageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReleasePackages extends ListRecords
{
    protected static string $resource = ReleasePackageResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
