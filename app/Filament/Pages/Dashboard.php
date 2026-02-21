<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Admin Dashboard';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ChartBarSquare;

    public function getColumns(): int|array
    {
        return [
            'default' => 1,
            'md' => 6,
            'xl' => 12,
        ];
    }
}
