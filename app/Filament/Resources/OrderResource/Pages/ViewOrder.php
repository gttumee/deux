<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;
    
    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->url(OrderResource::getUrl()) 
                ->button()
                ->color('gray'),
            Actions\EditAction::make(),
        ];
    }
    
}