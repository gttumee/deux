<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Filament\Resources\ProjectResource\Pages\ActivityLogPage;
use Filament\Actions\Action;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    public static function getNavigationBadge(): ?string
    {
    return static::getModel()::count();
    }
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
          TextInput::make('name')->required()->maxLength(255)
          ->columnSpan('full'),
          RichEditor::make('explanation')->required()->maxLength(255)
          ->columnSpan('full')
          ->disableToolbarButtons([
            'blockquote',
            'strike',
            'attachFiles',
            'codeBlock',
          ]),
          Select::make('status')
            ->options([
            'new' => 'New',
            'process' => 'Process',
            'finish' => 'Finish',
            ])
            ->default('new'),
          DatePicker::make('end_date'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->recordUrl(
            fn (Project $record): string => Pages\ViewProject::getUrl([$record->id]),
            )
        ->columns([
            TextColumn::make('name'),
            TextColumn::make('status')
            ->sortable()
            ->badge()
            ->color(fn ($record) => match ($record->status) {
                'new' => 'success',
                'process' => 'warning',
                'finish' => 'success',
                 default => 'default',
            }),
            TextColumn::make('created_at')
            ->label('Create Date'),
            TextColumn::make('end_date'),
        ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
            'view' => Pages\ViewProject::route('/{record}'),
            'activities' => Pages\ActivityLogPage::route('/{record}/activities'),
        ];
    }
}