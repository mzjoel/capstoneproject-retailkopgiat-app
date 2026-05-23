<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Modules\Transactions\Models\Transaction;
use App\Modules\Catalog\Models\Product;
use App\Modules\Catalog\Models\Category;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {

        $availableCount    = Product::where('is_available', true)->count();
        $unavailableCount  = Product::where('is_available', false)->count();
        $todayCount     = Transaction::whereDate('created_at', today())->count();
        $yesterdayCount = Transaction::whereDate('created_at', today()->subDay())->count();
        $txChange = $yesterdayCount > 0
            ? round((($todayCount - $yesterdayCount) / $yesterdayCount) * 100, 1)
            : ($todayCount > 0 ? 100 : 0);

        $txDescription = $yesterdayCount > 0
            ? ($txChange >= 0 ? "+{$txChange}% dari kemarin" : "{$txChange}% dari kemarin")
            : 'Tidak ada data kemarin';

        $todayRevenue     = Transaction::whereDate('created_at', today())
            ->where('status', 'completed')->sum('grand_total');

        $yesterdayRevenue = Transaction::whereDate('created_at', today()->subDay())
            ->where('status', 'completed')->sum('grand_total');

        $revenueChange = $yesterdayRevenue > 0
            ? round((($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100, 1)
            : ($todayRevenue > 0 ? 100 : 0);

        $pendingCount = Transaction::where('status', 'pending')->count();



        $todayRevenue = Transaction::whereDate('created_at', today())
            ->where('status', 'completed')
            ->sum('grand_total');

         $yesterdayRevenue = Transaction::whereDate('created_at', today()->subDay())
            ->where('status', 'completed')
            ->sum('grand_total');

        $revenueChange = $yesterdayRevenue > 0
            ? round((($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100, 1)
            : 0;

        return [
             Stat::make('Produk Tersedia', $availableCount)
                ->description("{$unavailableCount} produk tidak tersedia")
                ->descriptionIcon('heroicon-m-cube')
                ->color('success'),
 
            Stat::make('Transaksi Hari Ini', $todayCount)
                ->description($txDescription)
                ->descriptionIcon(
                    $txChange >= 0
                        ? 'heroicon-m-arrow-trending-up'
                        : 'heroicon-m-arrow-trending-down'
                )
                ->color($txChange >= 0 ? 'success' : 'danger'),
 
            Stat::make('Pending', $pendingCount)
                ->description('Transaksi menunggu proses')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
 
            Stat::make('Pendapatan Hari Ini', 'Rp ' . number_format($todayRevenue, 0, ',', '.'))
                ->description(
                    $revenueChange >= 0
                        ? "+{$revenueChange}% dari kemarin"
                        : "{$revenueChange}% dari kemarin"
                )
                ->descriptionIcon(
                    $revenueChange >= 0
                        ? 'heroicon-m-arrow-trending-up'
                        : 'heroicon-m-arrow-trending-down'
                )
                ->color($revenueChange >= 0 ? 'success' : 'danger'),

        ];
    }
}
