<?php

namespace App\Livewire\Production\Admin;

use App\Models\ProductionBatch;
use App\Models\ProductionBatchDay;
use App\Models\ProductionMaterial;
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

    public $day_no = null;
    public string $work_date = '';
    public $produced_qty = 0;
    public $expense_amount = 0;
    public string $expense_note = '';
    public array $material_rows = [];
    public array $commission_rows = [];

    public function mount($batchId)
    {
        $this->loadBatch((int) $batchId);
        $this->work_date = now()->format('Y-m-d');
    }

    private function loadBatch(int $batchId): void
    {
        $this->batch = ProductionBatch::with(['supervisor', 'staffMembers', 'days'])->findOrFail($batchId);
    }

    public function openDayModal()
    {
        $nextDayNo = ((int) $this->batch->days()->max('day_no')) + 1;

        $this->day_no = $nextDayNo;
        $this->work_date = now()->format('Y-m-d');
        $this->produced_qty = 0;
        $this->expense_amount = 0;
        $this->expense_note = '';

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

        $this->showDayModal = true;
    }

    public function closeDayModal()
    {
        $this->showDayModal = false;
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

    public function saveDayLog()
    {
        $this->validate([
            'day_no' => 'required|integer|min:1',
            'work_date' => 'required|date',
            'produced_qty' => 'required|integer|min:0',
            'expense_amount' => 'required|numeric|min:0',
            'expense_note' => 'nullable|string|max:1000',
            'material_rows' => 'array',
            'material_rows.*.material_id' => 'nullable|exists:production_materials,id',
            'material_rows.*.qty_ton' => 'nullable|numeric|min:0',
            'material_rows.*.note' => 'nullable|string|max:255',
            'commission_rows' => 'required|array|min:1',
            'commission_rows.*.user_id' => 'required|exists:users,id',
            'commission_rows.*.amount' => 'required|numeric|min:0',
        ]);

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

        DB::transaction(function () use ($materialUsages, $staffCommissions) {
            ProductionBatchDay::updateOrCreate(
                [
                    'production_batch_id' => $this->batch->id,
                    'day_no' => (int) $this->day_no,
                ],
                [
                    'work_date' => $this->work_date,
                    'produced_qty' => (int) $this->produced_qty,
                    'expense_amount' => (float) $this->expense_amount,
                    'expense_note' => $this->expense_note ?: null,
                    'material_usages' => $materialUsages,
                    'staff_commissions' => $staffCommissions,
                    'recorded_by' => (int) Auth::id(),
                ]
            );

            $this->batch->completed_qty = (int) $this->batch->days()->sum('produced_qty');
            if ((int) $this->batch->target_qty > 0 && $this->batch->completed_qty >= (int) $this->batch->target_qty) {
                $this->batch->status = 'completed';
                $this->batch->end_date = now()->format('Y-m-d');
            }
            $this->batch->save();
        });

        $this->loadBatch($this->batch->id);
        $this->activeTab = 'day_' . $this->day_no;
        $this->showDayModal = false;

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
        ]);
    }
}
