<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;


class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;
    protected function getRedirectUrl(): string
    {
        $user = auth()->user();
        $order = $this->record;
        $notification = Notification::make()
            ->success()
            ->title('Order edited successfully')
            ->body("Order {$order->id} {$order->name} edited")
            ->actions([
                Action::make('view')->url(
                    OrderResource::getUrl('view',['record'=>$order])
                ),
            ]);
        $notification->sendToDatabase($user);
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}