<?php

namespace App\Livewire\Production\Admin;

use App\Models\ProductionBatch;
use App\Models\ProductionBatchDay;
use App\Models\ProductionMaterialBatch;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.production.admin')]
#[Title('Production Overview')]
class ProductionAdminDashboard extends Component
{
    public function render()
    {
        $today = now()->startOfDay();
        $lastSevenDaysStart = $today->copy()->subDays(6);

        $totalMaterialStock = (float) ProductionMaterialBatch::query()->sum('remaining_quantity');
        $totalProduced = (int) ProductionBatchDay::query()->sum('produced_qty');

        $thisMonthProduced = (int) ProductionBatchDay::query()
            ->whereBetween('work_date', [$today->copy()->startOfMonth(), $today->copy()->endOfMonth()])
            ->sum('produced_qty');

        $lastMonthProduced = (int) ProductionBatchDay::query()
            ->whereBetween('work_date', [
                $today->copy()->subMonthNoOverflow()->startOfMonth(),
                $today->copy()->subMonthNoOverflow()->endOfMonth(),
            ])
            ->sum('produced_qty');

        $producedChange = $lastMonthProduced > 0
            ? (($thisMonthProduced - $lastMonthProduced) / $lastMonthProduced) * 100
            : null;

        $thisMonthExpense = (float) ProductionBatchDay::query()
            ->whereBetween('work_date', [$today->copy()->startOfMonth(), $today->copy()->endOfMonth()])
            ->sum('expense_amount');

        $lastMonthExpense = (float) ProductionBatchDay::query()
            ->whereBetween('work_date', [
                $today->copy()->subMonthNoOverflow()->startOfMonth(),
                $today->copy()->subMonthNoOverflow()->endOfMonth(),
            ])
            ->sum('expense_amount');

        $expenseChange = $lastMonthExpense > 0
            ? (($thisMonthExpense - $lastMonthExpense) / $lastMonthExpense) * 100
            : null;

        $activeBatchCount = (int) ProductionBatch::query()->where('status', 'active')->count();
        $priorityBatchCount = (int) ProductionBatch::query()
            ->where('status', 'active')
            ->whereNotNull('end_date')
            ->whereBetween('end_date', [$today->toDateString(), $today->copy()->addDays(7)->toDateString()])
            ->count();

        $stats = [
            [
                'label' => 'Material Stock',
                'value' => number_format($totalMaterialStock, 0),
                'sub' => 'Units available in inventory',
                'trend' => null,
                'color' => '#fbbf24',
                'icon' => 'bi-box-seam',
            ],
            [
                'label' => 'Total Production',
                'value' => number_format($totalProduced),
                'sub' => $this->formatTrendText($producedChange, 'vs last month'),
                'trend' => null,
                'color' => '#3b82f6',
                'icon' => 'bi-lightning-charge',
            ],
            [
                'label' => 'Expenses',
                'value' =>  number_format($thisMonthExpense, 2),
                'sub' => $this->formatTrendText($expenseChange, 'this month vs last month'),
                'trend' => null,
                'color' => '#ef4444',
                'icon' => 'bi-wallet2',
            ],
            [
                'label' => 'Upcoming Orders',
                'value' => number_format($activeBatchCount),
                'sub' => $priorityBatchCount . ' priority ending in 7 days',
                'trend' => null,
                'color' => '#10b981',
                'icon' => 'bi-truck',
            ],
        ];

        $dailyRows = ProductionBatchDay::query()
            ->selectRaw('DATE(work_date) as work_day, SUM(produced_qty) as produced_total')
            ->whereBetween('work_date', [$lastSevenDaysStart->toDateString(), $today->toDateString()])
            ->groupBy(DB::raw('DATE(work_date)'))
            ->orderBy('work_day')
            ->get()
            ->keyBy('work_day');

        $dailyLabels = [];
        $dailyValues = [];
        for ($offset = 0; $offset < 7; $offset++) {
            $day = $lastSevenDaysStart->copy()->addDays($offset);
            $key = $day->toDateString();

            $dailyLabels[] = $day->format('D');
            $dailyValues[] = (int) ($dailyRows[$key]->produced_total ?? 0);
        }

        $maxDailyValue = max($dailyValues ?: [0]);
        $dailyCapacity = max(100, (int) (ceil(($maxDailyValue ?: 1) / 50) * 50));

        $recentProductions = ProductionBatchDay::query()
            ->with('batch:id,batch_code,status')
            ->latest('work_date')
            ->latest('id')
            ->limit(5)
            ->get()
            ->map(function (ProductionBatchDay $day) {
                $isCompleted = $day->batch?->status === 'completed';
                $statusColor = $isCompleted ? '#10b981' : '#3b82f6';
                $activity = $isCompleted ? 'Completed' : 'In progress';

                if ((int) $day->produced_qty <= 0) {
                    $statusColor = '#ef4444';
                    $activity = 'No output logged';
                } else {
                    $activity = 'Produced ' . number_format((int) $day->produced_qty) . ' units';
                }

                return [
                    'unit' => $day->batch?->batch_code ?? ('DAY #' . $day->day_no),
                    'activity' => $activity,
                    'time' => Carbon::parse($day->work_date)->format('d M') . ' · ' . $day->created_at?->format('h:i A'),
                    'status_color' => $statusColor,
                ];
            })
            ->toArray();

        $monthlyStart = $today->copy()->startOfMonth()->subMonths(3);

        $monthlyRows = ProductionBatchDay::query()
            ->selectRaw('YEAR(work_date) as year_num, MONTH(work_date) as month_num, SUM(produced_qty) as produced_total, SUM(expense_amount) as expense_total')
            ->whereBetween('work_date', [$monthlyStart->toDateString(), $today->copy()->endOfMonth()->toDateString()])
            ->groupBy('year_num', 'month_num')
            ->orderBy('year_num')
            ->orderBy('month_num')
            ->get()
            ->mapWithKeys(function ($row) {
                $key = sprintf('%04d-%02d', (int) $row->year_num, (int) $row->month_num);

                return [$key => $row];
            });

        $monthlyProducedMap = [];
        for ($offset = 0; $offset < 4; $offset++) {
            $month = $monthlyStart->copy()->addMonths($offset);
            $key = $month->format('Y-m');
            $monthlyProducedMap[$key] = (int) ($monthlyRows[$key]->produced_total ?? 0);
        }

        $peakKey = !empty($monthlyProducedMap)
            ? array_keys($monthlyProducedMap, max($monthlyProducedMap), true)[0]
            : null;

        $maxProduced = max($monthlyProducedMap ?: [0]);
        $maxExpense = max($monthlyRows->pluck('expense_total')->map(fn($value) => (float) $value)->all() ?: [0]);

        $monthlyStats = [];
        for ($offset = 0; $offset < 4; $offset++) {
            $month = $monthlyStart->copy()->addMonths($offset);
            $key = $month->format('Y-m');
            $produced = (int) ($monthlyRows[$key]->produced_total ?? 0);
            $expense = (float) ($monthlyRows[$key]->expense_total ?? 0);

            $producedWidth = $maxProduced > 0 ? (int) round(($produced / $maxProduced) * 100) : 0;
            $expenseWidthRaw = $maxExpense > 0 ? (int) round(($expense / $maxExpense) * 35) : 0;
            $expenseWidth = min(max(0, 100 - $producedWidth), $expenseWidthRaw);

            $monthlyStats[] = [
                'month' => $month->format('F'),
                'is_peak' => $key === $peakKey && $produced > 0,
                'produced' => $produced,
                'expense' => $expense,
                'produced_width' => $producedWidth,
                'expense_width' => $expenseWidth,
            ];
        }

        return view('livewire.production.admin.production-admin-dashboard', compact(
            'stats',
            'dailyLabels',
            'dailyValues',
            'dailyCapacity',
            'recentProductions',
            'monthlyStats'
        ));
    }

    private function formatTrendText(?float $change, string $suffix): string
    {
        if ($change === null) {
            return 'No previous month data';
        }

        if ($change > 0) {
            return '+' . number_format($change, 1) . '% ' . $suffix;
        }

        if ($change < 0) {
            return number_format($change, 1) . '% ' . $suffix;
        }

        return 'No change ' . $suffix;
    }
}
