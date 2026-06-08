<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Modules\Transactions\Models\Transaction;
use Illuminate\Support\Carbon;

class TransactionChartWidget extends ChartWidget
{
    protected  ?string $heading = 'Grafik Transaksi';
    protected  static ?int $sort = 2;
    protected  ?string $maxHeight = '300px';
    protected int|string|array $columnSpan = 'full';

    public ?string $filter = '7d';

    protected function getFilters(): ?array
    {
        return [
            '1d' => 'Hari Ini',
            '3d' => '3 Hari',
            '7d' => '7 Hari',
            '1m' => '1 Bulan',
        ];
    }

    protected function getData(): array
    {
        [$labels, $revenues, $counts] = $this->buildChartData();

        return [
            'datasets' => [
                [
                    'label'                => 'Pendapatan (Rp)',
                    'data'                 => $revenues,
                    'backgroundColor'      => 'rgba(16, 185, 129, 0.15)',
                    'borderColor'          => 'rgb(16, 185, 129)',
                    'borderWidth'          => 2,
                    'fill'                 => true,
                    'tension'              => 0.4,
                    'yAxisID'              => 'y',
                    'pointBackgroundColor' => 'rgb(16, 185, 129)',
                    'pointRadius'          => 4,
                ],
                [
                    'label'                => 'Jumlah Transaksi',
                    'data'                 => $counts,
                    'backgroundColor'      => 'rgba(245, 158, 11, 0.15)',
                    'borderColor'          => 'rgb(245, 158, 11)',
                    'borderWidth'          => 2,
                    'fill'                 => false,
                    'tension'              => 0.4,
                    'yAxisID'              => 'y1',
                    'pointBackgroundColor' => 'rgb(245, 158, 11)',
                    'pointRadius'          => 4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'interaction' => [
                'mode'      => 'index',
                'intersect' => false,
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                ],
                'tooltip' => [
                    'callbacks' => [
                        // Format tooltip pendapatan dengan prefix Rp
                    ],
                ],
            ],
            'scales' => [
                'y' => [
                    'type'     => 'linear',
                    'display'  => true,
                    'position' => 'left',
                    'ticks'    => [
                        'callback' => 'function(value){ return "Rp " + value.toLocaleString("id-ID"); }',
                    ],
                    'grid' => ['color' => 'rgba(0,0,0,0.05)'],
                ],
                'y1' => [
                    'type'     => 'linear',
                    'display'  => true,
                    'position' => 'right',
                    'grid'     => ['drawOnChartArea' => false],
                ],
            ],
        ];
    }

    // -------------------------------------------------------------------------

    private function buildChartData(): array
    {
        $labels   = [];
        $revenues = [];
        $counts   = [];

        match ($this->filter) {
            '1d' => $this->buildHourly($labels, $revenues, $counts),
            '3d' => $this->buildDaily($labels, $revenues, $counts, 3),
            '7d' => $this->buildDaily($labels, $revenues, $counts, 7),
            '1m' => $this->buildDaily($labels, $revenues, $counts, 30),
        };

        return [$labels, $revenues, $counts];
    }

    /** Per jam untuk filter 1 hari */
    private function buildHourly(array &$labels, array &$revenues, array &$counts): void
    {
        $rows = Transaction::selectRaw('HOUR(created_at) as hour, SUM(grand_total) as total, COUNT(*) as cnt')
            ->whereDate('created_at', today())
            ->where('status', 'completed')
            ->groupByRaw('HOUR(created_at)')
            ->orderByRaw('HOUR(created_at)')
            ->get()
            ->keyBy('hour');

        for ($h = 0; $h < 24; $h++) {
            $labels[]   = sprintf('%02d:00', $h);
            $revenues[] = (float) ($rows[$h]->total ?? 0);
            $counts[]   = (int)   ($rows[$h]->cnt   ?? 0);
        }
    }

    /** Per hari untuk filter 3d / 7d / 1m */
    private function buildDaily(array &$labels, array &$revenues, array &$counts, int $days): void
    {
        $start = today()->subDays($days - 1);

        $rows = Transaction::selectRaw('DATE(created_at) as date, SUM(grand_total) as total, COUNT(*) as cnt')
            ->where('created_at', '>=', $start)
            ->where('status', 'completed')
            ->groupByRaw('DATE(created_at)')
            ->orderByRaw('DATE(created_at)')
            ->get()
            ->keyBy('date');

        for ($i = 0; $i < $days; $i++) {
            $date       = $start->copy()->addDays($i)->toDateString();
            $labels[]   = Carbon::parse($date)->translatedFormat('d M');
            $revenues[] = (float) ($rows[$date]->total ?? 0);
            $counts[]   = (int)   ($rows[$date]->cnt   ?? 0);
        }
    }
}