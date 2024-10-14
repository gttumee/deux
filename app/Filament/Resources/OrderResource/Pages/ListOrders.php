<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    
    public function getTabs(): array
    {
        return [  
            'All' => Tab::make(), 
            'Today’s Completed' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('end_date', 'like', Carbon::today()->format('Y-m-d') . '%')),
            
            'New Orders' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'new')),
            
            'Finish Orders' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'finish')),    
            
                'This Week' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereBetween('end_date', [
                    Carbon::now()->startOfWeek()->format('Y-m-d H:i:s'),
                    Carbon::now()->endOfWeek()->format('Y-m-d H:i:s')
                ])),
            
            'This Month' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('end_date', 'like', Carbon::now()->format('Y-m') . '%')),
            
            'My Orders' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('user_id', auth()->id())),
            
            'Overdue' => Tab::make() 
                ->modifyQueryUsing(fn (Builder $query) => $query->where('end_date', '<', now()))
        ];
    }
    
    
}