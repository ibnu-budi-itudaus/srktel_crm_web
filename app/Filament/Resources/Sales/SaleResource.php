<?php

namespace App\Filament\Resources\Sales;

use BackedEnum;
use App\Models\Sale;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use App\Filament\Resources\Sales\Pages;
use App\Filament\Resources\Sales\Pages\EditSale;
use App\Filament\Resources\Sales\Pages\ListSales;
use App\Filament\Resources\Sales\Pages\CreateSale;
use App\Filament\Resources\Sales\Schemas\SaleForm;
use App\Filament\Resources\Sales\Pages\KanbanBoard;
use App\Filament\Resources\Sales\Tables\SalesTable;
use App\Filament\Resources\Sales\Pages\SalesPipeline;
use App\Filament\Resources\Sales\Pages\SaleHistory;
use Filament\Navigation\NavigationItem;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use UnitEnum;

class SaleResource extends Resource
{
    protected static ?string $model = Sale::class;

    //protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    
    protected static ?string $pluralLabel = 'Deals';
    
    protected static ?string $modelLabel = 'Deal';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-check';
    protected static ?string $navigationLabel = 'Deals';
    
    protected static ?string $recordTitleAttribute = 'product';
    protected static ?int $navigationSort = 3;
    // protected static string | UnitEnum | null $navigationGroup = 'Sales';

    public static function form(Schema $schema): Schema
    {
        return SaleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SalesTable::configure($table);
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
            'index' => ListSales::route('/'),
            'create' => CreateSale::route('/create'),
            'edit' => EditSale::route('/{record}/edit'),
             // 👉 Tambahkan ini untuk pipeline page
            'pipeline' => SalesPipeline::route('/pipeline'),
            'history' => SaleHistory::route('/history'),
             
        ];
    }

//     public function panel(Panel $panel): Panel
// {
//     return $panel
//         // ...
//         ->navigationGroups([
//             NavigationGroup::make('Sales')
//                  ->label('Shop')
//                  ->icon('heroicon-o-shopping-cart'),
//             NavigationGroup::make('Sales')
//                 ->label('Blog')
//                 ->icon('heroicon-o-pencil'),
//             NavigationGroup::make('Sales')
//                 ->label(fn (): string => __('navigation.settings'))
//                 ->icon('heroicon-o-cog-6-tooth')
//                 ->collapsed(),
//         ]);
// }

// public static function getNavigationGroup(): ?string
// {
//     return 'Sales';
// }

// public static function getNavigationLabel(): string
// {
//     return 'List Sales'; // 👈 nama utama
// }  

// public static function getNavigationIcon(): string
// {
//     return 'heroicon-o-table-cells'; // icon list
// }


//    public static function getNavigationItems(): array
// {
//     return [
//         NavigationItem::make('List Sales')
//             ->url(static::getUrl('index'))
//             ->icon('heroicon-o-table-cells')
//             ->isActiveWhen(fn (): bool => request()->routeIs(static::getRouteBaseName() . '.index')),

//         NavigationItem::make('Sales Pipeline')
//             ->url(static::getUrl('pipeline'))
//             ->icon('heroicon-o-squares-2x2')
//             ->isActiveWhen(fn (): bool => request()->routeIs(static::getRouteBaseName() . '.pipeline')),
//     ];
// }



}
