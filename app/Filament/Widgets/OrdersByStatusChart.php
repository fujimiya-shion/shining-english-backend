<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Str;

class OrdersByStatusChart extends ChartWidget
{
    protected int|string|array $columnSpan = [
        'default' => 12,
        'md' => 6,
        'xl' => 4,
    ];

    protected string $color = 'warning';

    protected ?string $heading = 'Orders by Status';

    protected ?string $description = 'Current distribution of order states';

    protected ?string $maxHeight = '280px';

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $counts = Order::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->all();

        $labels = [];
        $data = [];

        foreach (OrderStatus::cases() as $status) {
            $labels[] = Str::of($status->value)->replace('_', ' ')->title()->toString();
            $data[] = (int) ($counts[$status->value] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Orders',
                    'data' => $data,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
