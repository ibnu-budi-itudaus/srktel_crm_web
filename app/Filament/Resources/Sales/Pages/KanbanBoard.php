<?php

namespace App\Filament\Resources\Sales\Pages;

use App\Filament\Resources\Sales\SaleResource;
use App\Models\Sale;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Collection;
use Filament\Widgets\Card;

use BackedEnum;

class KanbanBoard extends Page
{
    protected static string $resource = SaleResource::class;

    protected string $view = 'filament.resources.sales.pages.kanban-board';

    protected static ?string $slug = 'kanban-board';

    protected static ?string $title = 'Deals Pipeline (Kanban)';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    public Collection $sales;

    public array $statuses = [
        'Prospect',
        'Pending',
        'Deal',
       'No Deal',
    ];


    public function mount(): void
    {
        $this->sales = Sale::with('customer')->get();
    }

    public function updateStatus($saleId, $status): void
    {
        $sale = Sale::find($saleId);
        if ($sale) {
            $sale->status = $status;
            $sale->save();
            $this->sales = Sale::with('customer')->get();
        }
    }

     public function getSalesByStatus(string $status)
    {
        return Sale::with('project')
        ->where('status', $status)
        ->get();
    }

}
