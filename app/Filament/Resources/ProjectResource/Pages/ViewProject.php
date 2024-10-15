<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewProject extends ViewRecord
{
    protected static string $resource = ProjectResource::class;
    
    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->url(ProjectResource::getUrl())
                ->button()
                ->color('gray'),
            Actions\EditAction::make(),
        ];
    }
}