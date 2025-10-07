<?php

namespace App\Filament\Resources\Sales\Pages;

use App\Models\Sale;
use Filament\Tables;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Sales\SaleResource;
use Illuminate\Database\Eloquent\Builder;

class SaleHistory extends ListRecords
{
    protected static string $resource = SaleResource::class;

     public function getTitle(): string
    {
        return 'Riwayat Arsip Sales';
    }

     public function getBreadcrumb(): string
    {
        return 'Riwayat Arsip'; // breadcrumb custom
    }


     public function table(Tables\Table $table): Tables\Table
    {

        return $table
        ->query(Sale::query()->archived())
        ->columns([
            Tables\Columns\TextColumn::make('id'),
            Tables\Columns\TextColumn::make('customer.name')->label('Customer'),
            Tables\Columns\TextColumn::make('status')->badge(),
            Tables\Columns\TextColumn::make('price')->money('idr'),
            Tables\Columns\TextColumn::make('archived_at')->date(),
        ])
        ->filters([
            // filter tambahan kalau mau
        ]);

    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('back')
                ->label('Kembali ke Sales')
                ->icon('heroicon-m-arrow-left')
                ->url(static::getResource()::getUrl('index')),
        ];
    }

    //  protected function getTableQuery(): Builder
    // {
    //     // hanya tampilkan data yang sudah diarsipkan
    //     return parent::getTableQuery()
    //         ->whereNotNull('archived_at');
    // }

    

    public static function getNavigationLabel(): string
    {
        return 'Riwayat Sales';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-archive-box';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Sales';
    }
}
