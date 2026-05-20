<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Services;

use App\Models\Generation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    public function getSummary(int $userId, string $range = '30d'): array
    {
        $from = $this->rangeToDate($range);

        $total = Generation::where('user_id', $userId)
            ->where('created_at', '>=', $from)
            ->count();

        $completed = Generation::where('user_id', $userId)
            ->where('status', Generation::STATUS_COMPLETED)
            ->where('created_at', '>=', $from)
            ->count();

        $failed = Generation::where('user_id', $userId)
            ->where('status', Generation::STATUS_FAILED)
            ->where('created_at', '>=', $from)
            ->count();

        return [
            'impressions'  => $total * 12,  // Simulated metric
            'clicks'       => $total * 3,
            'conversions'  => $completed,
            'period'       => $range,
            'generated'    => $total,
            'completedRate' => $total > 0 ? round($completed / $total * 100, 1) : 0,
        ];
    }

    /**
     * Return monthly usage series.
     *
     * @return array<array{month: string, usage: int}>
     */
    public function getUsageSeries(int $userId, string $range = '12m'): array
    {
        $months = $range === '12m' ? 12 : 6;
        $from   = Carbon::now()->subMonths($months - 1)->startOfMonth();

        $rows = DB::table('generations')
            ->selectRaw("TO_CHAR(created_at, 'YYYY-MM') as month, COUNT(*) as usage")
            ->where('user_id', $userId)
            ->where('created_at', '>=', $from)
            ->groupByRaw("TO_CHAR(created_at, 'YYYY-MM')")
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $series = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $key     = Carbon::now()->subMonths($i)->format('Y-m');
            $label   = Carbon::now()->subMonths($i)->format('M Y');
            $series[] = [
                'month' => $label,
                'usage' => isset($rows[$key]) ? (int) $rows[$key]->usage : 0,
            ];
        }

        return array_reverse($series);
    }

    private function rangeToDate(string $range): Carbon
    {
        return match($range) {
            '7d'  => Carbon::now()->subDays(7),
            '30d' => Carbon::now()->subDays(30),
            '90d' => Carbon::now()->subDays(90),
            '12m' => Carbon::now()->subMonths(12),
            default => Carbon::now()->subDays(30),
        };
    }
}
