<?php

namespace App\Observers;
namespace App\Observers;

use Parallax\FilamentComments\Models\FilamentComment;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use App\Filament\Resources\OrderResource;

class CommentObserver
{
    /**
     * Handle the FilamentComment "created" event.
     */
    public function created(FilamentComment $comment): void
    {
        $user = auth()->user(); 
        $order = $comment->subject;
        $notification = Notification::make()
            ->success()
            ->title('Comment created successfully')
            ->body("New Comment on Order {$order->id}: {$comment->comment}")
            ->actions([
                Action::make('view')->url(
                    OrderResource::getUrl('view', ['record' => $order])
                ),
            ]);

        $notification->sendToDatabase($user);
    }

    /**
     * Handle the FilamentComment "updated" event.
     */
    public function updated(FilamentComment $filamentComment): void
    {
        //
    }

    /**
     * Handle the FilamentComment "deleted" event.
     */
    public function deleted(FilamentComment $comment): void
    {
        $user = auth()->user(); 
        $order = $comment->subject;
        $notification = Notification::make()
            ->success()
            ->title('Comment deleted successfully')
            ->body("delete comment {$order->id}: {$comment->comment}")
            ->actions([
                Action::make('view')->url(
                    OrderResource::getUrl('view', ['record' => $order])
                ),
            ]);

        $notification->sendToDatabase($user);
    }

    /**
     * Handle the FilamentComment "restored" event.
     */
    public function restored(FilamentComment $filamentComment): void
    {
        //
    }

    /**
     * Handle the FilamentComment "force deleted" event.
     */
    public function forceDeleted(FilamentComment $filamentComment): void
    {
        //
    }
}