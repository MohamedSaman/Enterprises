<?php

namespace App\Livewire\Production\Admin;

use App\Models\ProductionBatch;
use App\Models\ProductionBatchDay;
use App\Models\ProductionMaterial;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.production.admin')]
#[Title('Production Batch Details')]
class ProductionBatchDetails extends Component
{
    public ProductionBatch $batch;

    public string $activeTab = 'all';
    public bool $showDayModal = false;
    public bool $showViewModal = false;
    public bool $showDeleteModal = false;

    public $day_no = null;
    public string $work_date = '';
    public $produced_qty = 0;
    public $expense_amount = 0;
    public string $expense_note = '';
    public array $expense_rows = [];
    public array $material_rows = [];
    public array $commission_rows = [];
    public array $commissionSettings = [
        'threshold_items' => 10000,
        'rate_upto_threshold' => 10,
        'rate_after_threshold' => 15,
    ];
    public ?int $editingDayId = null;
    public ?ProductionBatchDay $viewDay = null;
    public ?ProductionBatchDay $dayToDelete = null;

    public function mount($batchId)
    {
        $this->loadBatch((int) $batchId);
        $this->loadCommissionSettings();
        $this->work_date = now()->format('Y-m-d');
    }

    private function loadBatch(int $batchId): void
    {
        $this->batch = ProductionBatch::with(['supervisor', 'material', 'staffMembers', 'days'])->findOrFail($batchId);
    }

    public function openDayModal()
    {
        $this->resetDayForm();
        $nextDayNo = ((int) $this->batch->days()->max('day_no')) + 1;

        $this->day_no = $nextDayNo;
        $this->work_date = now()->format('Y-m-d');
        $this->expense_rows = [
            ['label' => 'Electricity', 'amount' => 0, 'note' => ''],
            ['label' => 'Packing', 'amount' => 0, 'note' => ''],
        ];
        $this->loadCommissionSettings();

        $this->material_rows = [
            ['material_id' => '', 'qty_ton' => 0, 'note' => ''],
        ];

        $this->commission_rows = $this->batch->staffMembers->map(function ($staff) {
            return [
                'user_id' => $staff->id,
                'name' => $staff->name,
                'amount' => 0,
            ];
        })->toArray();

        $this->recalculateCommissionRows();

        $this->showDayModal = true;
    }

    public function openEditModal(int $dayId): void
    {
        $day = $this->batch->days()->findOrFail($dayId);

        $this->editingDayId = $day->id;
        $this->day_no = $day->day_no;
        $this->work_date = optional($day->work_date)->format('Y-m-d') ?? now()->format('Y-m-d');
        $this->produced_qty = (int) $day->produced_qty;
        $this->expense_note = (string) ($day->expense_note ?? '');
        $this->expense_rows = !empty($day->expense_items)
            ? array_values($day->expense_items)
            : [['label' => 'Expense', 'amount' => (float) $day->expense_amount, 'note' => $this->expense_note]];
        $this->material_rows = !empty($day->material_usages)
            ? array_values($day->material_usages)
            : [['material_id' => '', 'qty_ton' => 0, 'note' => '']];
        $this->commission_rows = !empty($day->staff_commissions)
            ? array_values($day->staff_commissions)
            : $this->batch->staffMembers->map(function ($staff) {
                return [
                    'user_id' => $staff->id,
                    'name' => $staff->name,
                    'amount' => 0,
                ];
            })->toArray();
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
        $this->recalculateBatchCompletion();
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

    private function resetDayForm(): void
    {
        $this->editingDayId = null;
        $this->day_no = null;
        $this->work_date = '';
        $this->produced_qty = 0;
        $this->expense_amount = 0;
        $this->expense_note = '';
        $this->expense_rows = [];
        $this->material_rows = [];
        $this->commission_rows = [];
    }

    private function recalculateBatchCompletion(): void
    {
        $this->batch->completed_qty = (int) $this->batch->days()->sum('produced_qty');
        if ((int) $this->batch->target_qty > 0 && $this->batch->completed_qty >= (int) $this->batch->target_qty) {
            $this->batch->status = 'completed';
            $this->batch->end_date = $this->batch->end_date ?: now()->format('Y-m-d');
        } elseif ((int) $this->batch->completed_qty < (int) $this->batch->target_qty) {
            $this->batch->status = 'active';
            $this->batch->end_date = null;
        }

        $this->batch->save();
    }

    public function closeDayModal()
    {
        $this->showDayModal = false;
        $this->editingDayId = null;
    }

    public function addMaterialRow()
    {
        $this->material_rows[] = ['material_id' => '', 'qty_ton' => 0, 'note' => ''];
    }

    public function removeMaterialRow($index)
    {
        if (isset($this->material_rows[$index])) {
            unset($this->material_rows[$index]);
            $this->material_rows = array_values($this->material_rows);
        }
    }

    public function updatedProducedQty(): void
    {
        $this->recalculateCommissionRows();
    }

    private function loadCommissionSettings(): void
    {
        $settings = Setting::query()
            ->whereIn('key', [
                'production_commission_threshold_items',
                'production_commission_rate_upto_threshold',
                'production_commission_rate_after_threshold',
            ])
            ->pluck('value', 'key');

        $this->commissionSettings = [
            'threshold_items' => (int) ($settings['production_commission_threshold_items'] ?? 10000),
            'rate_upto_threshold' => (float) ($settings['production_commission_rate_upto_threshold'] ?? 10),
            'rate_after_threshold' => (float) ($settings['production_commission_rate_after_threshold'] ?? 15),
        ];
    }

    private function calculateTotalCommission(int $producedQty): float
    {
        $threshold = (int) ($this->commissionSettings['threshold_items'] ?? 10000);
        $baseRate = (float) ($this->commissionSettings['rate_upto_threshold'] ?? 10);
        $afterRate = (float) ($this->commissionSettings['rate_after_threshold'] ?? 15);

        $withinThreshold = min($producedQty, $threshold);
        $afterThreshold = max($producedQty - $threshold, 0);

        return ($withinThreshold * $baseRate) + ($afterThreshold * $afterRate);
    }

    private function recalculateCommissionRows(): void
    {
        $staffCount = max(count($this->commission_rows), 1);
        $totalCommission = $this->calculateTotalCommission((int) $this->produced_qty);
        $perStaffAmount = $staffCount > 0 ? round($totalCommission / $staffCount, 2) : 0;

        foreach ($this->commission_rows as $index => $row) {
            $this->commission_rows[$index]['amount'] = $perStaffAmount;
        }
    }

    public function saveDayLog()
    {
        $this->recalculateCommissionRows();

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
            'expense_amount' => 'nullable|numeric|min:0',
            'expense_note' => 'nullable|string|max:1000',
            'expense_rows' => 'required|array|min:1',
            'expense_rows.*.label' => 'required|string|max:120',
            'expense_rows.*.amount' => 'required|numeric|min:0',
            'expense_rows.*.note' => 'nullable|string|max:255',
            'material_rows' => 'array',
            'material_rows.*.material_id' => 'nullable|exists:production_materials,id',
            'material_rows.*.qty_ton' => 'nullable|numeric|min:0',
            'material_rows.*.note' => 'nullable|string|max:255',
            'commission_rows' => 'required|array|min:1',
            'commission_rows.*.user_id' => 'required|exists:users,id',
            'commission_rows.*.amount' => 'required|numeric|min:0',
        ]);

