<?php

namespace App\Livewire\Production\Admin;

use App\Models\ProductionBatch;
use App\Models\ProductionMaterial;
use App\Models\ProductionMaterialBatch;
use App\Models\Setting;
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
    public $production_material_id = '';
    public $planned_material_ton = '';
    public $estimated_days = '';
    public int $estimated_target_qty = 0;
    public $target_qty = 0;
    public $supervisor_id = '';
    public array $staff_ids = [];
    public string $workerSearch = '';
    public string $notes = '';
    public bool $isEditMode = false;
    public ?int $editingBatchId = null;
    public bool $showDeleteModal = false;
    public ?int $deletingBatchId = null;
    public string $deletingBatchCode = '';
    public float $availableMaterialTon = 0;
    public array $sizeFactors = [
        'S' => 0.3,
        'M' => 0.5,
        'L' => 0.75,
    ];

    public function mount()
    {
        $this->start_date = now()->format('Y-m-d');
        $this->loadSizeFactors();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->loadSizeFactors();
        $this->refreshAvailableMaterialTon();
        $this->isEditMode = false;
        $this->editingBatchId = null;
        $this->showCreateModal = true;
    }

    public function openEditModal(int $batchId)
    {
        $batch = ProductionBatch::with('staffMembers')->findOrFail($batchId);

        $this->isEditMode = true;
        $this->editingBatchId = $batch->id;
        $this->loadSizeFactors();
        $this->size = (string) $batch->size;
        $this->production_material_id = (string) ($batch->production_material_id ?? '');
        $this->planned_material_ton = (string) ($batch->planned_material_ton ?: '');
        $this->estimated_days = (string) ($batch->estimated_days ?: '');
        $this->start_date = optional($batch->start_date)->format('Y-m-d') ?: now()->format('Y-m-d');
        $this->target_qty = (int) $batch->target_qty;
        $this->refreshAvailableMaterialTon();
        $this->recalculateEstimatedTarget();

        if ((float) $this->planned_material_ton <= 0 && (int) $batch->target_qty > 0) {
            $this->estimated_target_qty = (int) $batch->target_qty;
        }

        $this->supervisor_id = (string) $batch->supervisor_id;
        $this->staff_ids = $batch->staffMembers->pluck('id')->map(fn($id) => (int) $id)->all();
        $this->workerSearch = '';
        $this->notes = (string) ($batch->notes ?? '');
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->isEditMode = false;
        $this->editingBatchId = null;
    }

    private function resetForm()
    {
        $this->size = 'S';
        $this->production_material_id = '';
        $this->planned_material_ton = '';
        $this->estimated_days = '';
        $this->estimated_target_qty = 0;
        $this->target_qty = 0;
        $this->availableMaterialTon = 0;
        $this->start_date = now()->format('Y-m-d');
        $this->supervisor_id = '';
        $this->staff_ids = [];
        $this->workerSearch = '';
        $this->notes = '';
        $this->isEditMode = false;
        $this->editingBatchId = null;
    }

    public function createBatch()
    {
        $this->loadSizeFactors();
        $this->refreshAvailableMaterialTon();
        $this->recalculateEstimatedTarget();

        $this->validate([
            'size' => 'required|in:S,M,L',
            'production_material_id' => 'required|exists:production_materials,id',
            'planned_material_ton' => 'required|numeric|min:0.001',
            'estimated_days' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'target_qty' => 'required|integer|min:1',
            'supervisor_id' => 'required|exists:users,id',
            'staff_ids' => 'required|array|min:1',
            'staff_ids.*' => 'required|distinct|exists:users,id',
            'notes' => 'nullable|string|max:1000',
        ], [
            'staff_ids.min' => 'Select at least 1 worker for the batch.',
        ]);

        if ($this->availableMaterialTon <= 0) {
            $this->addError('planned_material_ton', 'No stock available for the selected material and size.');
            return;
        }

        if ((float) $this->planned_material_ton > $this->availableMaterialTon) {
            $this->addError('planned_material_ton', 'Planned ton cannot exceed available stock of ' . number_format($this->availableMaterialTon, 3) . ' ton.');
            return;
        }

        if (in_array((int) $this->supervisor_id, array_map('intval', $this->staff_ids), true)) {
            $this->addError('staff_ids', 'Supervisor cannot also be selected as batch staff.');
            return;
        }

        DB::transaction(function () {
            $batch = ProductionBatch::create([
                'batch_code' => $this->generateBatchCode($this->size),
                'size' => $this->size,
                'production_material_id' => (int) $this->production_material_id,
                'start_date' => $this->start_date,
                'planned_material_ton' => (float) $this->planned_material_ton,
                'estimated_days' => (int) $this->estimated_days,
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

    public function updateBatch()
    {
        if (!$this->editingBatchId) {
            $this->addError('size', 'Invalid batch selected for editing.');
            return;
        }

        $this->loadSizeFactors();
        $this->refreshAvailableMaterialTon();
        $this->recalculateEstimatedTarget();

        $this->validate([
            'size' => 'required|in:S,M,L',
            'production_material_id' => 'required|exists:production_materials,id',
            'planned_material_ton' => 'required|numeric|min:0.001',
            'estimated_days' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'target_qty' => 'required|integer|min:1',
            'supervisor_id' => 'required|exists:users,id',
            'staff_ids' => 'required|array|min:1',
            'staff_ids.*' => 'required|distinct|exists:users,id',
            'notes' => 'nullable|string|max:1000',
        ], [
            'staff_ids.min' => 'Select at least 1 worker for the batch.',
        ]);

        if ($this->availableMaterialTon <= 0) {
            $this->addError('planned_material_ton', 'No stock available for the selected material and size.');
            return;
        }

        if ((float) $this->planned_material_ton > $this->availableMaterialTon) {
            $this->addError('planned_material_ton', 'Planned ton cannot exceed available stock of ' . number_format($this->availableMaterialTon, 3) . ' ton.');
            return;
        }

        if (in_array((int) $this->supervisor_id, array_map('intval', $this->staff_ids), true)) {
            $this->addError('staff_ids', 'Supervisor cannot also be selected as batch staff.');
            return;
        }

        DB::transaction(function () {
            $batch = ProductionBatch::findOrFail($this->editingBatchId);

            $batch->update([
                'size' => $this->size,
                'production_material_id' => (int) $this->production_material_id,
                'start_date' => $this->start_date,
                'planned_material_ton' => (float) $this->planned_material_ton,
                'estimated_days' => (int) $this->estimated_days,
                'target_qty' => (int) $this->target_qty,
                'supervisor_id' => (int) $this->supervisor_id,
                'notes' => $this->notes ?: null,
            ]);

            $batch->staffMembers()->sync(array_map('intval', $this->staff_ids));

            if ((int) $batch->target_qty > 0 && (int) $batch->completed_qty >= (int) $batch->target_qty) {
                $batch->status = 'completed';
                $batch->end_date = $batch->end_date ?: now()->format('Y-m-d');
            } elseif ((int) $batch->completed_qty < (int) $batch->target_qty) {
                $batch->status = 'active';
                $batch->end_date = null;
            }

            $batch->save();
        });

        $this->dispatch('alert', ['message' => 'Production batch updated successfully!', 'type' => 'success']);
        $this->closeCreateModal();
        $this->resetForm();
    }

    public function deleteBatch(int $batchId)
    {
        $this->confirmDeleteBatch($batchId);
    }

    public function confirmDeleteBatch(int $batchId)
    {
        $batch = ProductionBatch::findOrFail($batchId);

        $this->deletingBatchId = $batch->id;
        $this->deletingBatchCode = (string) $batch->batch_code;
        $this->showDeleteModal = true;
    }

    public function cancelDeleteBatch()
    {
        $this->showDeleteModal = false;
        $this->deletingBatchId = null;
        $this->deletingBatchCode = '';
    }

    public function performDeleteBatch()
    {
        if (!$this->deletingBatchId) {
            $this->cancelDeleteBatch();
            return;
        }

        $batch = ProductionBatch::findOrFail($this->deletingBatchId);
        $batchCode = $batch->batch_code;
        $batch->delete();

        $this->cancelDeleteBatch();
        $this->dispatch('alert', ['message' => "Batch {$batchCode} deleted successfully!", 'type' => 'success']);
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

    public function addWorker(int $workerId): void
    {
        $ids = array_map('intval', $this->staff_ids);

        if (!in_array($workerId, $ids, true)) {
            $ids[] = $workerId;
        }

        $this->staff_ids = array_values(array_unique($ids));
    }

    public function removeWorker(int $workerId): void
    {
        $this->staff_ids = array_values(array_filter(
            array_map('intval', $this->staff_ids),
            fn(int $id) => $id !== $workerId
        ));
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

    public function getMaterialsProperty()
    {
        return ProductionMaterial::query()
            ->orderBy('name')
            ->get();
    }

    public function updatedSize(): void
    {
        $this->refreshAvailableMaterialTon();
        $this->recalculateEstimatedTarget();
    }

    public function updatedProductionMaterialId(): void
    {
        $this->refreshAvailableMaterialTon();
        $this->recalculateEstimatedTarget();
    }

    public function updatedPlannedMaterialTon(): void
    {
        $this->recalculateEstimatedTarget();
    }

    private function refreshAvailableMaterialTon(): void
    {
        if (empty($this->production_material_id) || empty($this->size)) {
            $this->availableMaterialTon = 0;
            return;
        }

        $this->availableMaterialTon = (float) ProductionMaterialBatch::query()
            ->where('production_material_id', (int) $this->production_material_id)
            ->whereRaw('UPPER(COALESCE(size, "")) = ?', [strtoupper((string) $this->size)])
            ->sum('remaining_quantity');

        if ((float) $this->planned_material_ton > $this->availableMaterialTon) {
            $this->planned_material_ton = $this->availableMaterialTon > 0
                ? (string) number_format($this->availableMaterialTon, 3, '.', '')
                : '';
            $this->recalculateEstimatedTarget();
        }
    }

    private function loadSizeFactors(): void
    {
        $defaults = ['S' => 0.3, 'M' => 0.5, 'L' => 0.75];

        $settings = Setting::query()
            ->whereIn('key', [
                'production_size_factor_s',
                'production_size_factor_m',
                'production_size_factor_l',
            ])
            ->pluck('value', 'key');

        $this->sizeFactors = [
            'S' => (float) ($settings['production_size_factor_s'] ?? $defaults['S']),
            'M' => (float) ($settings['production_size_factor_m'] ?? $defaults['M']),
            'L' => (float) ($settings['production_size_factor_l'] ?? $defaults['L']),
        ];
    }

    private function recalculateEstimatedTarget(): void
    {
        $ton = (float) $this->planned_material_ton;
        $factor = (float) ($this->sizeFactors[$this->size] ?? 0);

        if ($ton <= 0 || $factor <= 0) {
            $this->estimated_target_qty = 0;
            $this->target_qty = 0;
            return;
        }

        $estimated = (int) floor(($ton * 1000) / $factor);
        $this->estimated_target_qty = max(0, $estimated);
        $this->target_qty = $this->estimated_target_qty;
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
            'materials' => $this->materials,
            'sizeFactors' => $this->sizeFactors,
            'supervisors' => $this->supervisors,
        ]);
    }
}
