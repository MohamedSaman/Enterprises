<?php

namespace App\Livewire\Production\Admin;

use App\Models\ProductionBatch;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.production.admin')]
#[Title('Production Batches')]
class ProductionBatches extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showCreateModal = false;

    public string $size = 'S';
    public string $start_date = '';
    public $target_qty = 0;
    public $supervisor_id = '';
    public array $staff_ids = [];
    public string $workerSearch = '';
    public string $notes = '';

    public function mount()
    {
        $this->start_date = now()->format('Y-m-d');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
    }

    private function resetForm()
    {
        $this->size = 'S';
        $this->start_date = now()->format('Y-m-d');
        $this->target_qty = 0;
        $this->supervisor_id = '';
        $this->staff_ids = [];
        $this->workerSearch = '';
        $this->notes = '';
    }

    public function createBatch()
    {
        $this->validate([
            'size' => 'required|in:S,M,L',
            'start_date' => 'required|date',
            'target_qty' => 'required|integer|min:1',
            'supervisor_id' => 'required|exists:users,id',
            'staff_ids' => 'required|array|min:1',
            'staff_ids.*' => 'required|distinct|exists:users,id',
            'notes' => 'nullable|string|max:1000',
        ], [
            'staff_ids.min' => 'Select at least 1 worker for the batch.',
        ]);

        if (in_array((int) $this->supervisor_id, array_map('intval', $this->staff_ids), true)) {
            $this->addError('staff_ids', 'Supervisor cannot also be selected as batch staff.');
            return;
        }

        DB::transaction(function () {
            $batch = ProductionBatch::create([
                'batch_code' => $this->generateBatchCode($this->size),
                'size' => $this->size,
                'start_date' => $this->start_date,
                'target_qty' => (int) $this->target_qty,
                'completed_qty' => 0,
                'supervisor_id' => (int) $this->supervisor_id,
                'created_by' => (int) Auth::id(),
                'status' => 'active',
                'notes' => $this->notes ?: null,
            ]);

            $batch->staffMembers()->sync(array_map('intval', $this->staff_ids));
        });

        $this->dispatch('alert', ['message' => 'Production batch created successfully!', 'type' => 'success']);
        $this->closeCreateModal();
        $this->resetForm();
        $this->resetPage();
    }

    private function generateBatchCode(string $size): string
    {
        $prefix = 'PB' . now()->format('Ymd') . $size;
        $latest = ProductionBatch::where('batch_code', 'like', $prefix . '-%')->orderByDesc('id')->first();

        $next = 1;
        if ($latest) {
            $parts = explode('-', $latest->batch_code);
            $next = isset($parts[1]) ? ((int) $parts[1]) + 1 : 1;
        }

        return $prefix . '-' . sprintf('%04d', $next);
    }

    public function getEligibleStaffProperty()
    {
        return User::query()
            ->with('detail')
            ->where('role', 'staff')
            ->where(function ($q) {
                $q->whereNull('module')
                    ->orWhere('module', 'production')
                    ->orWhere('module', 'both');
            })
            ->whereHas('detail', function ($q) {
                $q->where('work_role', '!=', 'supervisor');
            })
            ->orderBy('name')
            ->get();
    }

    public function getFilteredEligibleStaffProperty()
    {
        if (trim($this->workerSearch) === '') {
            return collect();
        }

        $query = User::query()
            ->with('detail')
            ->where('role', 'staff')
            ->where(function ($q) {
                $q->whereNull('module')
                    ->orWhere('module', 'production')
                    ->orWhere('module', 'both');
            })
            ->whereHas('detail', function ($q) {
                $q->where(function ($sub) {
                    $sub->whereNull('work_role')
                        ->orWhere('work_role', '!=', 'supervisor');
                });
            });

        $term = '%' . trim($this->workerSearch) . '%';
        $query->where(function ($q) use ($term) {
            $q->where('name', 'like', $term)
                ->orWhere('email', 'like', $term)
                ->orWhere('contact', 'like', $term)
                ->orWhereHas('detail', function ($sub) use ($term) {
                    $sub->where('nic_num', 'like', $term)
                        ->orWhere('work_role', 'like', $term);
                });
        });

        return $query->orderBy('name')->get();
    }

    public function getSupervisorsProperty()
    {
        return User::query()
            ->with('detail')
            ->where('role', 'staff')
            ->where(function ($q) {
                $q->whereNull('module')
                    ->orWhere('module', 'production')
                    ->orWhere('module', 'both');
            })
            ->whereHas('detail', function ($q) {
                $q->where('work_role', 'supervisor');
            })
            ->orderBy('name')
            ->get();
    }

    public function render()
    {
        $batches = ProductionBatch::with(['supervisor', 'staffMembers', 'days'])
            ->when($this->search, function ($query) {
                $query->where('batch_code', 'like', '%' . $this->search . '%')
                    ->orWhere('size', 'like', '%' . $this->search . '%')
                    ->orWhereHas('supervisor', function ($sub) {
                        $sub->where('name', 'like', '%' . $this->search . '%');
                    });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.production.admin.production-batches', [
            'batches' => $batches,
            'eligibleStaff' => $this->eligibleStaff,
            'filteredEligibleStaff' => $this->filteredEligibleStaff,
            'supervisors' => $this->supervisors,
        ]);
    }
}
