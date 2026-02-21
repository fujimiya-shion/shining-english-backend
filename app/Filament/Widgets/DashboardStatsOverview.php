<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Models\Course;
use App\Models\Order;
use App\Models\User;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class DashboardStatsOverview extends StatsOverviewWidget
{
    protected int|string|array $columnSpan = 'full';

    protected ?string $heading = 'Quick Stats';

    protected ?string $description = 'Snapshot of the last 30 days';

    protected function getStats(): array
    {
        $now = now();
        $currentStart = $now->copy()->subDays(29)->startOfDay();
        $previousStart = $currentStart->copy()->subDays(30);
        $previousEnd = $currentStart->copy()->subSecond();

        $newUsers = User::query()
            ->whereBetween('created_at', [$currentStart, $now])
            ->count();
        $totalUsers = User::query()->count();

        $activeCourses = Course::query()->where('status', true)->count();
        $totalCourses = Course::query()->count();

        $ordersCurrent = Order::query()
            ->whereBetween('placed_at', [$currentStart, $now])
            ->count();
        $ordersPrevious = Order::query()
            ->whereBetween('placed_at', [$previousStart, $previousEnd])
            ->count();

        $revenueCurrent = Order::query()
            ->where('status', OrderStatus::Paid)
            ->whereBetween('placed_at', [$currentStart, $now])
            ->sum('total_amount');
        $revenuePrevious = Order::query()
            ->where('status', OrderStatus::Paid)
            ->whereBetween('placed_at', [$previousStart, $previousEnd])
            ->sum('total_amount');

        [$ordersDeltaLabel, $ordersDeltaIcon, $ordersDeltaColor] = $this->trendLabel(
            $ordersCurrent,
            $ordersPrevious,
        );
        [$revenueDeltaLabel, $revenueDeltaIcon, $revenueDeltaColor] = $this->trendLabel(
            $revenueCurrent,
            $revenuePrevious,
            suffix: 'VND',
        );

        return [
            Stat::make('Total Users', number_format($totalUsers))
                ->description($this->pluralize($newUsers, 'new user').' in 30 days')
                ->descriptionIcon(Heroicon::ArrowTrendingUp)
                ->color($newUsers > 0 ? 'success' : 'gray')
                ->icon(Heroicon::Users)
                ->chart($this->countSparkline(User::query(), 'created_at')),
            Stat::make('Active Courses', number_format($activeCourses))
                ->description(number_format($totalCourses).' total courses')
                ->descriptionIcon(Heroicon::BookOpen)
                ->color('primary')
                ->icon(Heroicon::AcademicCap),
            Stat::make('Orders (30d)', number_format($ordersCurrent))
                ->description($ordersDeltaLabel)
                ->descriptionIcon($ordersDeltaIcon)
                ->color($ordersDeltaColor)
                ->icon(Heroicon::ShoppingBag)
                ->chart($this->countSparkline(Order::query(), 'placed_at')),
            Stat::make('Revenue (30d)', number_format($revenueCurrent).' VND')
                ->description($revenueDeltaLabel)
                ->descriptionIcon($revenueDeltaIcon)
                ->color($revenueDeltaColor)
                ->icon(Heroicon::Banknotes)
                ->chart($this->sumSparkline(
                    Order::query()->where('status', OrderStatus::Paid),
                    'placed_at',
                    'total_amount',
                )),
        ];
    }

    /**
     * @return array{0: string, 1: Heroicon, 2: string}
     */
    private function trendLabel(int $current, int $previous, string $suffix = ''): array
    {
        $delta = $current - $previous;
        $formattedDelta = number_format(abs($delta));

        if ($suffix !== '') {
            $formattedDelta .= ' '.$suffix;
        }

        if ($delta === 0) {
            return ['No change vs last 30 days', Heroicon::ArrowRight, 'gray'];
        }

        $direction = $delta > 0 ? 'up' : 'down';
        $label = $delta > 0
            ? "{$formattedDelta} up vs last 30 days"
            : "{$formattedDelta} down vs last 30 days";

        return [
            $label,
            $direction === 'up' ? Heroicon::ArrowTrendingUp : Heroicon::ArrowTrendingDown,
            $direction === 'up' ? 'success' : 'danger',
        ];
    }

    private function pluralize(int $count, string $singular): string
    {
        $label = $count === 1 ? $singular : "{$singular}s";

        return number_format($count).' '.$label;
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>  $query
     * @return array<int>
     */
    private function countSparkline($query, string $column): array
    {
        $start = now()->subDays(6)->startOfDay();
        $end = now()->endOfDay();
        $series = $this->dateSeries($start, $end);

        $rows = $query
            ->whereBetween($column, [$start, $end])
            ->selectRaw("DATE({$column}) as date, COUNT(*) as total")
            ->groupBy('date')
            ->pluck('total', 'date')
            ->all();

        foreach ($rows as $date => $total) {
            if (array_key_exists($date, $series)) {
                $series[$date] = (int) $total;
            }
        }

        return array_values($series);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>  $query
     * @return array<int>
     */
    private function sumSparkline($query, string $column, string $sumColumn): array
    {
        $start = now()->subDays(6)->startOfDay();
        $end = now()->endOfDay();
        $series = $this->dateSeries($start, $end);

        $rows = $query
            ->whereBetween($column, [$start, $end])
            ->selectRaw("DATE({$column}) as date, SUM({$sumColumn}) as total")
            ->groupBy('date')
            ->pluck('total', 'date')
            ->all();

        foreach ($rows as $date => $total) {
            if (array_key_exists($date, $series)) {
                $series[$date] = (int) $total;
            }
        }

        return array_values($series);
    }

    /**
     * @return array<string, int>
     */
    private function dateSeries(Carbon $start, Carbon $end): array
    {
        $series = [];
        $cursor = $start->copy();

        while ($cursor->lessThanOrEqualTo($end)) {
            $series[$cursor->toDateString()] = 0;
            $cursor->addDay();
        }

        return $series;
    }
}
