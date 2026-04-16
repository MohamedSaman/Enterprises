<?php

namespace App\Livewire\Production\Staff;

use App\Models\ProductionBatch;
use App\Models\ProductionBatchDay;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.production.staff')]
#[Title('Batch Daily Logs')]
class ProductionStaffBatchDetails extends Component
{
    public ProductionBatch $batch;

    public bool $showDayModal = false;
    public bool $showViewModal = false;
    public bool $showDeleteModal = false;

    public $day_no = null;
    public string $work_date = '';
    public $produced_qty = 0;
    public $produced_s_qty = 0;
    public $produced_m_qty = 0;
    public $produced_l_qty = 0;
    public $expense_amount = 0;
    public string $expense_note = '';
    public array $expense_rows = [];
    public ?int $editingDayId = null;
    public ?ProductionBatchDay $viewDay = null;
    public ?ProductionBatchDay $dayToDelete = null;

    public function mount($batchId): void
    {
        $this->loadBatch((int) $batchId);
        $this->work_date = now()->format('Y-m-d');
    }

    private function loadBatch(int $batchId): void
    {
        $this->batch = ProductionBatch::query()
            ->with(['supervisor', 'staffMembers', 'days'])
            ->where('supervisor_id', (int) Auth::id())
            ->findOrFail($batchId);
    }

    public function openDayModal(): void
    {
        $this->resetDayForm();
        $this->day_no = ((int) $this->batch->days()->max('day_no')) + 1;
        $this->work_date = now()->format('Y-m-d');
        $this->expense_rows = [];
        $this->produced_s_qty = 0;
        $this->produced_m_qty = 0;
        $this->produced_l_qty = 0;
        $this->produced_qty = 0;
        $this->showDayModal = true;
    }

    public function openEditModal(int $dayId): void
    {
        $day = $this->batch->days()->findOrFail($dayId);

        $this->editingDayId = $day->id;
        $this->day_no = $day->day_no;
        $this->work_date = optional($day->work_date)->format('Y-m-d') ?? now()->format('Y-m-d');
        $this->produced_s_qty = (int) ($day->produced_s_qty ?? 0);
        $this->produced_m_qty = (int) ($day->produced_m_qty ?? 0);
        $this->produced_l_qty = (int) ($day->produced_l_qty ?? 0);
        $this->produced_qty = (int) ($day->produced_qty ?? 0);
        $this->expense_amount = (float) $day->expense_amount;
        $this->expense_note = (string) ($day->expense_note ?? '');
        $this->expense_rows = !empty($day->expense_items)
            ? array_values($day->expense_items)
            : [['label' => 'Expense', 'amount' => $this->expense_amount, 'note' => $this->expense_note]];
        $this->showDayModal = true;
    }

    public function openViewModal(int $dayId): void
    {
        $this->viewDay = $this->batch->days()->findOrFail($dayId);
        $this->showViewModal = true;
    }

    public function closeViewModal(): void
    {
        $this->showViewModal = false;
        $this->viewDay = null;
    }

    public function confirmDeleteDay(int $dayId): void
    {
        $this->dayToDelete = $this->batch->days()->findOrFail($dayId);
        $this->showDeleteModal = true;
    }

    public function cancelDeleteDay(): void
    {
        $this->showDeleteModal = false;
        $this->dayToDelete = null;
    }

    public function deleteDay(): void
    {
        if (!$this->dayToDelete) {
            $this->cancelDeleteDay();
            return;
        }

        $this->dayToDelete->delete();
        $this->cancelDeleteDay();
        $this->loadBatch($this->batch->id);
        $this->dispatch('alert', ['message' => 'Daily log deleted successfully.', 'type' => 'success']);
    }

    public function addExpenseRow(): void
    {
        $this->expense_rows[] = ['label' => '', 'amount' => 0, 'note' => ''];
    }

    public function removeExpenseRow(int $index): void
    {
        if (isset($this->expense_rows[$index])) {
            unset($this->expense_rows[$index]);
            $this->expense_rows = array_values($this->expense_rows);
        }
    }

    public function updatedProducedSQty(): void
    {
        $this->syncProducedTotal();
    }

    public function updatedProducedMQty(): void
    {
        $this->syncProducedTotal();
    }

    public function updatedProducedLQty(): void
    {
        $this->syncProducedTotal();
    }

    private function syncProducedTotal(): void
    {
        $this->produced_qty = (int) $this->produced_s_qty + (int) $this->produced_m_qty + (int) $this->produced_l_qty;
    }

    private function resetDayForm(): void
    {
        $this->editingDayId = null;
        $this->day_no = null;
        $this->work_date = '';
        $this->produced_qty = 0;
        $this->produced_s_qty = 0;
        $this->produced_m_qty = 0;
        $this->produced_l_qty = 0;
        $this->expense_amount = 0;
        $this->expense_note = '';
        $this->expense_rows = [];
    }

    public function closeDayModal(): void
    {
        $this->showDayModal = false;
        $this->editingDayId = null;
    }

