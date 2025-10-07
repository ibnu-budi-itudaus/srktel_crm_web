<?php

namespace App\Filament\Resources\Sales\Pages;


use App\Filament\Resources\Sales\SaleResource;
use BackedEnum;
use Filament\Resources\Pages\Page;

class DealsPage extends Page
{
     protected static string $resource = SaleResource::class;
     protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-check';
    protected string $view = 'filament.resources.sales.pages.deals-page';
    protected static ?string $title = 'Deals';

    // public function mount(): void
    // {
    //    // Redirect otomatis ke view=list jika query tidak ada
    //     if (! request()->has('view')) {
    //         redirect(static::getUrl() . '?view=list')->send();
    //         exit;
    //      }
    // }

    // public function getBreadcrumbs(): array
    // {
    //     return [
    //         static::getUrl(['view' => 'list']) => 'List',
    //         static::getUrl(['view' => 'pipeline']) => 'Pipeline',
    //     ];
    // }
}
