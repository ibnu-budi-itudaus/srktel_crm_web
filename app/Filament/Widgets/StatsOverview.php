<?php

namespace App\Filament\Widgets;

use App\Models\Sale;
use App\Models\Customer;
use App\Models\FollowUp;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Customers', Customer::count())
            ->description('Jumlah semua pelanggan')
            ->descriptionIcon('heroicon-m-user-group')
            ->color('success'),

            Stat::make('Total Sales (Deal)', Sale::whereHas('customer', fn($q) => $q->where('status', 'deal'))->count())
                ->description('Jumlah penjualan sukses')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('info'),

            Stat::make('Follow Ups', FollowUp::count())
                ->description('Aktivitas follow up')
                ->descriptionIcon('heroicon-m-phone')
                ->color('warning'),

        ];
    }
}
