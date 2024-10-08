<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Livewire\Attributes\Title;
use Filament\Notifications\Actions\Action;


class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;
    protected static bool $canCreateAnother = false;
    protected function getRedirectUrl(): string
    {
        $user = auth()->user();
        $order = $this->record;
        $notification = Notification::make()
            ->success()
            ->title('Order created successfully')
            ->body("New Order {$order->id} {$order->name} created")
            ->actions([
                Action::make('view')->url(
                    OrderResource::getUrl('view',['record'=>$order])
                ),
            ]);
        $notification->sendToDatabase($user);
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }
    
}