<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\TransactionChartWidget;
use App\Filament\Widgets\FavoriteProductsWidget;
use BackedEnum;

class Dashboard extends BaseDashboard
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-home';
    protected static ?string $title = 'Dashboard Kasir';

    public function getWidgets(): array
    {
        return [
            StatsOverview::class,
            TransactionChartWidget::class,
            FavoriteProductsWidget::class,
        ];
    }

    public function getColumns(): array|int
    {
        return 2;
    }
}