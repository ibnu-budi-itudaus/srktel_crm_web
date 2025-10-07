<?php

namespace App\Filament\Widgets;

use App\Models\Sale;
use Filament\Widgets\ChartWidget;

class SalesChart extends ChartWidget
{
    protected ?string $heading = 'Sales per Month';

    protected int|string|array $columnSpan = 1;

    protected function getData(): array
    {
        $sales = Sale::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->groupBy('month')
            ->pluck('total', 'month')
            ->all();

        return [
            'datasets' => [
                [
                    'label' => 'Sales',
                    'data' => array_values($sales),
                ],
            ],
            'labels' => array_map(fn ($m) => date('F', mktime(0, 0, 0, $m, 10)), array_keys($sales)),
        ];
    }

    protected function getType(): string
    {
        return 'bar'; // Bisa 'line', 'bar', 'pie', dll.
    }
}
