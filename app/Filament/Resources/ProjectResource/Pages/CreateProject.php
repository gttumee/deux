<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use App\Models\Project;
use App\Models\User;
use Filament\Actions;
use Filament\Notifications\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;


class CreateProject extends CreateRecord
{
    protected static string $resource = ProjectResource::class;
    
    protected function getRedirectUrl(): string
    {
        $user = auth()->user();
        $project = $this->record;
        $notification = Notification::make()
            ->success()
            ->title('Project created successfully')
            ->body("New Project {$project->id} {$project->name} created")
            ->actions([
                Action::make('view')->url(
                    ProjectResource::getUrl('create',['record'=>$project])
                ),
            ]);
        $notification->sendToDatabase($user);
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }
}