        $existingDay = ProductionBatchDay::query()
            ->where('production_batch_id', $this->batch->id)
            ->whereDate('work_date', $this->work_date)
            ->when($this->editingDayId, fn($query) => $query->where('id', '!=', $this->editingDayId))
            ->first();

        if ($existingDay) {
            $this->addError('work_date', 'Only one daily log is allowed for a particular date.');
            return;
        }

        $materialUsages = collect($this->material_rows)
            ->filter(function ($row) {
                return !empty($row['material_id']) || (float) ($row['qty_ton'] ?? 0) > 0;
            })
            ->map(function ($row) {
                return [
                    'material_id' => (int) ($row['material_id'] ?? 0),
                    'qty_ton' => (float) ($row['qty_ton'] ?? 0),
                    'note' => $row['note'] ?? null,
                ];
            })
            ->values()
            ->toArray();

        $staffCommissions = collect($this->commission_rows)
            ->map(function ($row) {
                return [
                    'user_id' => (int) $row['user_id'],
                    'name' => $row['name'] ?? '',
                    'amount' => (float) ($row['amount'] ?? 0),
                ];
            })
            ->values()
            ->toArray();

        DB::transaction(function () use ($materialUsages, $staffCommissions, $expenseTotal, $normalizedExpenseRows) {
            ProductionBatchDay::updateOrCreate(
                [
                    'production_batch_id' => $this->batch->id,
                    'day_no' => (int) $this->day_no,
                ],
                [
                    'work_date' => $this->work_date,
                    'produced_qty' => (int) $this->produced_qty,
                    'expense_amount' => $expenseTotal,
                    'expense_note' => $this->expense_note ?: null,
                    'expense_items' => $normalizedExpenseRows,
                    'material_usages' => $materialUsages,
                    'staff_commissions' => $staffCommissions,
                    'recorded_by' => (int) Auth::id(),
                ]
            );

            $this->recalculateBatchCompletion();
        });

        $this->loadBatch($this->batch->id);
        $this->activeTab = 'day_' . $this->day_no;
        $this->showDayModal = false;
        $this->resetDayForm();

        $this->dispatch('alert', ['message' => 'Day log saved successfully.', 'type' => 'success']);
    }

    public function completeBatch()
    {
        if ($this->batch->status === 'completed') {
            return;
        }

        $this->batch->status = 'completed';
        $this->batch->end_date = now()->format('Y-m-d');
        $this->batch->save();

        $this->loadBatch($this->batch->id);
        $this->dispatch('alert', ['message' => 'Batch marked as completed.', 'type' => 'success']);
    }

    public function getDayTabsProperty()
    {
        return $this->batch->days->sortBy('day_no')->values();
    }

    public function getMaterialsProperty()
    {
        return ProductionMaterial::orderBy('name')->get();
    }

    public function getTotalsProperty()
    {
        $days = $this->batch->days;

        $totalCommission = $days->sum(function ($d) {
            return collect($d->staff_commissions ?? [])->sum('amount');
        });

        return [
            'produced' => (int) $days->sum('produced_qty'),
            'expenses' => (float) $days->sum('expense_amount'),
            'commissions' => (float) $totalCommission,
            'days' => (int) $days->count(),
        ];
    }

    public function getExpenseRowsTotalProperty(): float
    {
        return (float) collect($this->expense_rows)->sum(fn($row) => (float) ($row['amount'] ?? 0));
    }

    public function render()
    {
        $selectedDay = null;
        if (str_starts_with($this->activeTab, 'day_')) {
            $dayNo = (int) str_replace('day_', '', $this->activeTab);
            $selectedDay = $this->batch->days->firstWhere('day_no', $dayNo);
        }

        return view('livewire.production.admin.production-batch-details', [
            'dayTabs' => $this->dayTabs,
            'selectedDay' => $selectedDay,
            'materials' => $this->materials,
            'totals' => $this->totals,
            'commissionSettings' => $this->commissionSettings,
            'calculatedTotalCommission' => $this->calculateTotalCommission((int) $this->produced_qty),
        ]);
    }
}
