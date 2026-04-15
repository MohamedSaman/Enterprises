<?php

namespace App\Livewire\Production\Staff;

use App\Models\ProductionBatch;
use App\Models\ProductionBatchDay;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.production.staff')]
#[Title('Supervisor Dashboard')]
class ProductionStaffDashboard extends Component
{
    public function render()
    {
        $supervisorId = (int) Auth::id();

        $today = now()->startOfDay();
        $monthStart = $today->copy()->startOfMonth();
        $lastMonthStart = $today->copy()->subMonthNoOverflow()->startOfMonth();
        $lastMonthEnd = $today->copy()->subMonthNoOverflow()->endOfMonth();
        $lastSevenDaysStart = $today->copy()->subDays(6);

        $assignedBatches = ProductionBatch::query()
            ->where('supervisor_id', $supervisorId)
            ->count();

        $activeBatches = ProductionBatch::query()
            ->where('supervisor_id', $supervisorId)
            ->where('status', 'active')
            ->count();

        $todayProduced = (int) ProductionBatchDay::query()
            ->whereDate('work_date', now()->toDateString())
            ->whereHas('batch', fn($q) => $q->where('supervisor_id', $supervisorId))
            ->sum('produced_qty');

        $todayExpense = (float) ProductionBatchDay::query()
            ->whereDate('work_date', now()->toDateString())
            ->whereHas('batch', fn($q) => $q->where('supervisor_id', $supervisorId))
            ->sum('expense_amount');

        $lastMonthProduced = (int) ProductionBatchDay::query()
            ->whereBetween('work_date', [$lastMonthStart->toDateString(), $lastMonthEnd->toDateString()])
            ->whereHas('batch', fn($q) => $q->where('supervisor_id', $supervisorId))
            ->sum('produced_qty');

        $currentMonthProduced = (int) ProductionBatchDay::query()
            ->whereBetween('work_date', [$monthStart->toDateString(), $today->toDateString()])
            ->whereHas('batch', fn($q) => $q->where('supervisor_id', $supervisorId))
            ->sum('produced_qty');

        $currentMonthTarget = (int) ProductionBatch::query()
            ->where('supervisor_id', $supervisorId)
            ->where('status', 'active')
            ->sum('target_qty');

        $completionPercent = $currentMonthTarget > 0
            ? (int) round(min(100, ($currentMonthProduced / $currentMonthTarget) * 100))
            : 0;

        $pendingItems = max($currentMonthTarget - $currentMonthProduced, 0);

        $chartRows = ProductionBatchDay::query()
            ->selectRaw('DATE(work_date) as day_key, SUM(produced_qty) as produced_total')
            ->whereBetween('work_date', [$lastSevenDaysStart->toDateString(), $today->toDateString()])
            ->whereHas('batch', fn($q) => $q->where('supervisor_id', $supervisorId))
            ->groupBy('day_key')
            ->orderBy('day_key')
            ->get()
            ->keyBy('day_key');

        $chartLabels = [];
        $chartValues = [];
        for ($offset = 0; $offset < 7; $offset++) {
            $day = $lastSevenDaysStart->copy()->addDays($offset);
            $key = $day->toDateString();
            $chartLabels[] = $day->format('D');
            $chartValues[] = (int) ($chartRows[$key]->produced_total ?? 0);
        }

        $recentProductionItems = ProductionBatchDay::query()
            ->with(['batch:id,batch_code,size,target_qty,completed_qty,status'])
            ->whereHas('batch', fn($q) => $q->where('supervisor_id', $supervisorId))
            ->latest('work_date')
            ->latest('id')
            ->limit(7)
            ->get()
            ->map(function (ProductionBatchDay $day) {
                return [
                    'batch_code' => $day->batch?->batch_code ?? '-',
                    'size' => $day->batch?->size ?? '-',
                    'day_no' => $day->day_no,
                    'work_date' => Carbon::parse($day->work_date)->format('d M Y'),
                    'produced_qty' => (int) $day->produced_qty,
                    'expense_amount' => (float) $day->expense_amount,
                    'note' => $day->expense_note ?: 'Daily production log',
                ];
            })
            ->toArray();

        return view('livewire.production.staff.production-staff-dashboard', [
            'lastMonthProduced' => $lastMonthProduced,
            'currentMonthTarget' => $currentMonthTarget,
            'currentMonthProduced' => $currentMonthProduced,
            'completionPercent' => $completionPercent,
            'pendingItems' => $pendingItems,
            'chartLabels' => $chartLabels,
            'chartValues' => $chartValues,
            'recentProductionItems' => $recentProductionItems,
        ]);
    }
}
