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

    public $day_no = null;
    public string $work_date = '';
    public $produced_qty = 0;
    public $expense_amount = 0;
    public string $expense_note = '';

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
        $this->day_no = ((int) $this->batch->days()->max('day_no')) + 1;
        $this->work_date = now()->format('Y-m-d');
        $this->produced_qty = 0;
        $this->expense_amount = 0;
        $this->expense_note = '';
        $this->showDayModal = true;
    }

    public function closeDayModal(): void
    {
        $this->showDayModal = false;
    }

    public function saveDayLog(): void
    {
        $this->validate([
            'day_no' => 'required|integer|min:1',
            'work_date' => 'required|date',
            'produced_qty' => 'required|integer|min:0',
            'expense_amount' => 'required|numeric|min:0',
            'expense_note' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () {
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

        $this->dispatch('alert', ['message' => 'Daily log saved successfully.', 'type' => 'success']);
    }

    public function getTotalsProperty(): array
    {
        $days = $this->batch->days;

        return [
            'produced' => (int) $days->sum('produced_qty'),
            'expense' => (float) $days->sum('expense_amount'),
            'days' => (int) $days->count(),
        ];
    }

    public function render()
    {
        return view('livewire.production.staff.production-staff-batch-details', [
            'totals' => $this->totals,
            'dayLogs' => $this->batch->days->sortByDesc('day_no')->values(),
        ]);
    }
}
