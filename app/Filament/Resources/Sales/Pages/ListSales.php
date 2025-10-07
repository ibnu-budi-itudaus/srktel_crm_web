<?php

namespace App\Filament\Resources\Sales\Pages;

use UnitEnum;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Illuminate\Contracts\View\View;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use App\Filament\Resources\Sales\SaleResource;
use App\Filament\Resources\Sales\Widgets\SalesTabs;

class ListSales extends ListRecords
{
    protected static string $resource = SaleResource::class;
    protected function getHeaderActions(): array
   
    {
        return [
            CreateAction::make(),
             Action::make('history')
            ->label('Riwayat Arsip')
            ->icon('heroicon-m-archive-box')
            ->url(static::getResource()::getUrl('history')),
        ];
    }

     protected function getHeaderWidgets(): array
    {
        return [
                 SalesTabs::class,
        ];
    }

     // Tambahkan method ini untuk mengatur session
    public function mount(): void
    {
        parent::mount();
       // Debugging - hapus komentar untuk melihat nilai
    // dump('Setting session to list in ListSales');
    
    session()->put('active_sales_tab', 'list');
    
    // Debugging - hapus komentar untuk melihat nilai
    // dump('Session value after setting: ' . session('active_sales_tab'));
    }

    // Override untuk memastikan styling konsisten
    // Tambahkan ini di bawah


    
    // Gunakan getHeader untuk inject tabs
    // public function getHeader(): ?View
    // {
    //     return view('filament.resources.sales.pages._sales-tabs', [
    //         'active' => 'list',
    //     ]);
    // }
    
    //  public static function getNavigationGroup(): ?string
    // {
    //     return 'Sales';
    // }

    //  public function getTabs(): array
    // {
    //     return [
    //         'list' => Tab::make('List')
    //             ->modifyQueryUsing(fn ($query) => $query), // default tabel

    //         'pipeline' => Tab::make('Pipeline')
    //             ->view('filament.resources.sales.pages.sales-pipeline'), // custom blade view pipeline
    //     ];
    // }

    

    
    
}