    public function saveDayLog(): void
    {
        $normalizedExpenseRows = collect($this->expense_rows)
            ->filter(fn($row) => trim((string) ($row['label'] ?? '')) !== '' || (float) ($row['amount'] ?? 0) > 0 || trim((string) ($row['note'] ?? '')) !== '')
            ->map(function ($row) {
                return [
                    'label' => trim((string) ($row['label'] ?? 'Expense')),
                    'amount' => (float) ($row['amount'] ?? 0),
                    'note' => trim((string) ($row['note'] ?? '')),
                ];
            })
            ->values()
            ->toArray();

        if (empty($normalizedExpenseRows)) {
            $normalizedExpenseRows = [[
                'label' => 'Expense',
                'amount' => (float) $this->expense_amount,
                'note' => $this->expense_note,
            ]];
        }

        $expenseTotal = (float) collect($normalizedExpenseRows)->sum('amount');

        $this->validate([
            'day_no' => 'required|integer|min:1',
            'work_date' => 'required|date',
            'produced_qty' => 'required|integer|min:0',
            'produced_s_qty' => 'required|integer|min:0',
            'produced_m_qty' => 'required|integer|min:0',
            'produced_l_qty' => 'required|integer|min:0',
            'expense_amount' => 'nullable|numeric|min:0',
            'expense_note' => 'nullable|string|max:1000',
            'expense_rows' => 'nullable|array',
            'expense_rows.*.label' => 'nullable|string|max:120',
            'expense_rows.*.amount' => 'nullable|numeric|min:0',
            'expense_rows.*.note' => 'nullable|string|max:255',
        ]);

        $existingDay = ProductionBatchDay::query()
            ->where('production_batch_id', $this->batch->id)
            ->whereDate('work_date', $this->work_date)
            ->when(!$this->editingDayId, fn($query) => $query)
            ->when($this->editingDayId, fn($query) => $query->where('id', '!=', $this->editingDayId))
            ->first();

        if ($existingDay) {
            $this->addError('work_date', 'Only one daily log is allowed for a particular date.');
            return;
        }

        DB::transaction(function () use ($expenseTotal, $normalizedExpenseRows) {
            ProductionBatchDay::updateOrCreate(
                [
                    'production_batch_id' => $this->batch->id,
                    'day_no' => (int) $this->day_no,
                ],
                [
                    'production_batch_id' => $this->batch->id,
                    'day_no' => (int) $this->day_no,
                    'work_date' => $this->work_date,
                    'produced_qty' => (int) $this->produced_qty,
                    'produced_s_qty' => (int) $this->produced_s_qty,
                    'produced_m_qty' => (int) $this->produced_m_qty,
                    'produced_l_qty' => (int) $this->produced_l_qty,
                    'expense_amount' => $expenseTotal,
                    'expense_note' => $this->expense_note ?: null,
                    'expense_items' => $normalizedExpenseRows,
                    'recorded_by' => (int) Auth::id(),
                ]
            );

            $this->batch->completed_qty = (int) $this->batch->days()->sum('produced_qty');

            if ((int) $this->batch->target_qty > 0 && (int) $this->batch->completed_qty >= (int) $this->batch->target_qty) {
                $this->batch->status = 'completed';
                $this->batch->end_date = $this->batch->end_date ?: now()->format('Y-m-d');
            }

            $this->batch->save();
        });

        $this->loadBatch($this->batch->id);
        $this->showDayModal = false;
        $this->showViewModal = false;
        $this->resetDayForm();

        $this->dispatch('alert', ['message' => 'Daily log saved successfully.', 'type' => 'success']);
    }

    public function getEstimatedDailyTargetProperty(): int
    {
        $targetQty = max(0, (int) ($this->batch->target_qty ?? 0));
        $estimatedDays = max(1, (int) ($this->batch->estimated_days ?? 0));
        $completedDays = (int) $this->batch->days->count();
        $producedSoFar = (int) $this->batch->days->sum('produced_qty');

        $remainingTarget = max($targetQty - $producedSoFar, 0);
        $remainingDays = max($estimatedDays - $completedDays, 1);

        return (int) round($remainingTarget / $remainingDays);
    }

    public function getDayLogsWithEstimateProperty()
    {
        $targetQty = max(0, (int) ($this->batch->target_qty ?? 0));
        $estimatedDays = max(1, (int) ($this->batch->estimated_days ?? 0));
        $cumulativeProduced = 0;

        return $this->batch->days
            ->sortBy('day_no')
            ->values()
            ->map(function ($log, $index) use ($targetQty, $estimatedDays, &$cumulativeProduced) {
                $remainingTargetBeforeDay = max($targetQty - $cumulativeProduced, 0);
                $remainingDaysBeforeDay = max($estimatedDays - (int) $index, 1);
                $dynamicEstimate = (int) round($remainingTargetBeforeDay / $remainingDaysBeforeDay);

                $log->dynamic_estimate_target = $dynamicEstimate;

                $cumulativeProduced += (int) ($log->produced_qty ?? 0);

                return $log;
            });
    }

    public function getExpenseRowsTotalProperty(): float
    {
        return (float) collect($this->expense_rows)->sum(fn($row) => (float) ($row['amount'] ?? 0));
    }

    public function getTotalsProperty(): array
    {
        $days = $this->batch->days;

        return [
            'produced' => (int) $days->sum('produced_qty'),
            'produced_s' => (int) $days->sum('produced_s_qty'),
            'produced_m' => (int) $days->sum('produced_m_qty'),
            'produced_l' => (int) $days->sum('produced_l_qty'),
            'expense' => (float) $days->sum('expense_amount'),
            'days' => (int) $days->count(),
        ];
    }

    public function render()
    {
        return view('livewire.production.staff.production-staff-batch-details', [
            'totals' => $this->totals,
            'dayLogs' => $this->dayLogsWithEstimate->sortByDesc('day_no')->values(),
            'expenseRowsTotal' => $this->expenseRowsTotal,
            'estimatedDailyTarget' => $this->estimatedDailyTarget,
        ]);
    }
}
