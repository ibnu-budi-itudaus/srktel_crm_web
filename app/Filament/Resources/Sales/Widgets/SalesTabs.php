<?php

namespace App\Filament\Resources\Sales\Widgets;

use App\Filament\Resources\Sales\SaleResource;
use Filament\Widgets\Widget;
use Illuminate\Contracts\View\View;

class SalesTabs extends Widget
{
    protected string $view = 'filament.resources.sales.pages._sales-tabs';
    protected static ?int $sort = -100; // Pastikan widget ini di-load pertama
    
    protected function getViewData(): array
    {
        $listUrl = SaleResource::getUrl('index');
        $pipelineUrl = SaleResource::getUrl('pipeline');
        $activeTab = session('active_sales_tab', 'list');
        
        return [
            'listUrl' => $listUrl,
            'pipelineUrl' => $pipelineUrl,
            'activeTab' => $activeTab,
        ];

      

    }

     
    // Add this to ensure proper rendering
    // public function render(): View
    // {
    //     return view($this->view, $this->getViewData());
    // }
}
