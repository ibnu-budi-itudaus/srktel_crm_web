<?php

namespace App\Filament\Widgets;

use App\Models\FollowUp;
use Filament\Widgets\ChartWidget;

class FollowUpChart extends ChartWidget
{
    protected ?string $heading = 'Follow Ups per Month';

    protected int|string|array $columnSpan = 1;

    protected function getData(): array
    {
        $followUps = FollowUp::selectRaw('MONTH(follow_up_date) as month, COUNT(*) as total')
            ->groupBy('month')
            ->pluck('total', 'month')
            ->all();

        return [
            'datasets' => [
                [
                    'label' => 'Follow Ups',
                    'data' => array_values($followUps),
                ],
            ],
            'labels' => array_map(
                fn ($m) => date('F', mktime(0, 0, 0, $m, 10)),
                array_keys($followUps)
            ),
        ];
    }

    protected function getType(): string
    {
        return 'line'; // Bisa diganti 'bar', 'pie', 'doughnut', dll.
    }
}
