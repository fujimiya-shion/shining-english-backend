<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class OrdersTrendChart extends ChartWidget
{
    protected int|string|array $columnSpan = [
        'default' => 12,
        'md' => 6,
        'xl' => 8,
    ];

    protected string $color = 'primary';

    protected ?string $heading = 'Orders Trend';

    protected ?string $description = 'Daily orders in the last 14 days';

    protected ?string $maxHeight = '280px';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        $data = $this->getCachedData();
        $values = $data['datasets'][0]['data'] ?? [0];
        $maxValue = max($values);

        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'min' => 0,
                    'suggestedMax' => $maxValue > 0 ? $maxValue : 1,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }

    protected function getData(): array
    {
        $series = $this->dateSeries(14);
        $start = Carbon::parse(array_key_first($series))->startOfDay();
        $end = Carbon::parse(array_key_last($series))->endOfDay();

        $rows = Order::query()
            ->whereBetween('placed_at', [$start, $end])
            ->selectRaw('DATE(placed_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date')
            ->all();

        foreach ($rows as $date => $total) {
            if (array_key_exists($date, $series)) {
                $series[$date] = (int) $total;
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Orders',
                    'data' => array_values($series),
                ],
            ],
            'labels' => $this->labelSeries(array_keys($series)),
        ];
    }

    /**
     * @return array<string, int>
     */
    private function dateSeries(int $days): array
    {
        $series = [];
        $cursor = now()->subDays($days - 1)->startOfDay();

        for ($i = 0; $i < $days; $i++) {
            $series[$cursor->toDateString()] = 0;
            $cursor->addDay();
        }

        return $series;
    }

    /**
     * @param  array<int, string>  $dates
     * @return array<int, string>
     */
    private function labelSeries(array $dates): array
    {
        return array_map(
            static fn (string $date): string => Carbon::parse($date)->format('d/m'),
            $dates,
        );
    }
}
