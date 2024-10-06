<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use App\Models\Project;
use Filament\Actions;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;
    
    protected function getRedirectUrl(): string
    {
        $user = auth()->user();
        $project = $this->record;
        $notification = Notification::make()
            ->success()
            ->title('Project edited successfully')
            ->body("Project {$project->id} {$project->name} edited")
            ->actions([
                Action::make('view')->url(
                    Project::getUrl('view',['record'=>$project])
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