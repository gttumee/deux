<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Models\Project;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Parallax\FilamentComments\Infolists\Components\CommentsEntry;
class OrderResource extends Resource
{
    public static function getNavigationBadge(): ?string
    {
    return static::getModel()::where('status', '!=', 'finish')->count();
    }
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'heroicon-s-ticket';
    protected static ?int $navigationSort = 3;
    
    public static function form(Form $form): Form
{
    $latestId = Order::max('id'); 
    $newId = $latestId ? $latestId + 1 : 1; 
    return $form
        ->schema([
            TextInput::make('id')->label('Order No')->default($newId)->disabled()
                ->columnSpan('full'),
            Select::make('project_id')->label('Select Project')->options(Project::all()->pluck('name', 'id'))
                ->required()
                ->columnSpan('full')
                ->placeholder('Choose project'),
            TextInput::make('name')->required()->maxLength(255)->label('Order name')
                ->columnSpan('full')
                ->placeholder('Order name')
                ->required(),
            RichEditor::make('explanation')
                ->columnSpan('full')
                ->placeholder('Description and File URL')
                ->disableToolbarButtons([
                    'blockquote',
                    'strike',
                    'attachFiles',
                    'codeBlock',
                ]),
            Select::make('user_id')->label('Send to')->options(User::all()->pluck('name', 'id'))->required(), 
            Select::make('status')
                ->options([
                    'New' => 'New',
                    'Process' => 'Process',
                    'Review' => 'Review',
                    'Finish' => 'Finish',
                ])
                ->native(false)
                ->default('New')
                ->required(),
            DatePicker::make('end_date')
            ->required(),
            TextInput::make('end_time')
                ->label('End Time (HH)')
                ->maxLength(2)
                ->maxValue(23)
                ->minValue(0)
                ->placeholder('12')
                ->numeric(), 
        ]);
}
    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(
                fn (Order $record): string => Pages\ViewOrder::getUrl([$record->id]),
                )
            ->columns([
                TextColumn::make('id')
                ->label('No')->sortable(),
                TextColumn::make('name')->label('Name')->sortable(),
                TextColumn::make('project.name')->label('Project'),
                TextColumn::make('status')
                ->sortable()
                ->badge()
                ->color(fn ($record) => match ($record->status) {
                    'New' => 'danger',
                    'Process' => 'warning',
                    'Finish' => 'success',
                }),
                TextColumn::make('created_at')
                ->formatStateUsing(fn ($state) => $state ? Carbon::parse($state)->format('Y-m-d') : '不明')->sortable()
                ->label('Create Date'),
                TextColumn::make('end_date')
                ->formatStateUsing(fn ($state) => $state ? Carbon::parse($state)->format('Y-m-d') : '不明')->sortable()
                ->badge()
                ->label('End Date'),
                TextColumn::make('end_time')
                ->badge()
                ->label('End Time'),
                TextColumn::make('user.name') 
                ->label('Assignee') 
                ->sortable()
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Filter::make('id')
                    ->form([
                        TextInput::make('id')->label('Order No'),
                    ])
                    ->query(function (Builder $query, $data) {
                        $searchWord = $data['id'] ?? null; // nullチェックを追加
                        
                        return $query->when(
                            $searchWord,
                            function (Builder $query) use ($searchWord) {
                                return $query->where('id', 'like', "%{$searchWord}%");
                            }
                        );
                    }),
            
                Filter::make('name')
                    ->form([
                        TextInput::make('name')->label('Order Name'),
                    ])
                    ->query(function (Builder $query, $data) {
                        $searchWord = $data['name'] ?? null;
                        return $query->when(
                            $searchWord,
                            function (Builder $query) use ($searchWord) {
                                return $query->where('name', 'like', "%{$searchWord}%");
                            }
                        );
                    }),
            
                Filter::make('status')
                    ->form([
                        Select::make('status')
                            ->options([
                                'New' => 'New',
                                'Process' => 'Process',
                                'Review' => 'Review',
                                'Finish' => 'Finish',
                            ])
                    ])
                    ->query(function (Builder $query, $data) {
                        $status = $data['status'] ?? null;
                        return $query->when(
                            $status,
                            function (Builder $query) use ($status) {
                                return $query->where('status', $status);
                            }
                        );
                    }),
            
                Filter::make('Assignee')
                    ->form([
                        Select::make('user_id')
                            ->label('Assignee')
                            ->options(User::all()->pluck('name', 'id'))
                    ])
                    ->query(function (Builder $query, $data) {
                        $userId = $data['user_id'] ?? null;
                        return $query->when(
                            $userId,
                            function (Builder $query) use ($userId) {
                                return $query->where('user_id', $userId);
                            }
                        );
                    }),
                Filter::make('End Date')
                    ->form([
                        DatePicker::make('end_date')
                    ])
                    ->query(function (Builder $query, $data) {
                        $endDate = $data['end_date'] ?? null;
                        return $query->when(
                            $endDate,
                            function (Builder $query) use ($endDate) {
                                return $query->whereDate('end_date', '<=', $endDate);
                            }
                        );
                    }),
            ], layout: FiltersLayout::AboveContentCollapsible)
            
            
              ->actions([
                Tables\Actions\EditAction::make(),
                tables\Actions\DeleteAction::make()
                ->after(function ($record) {
                    Notification::make()
                        ->success()
                        ->title('Order deleted successfully')
                        ->body("Order id {$record->id} has been deleted.")
                        ->sendToDatabase(auth()->user());
                }),
                
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
{
    return $infolist
        ->schema([
            Section::make('Order Detail')
            ->schema([
                Grid::make(3)
                    ->schema([
                        TextEntry::make('id')->label('Order No'),
                        TextEntry::make('name')->label('Order Name'),
                        TextEntry::make('status')->badge(),
                        TextEntry::make('user.name')->label('Assignee'),
                        TextEntry::make('project.name')->label('Project Name'),
                    ]),
                   
        ]),
        Section::make('')
        ->schema([
            Grid::make(1)
                ->schema([
                    TextEntry::make('explanation')->label('Description')->html(),
                ]),
            Grid::make(5)
                ->schema([
                    TextEntry::make('created_at')->label('Create Date')->date(),
                    TextEntry::make('end_date')->label('End Date')->badge()->date(),
                    TextEntry::make('end_time')->label('End Time')->badge(),
                ]),
    ]),
         Section::make('Comment')
        ->schema([
            Grid::make(1)
                ->schema([
                    CommentsEntry::make('filament_comments'),
                ]),
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'activities' => Pages\ActivityLogPage::route('/{record}/activities'),
        ];
    }
}