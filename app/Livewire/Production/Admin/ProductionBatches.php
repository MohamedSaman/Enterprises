<?php

namespace App\Livewire\Production\Admin;

use App\Models\ProductionBatch;
use App\Models\ProductionMaterial;
use App\Models\ProductionMaterialBatch;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
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

    public string $size = '';
    public string $start_date = '';
    public $production_material_id = '';
    public $production_material_batch_id = '';
    public $purchase_batch_no = '';
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
    public array $selectedBatchSummary = [];
    public array $estimatedTargetBreakdown = [];
    public array $allocatedTonBreakdown = [];
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
        $this->refreshSuggestedMaterialBatch();
        $this->showCreateModal = true;
    }

    public function openEditModal(int $batchId)
    {
        $batch = ProductionBatch::with('staffMembers')->findOrFail($batchId);

        $this->isEditMode = true;
        $this->editingBatchId = $batch->id;
        $this->loadSizeFactors();
        $this->production_material_id = (string) ($batch->production_material_id ?? '');
        $this->production_material_batch_id = (string) ($batch->production_material_batch_id ?? '');
        $this->purchase_batch_no = $this->normalizePurchaseBatchNo((string) ($batch->purchase_batch_no ?? ''));
        $this->planned_material_ton = (string) ($batch->planned_material_ton ?: '');
        $this->production_material_batch_id = (string) ($batch->production_material_batch_id ?? '');
        $this->purchase_batch_no = $this->normalizePurchaseBatchNo((string) ($batch->purchase_batch_no ?? ''));
        $this->planned_material_ton = (string) ($batch->planned_material_ton ?: '');
        $this->estimated_days = (string) ($batch->estimated_days ?: '');
        $this->start_date = optional($batch->start_date)->format('Y-m-d') ?: now()->format('Y-m-d');
        $this->target_qty = (int) $batch->target_qty;
        
        $this->allocatedTonBreakdown = $batch->allocated_breakdown ?: [strtoupper(trim((string)$batch->size)) => (float)$batch->planned_material_ton];
        
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
        $this->production_material_id = '';
        $this->production_material_batch_id = '';
        $this->purchase_batch_no = '';
        $this->planned_material_ton = '';
        $this->estimated_days = '';
        $this->estimated_target_qty = 0;
        $this->target_qty = 0;
        $this->availableMaterialTon = 0;
        $this->selectedBatchSummary = [];
        $this->estimatedTargetBreakdown = [];
        $this->allocatedTonBreakdown = [];
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
            'production_material_id' => 'required|exists:production_materials,id',
            'purchase_batch_no' => 'required|string|max:120',
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
            $this->addError('purchase_batch_no', 'No stock available for the selected purchase batch.');
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

        $groupedBatches = $this->getSelectedGroupedMaterialBatches();

        if ($groupedBatches->isEmpty()) {
            $this->addError('purchase_batch_no', 'Please choose a valid purchase batch group for this material.');
            return;
        }

        try {
            DB::transaction(function () use ($groupedBatches) {
                $selectedBatch = $groupedBatches->first();

                $lockedBatches = ProductionMaterialBatch::query()
                    ->lockForUpdate()
                    ->where('production_material_id', (int) $this->production_material_id)
                    ->where('remaining_quantity', '>', 0)
                    ->orderBy('created_at')
                    ->orderBy('id')
                    ->get()
                    ->filter(function ($batch) {
                        return $this->normalizePurchaseBatchNo((string) $batch->batch_no) === $this->normalizePurchaseBatchNo((string) $this->purchase_batch_no);
                    })
                    ->values();

                if ($lockedBatches->isEmpty()) {
                    throw new \RuntimeException('Selected purchase batch group is no longer available.');
                }

                $groupedStock = (float) $lockedBatches->sum('remaining_quantity');

                if ((int) $selectedBatch->production_material_id !== (int) $this->production_material_id) {
                    throw new \RuntimeException('Selected purchase batch group does not match the chosen material.');
                }

                if ($groupedStock < (float) $this->planned_material_ton) {
                    throw new \RuntimeException('Selected purchase batch group does not have enough remaining quantity.');
                }

                $storedSize = $this->resolveSizeForStorage($lockedBatches);

                $batch = ProductionBatch::create([
                    'batch_code' => $this->generateBatchCode(''),
                    'size' => $storedSize,
                    'production_material_id' => (int) $this->production_material_id,
                    'production_material_batch_id' => (int) $selectedBatch->id,
                    'purchase_batch_no' => $this->normalizePurchaseBatchNo((string) $this->purchase_batch_no),
                    'start_date' => $this->start_date,
                    'planned_material_ton' => (float) $this->planned_material_ton,
                    'estimated_days' => (int) $this->estimated_days,
                    'target_qty' => (int) $this->target_qty,
                    'allocated_breakdown' => $this->allocatedTonBreakdown,
                    'completed_qty' => 0,
                    'supervisor_id' => (int) $this->supervisor_id,
                    'created_by' => (int) Auth::id(),
                    'status' => 'active',
                    'notes' => $this->notes ?: null,
                ]);

                $batch->staffMembers()->sync(array_map('intval', $this->staff_ids));

                // Consume stock per-size based on allocated amounts
                foreach ($lockedBatches as $lockedBatch) {
                    $batchSize = strtoupper(trim((string) $lockedBatch->size));
                    $allocatedForSize = (float) ($this->allocatedTonBreakdown[$batchSize] ?? 0);

                    if ($allocatedForSize <= 0) {
                        continue;
                    }

                    $consume = min((float) $lockedBatch->remaining_quantity, $allocatedForSize);
                    $lockedBatch->remaining_quantity = max(0, (float) $lockedBatch->remaining_quantity - $consume);
                    $lockedBatch->save();

                    // Reduce allocated amount for this size (in case multiple rows share same size)
                    $this->allocatedTonBreakdown[$batchSize] = $allocatedForSize - $consume;
                }
            });
        } catch (\Throwable $exception) {
            $this->addError('purchase_batch_no', $exception->getMessage());
            $this->dispatch('alert', ['message' => $exception->getMessage(), 'type' => 'error']);
            return;
        }

        $this->dispatch('alert', ['message' => 'Production batch created successfully!', 'type' => 'success']);
        $this->closeCreateModal();
        $this->resetForm();
        $this->resetPage();
    }

    public function updateBatch()
    {
        if (!$this->editingBatchId) {
            $this->addError('production_material_batch_id', 'Invalid batch selected for editing.');
            return;
        }

        $this->loadSizeFactors();
        $this->refreshAvailableMaterialTon();
        $this->recalculateEstimatedTarget();

        $this->validate([
            'production_material_id' => 'required|exists:production_materials,id',
            'purchase_batch_no' => 'required|string|max:120',
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
            $this->addError('purchase_batch_no', 'No stock available for the selected purchase batch.');
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

        $groupedBatches = $this->getSelectedGroupedMaterialBatches();

        if ($groupedBatches->isEmpty()) {
            $this->addError('purchase_batch_no', 'Please choose a valid purchase batch group for this material.');
            return;
        }

        $selectedBatch = $groupedBatches->first();

        try {
            DB::transaction(function () use ($selectedBatch, $groupedBatches) {
                $batch = ProductionBatch::findOrFail($this->editingBatchId);
                $oldAllocated = $batch->allocated_breakdown ?: [strtoupper(trim((string)$batch->size)) => (float)$batch->planned_material_ton];

                $lockedBatches = ProductionMaterialBatch::query()
                    ->lockForUpdate()
                    ->where('production_material_id', (int) $this->production_material_id)
                    ->orderBy('created_at')
                    ->orderBy('id')
                    ->get()
                    ->filter(function ($b) {
                        return $this->normalizePurchaseBatchNo((string) $b->batch_no) === $this->normalizePurchaseBatchNo((string) $this->purchase_batch_no);
                    })
                    ->values();

                // Revert old allocation
                foreach ($oldAllocated as $size => $amount) {
                    if ($amount <= 0) continue;
                    $row = $lockedBatches->firstWhere(fn($b) => strtoupper(trim((string)$b->size)) === $size);
                    if ($row) {
                        $row->remaining_quantity += $amount;
                        $row->save();
                    }
                }

                $storedSize = $this->resolveSizeForStorage($lockedBatches);

                $batch->update([
                    'size' => $storedSize,
                    'production_material_id' => (int) $this->production_material_id,
                    'production_material_batch_id' => (int) $selectedBatch->id,
                    'purchase_batch_no' => $this->normalizePurchaseBatchNo((string) $this->purchase_batch_no),
                    'start_date' => $this->start_date,
                    'planned_material_ton' => (float) $this->planned_material_ton,
                    'allocated_breakdown' => $this->allocatedTonBreakdown,
                    'estimated_days' => (int) $this->estimated_days,
                    'target_qty' => (int) $this->target_qty,
                    'supervisor_id' => (int) $this->supervisor_id,
                    'notes' => $this->notes ?: null,
                ]);

                $batch->staffMembers()->sync(array_map('intval', $this->staff_ids));

                // Consume new allocation
                $remainingAllocations = $this->allocatedTonBreakdown;
                foreach ($lockedBatches as $lockedBatch) {
                    $batchSize = strtoupper(trim((string) $lockedBatch->size));
                    $allocatedForSize = (float) ($remainingAllocations[$batchSize] ?? 0);

                    if ($allocatedForSize <= 0) {
                        continue;
                    }

                    $consume = min((float) $lockedBatch->remaining_quantity, $allocatedForSize);
                    $lockedBatch->remaining_quantity = max(0, (float) $lockedBatch->remaining_quantity - $consume);
                    $lockedBatch->save();

                    $remainingAllocations[$batchSize] = $allocatedForSize - $consume;
                }

                if ((int) $batch->target_qty > 0 && (int) $batch->completed_qty >= (int) $batch->target_qty) {
                    $batch->status = 'completed';
                    $batch->end_date = $batch->end_date ?: now()->format('Y-m-d');
                } elseif ((int) $batch->completed_qty < (int) $batch->target_qty) {
                    $batch->status = 'active';
                    $batch->end_date = null;
                }

                $batch->save();
            });
        } catch (\Throwable $exception) {
            $this->addError('purchase_batch_no', $exception->getMessage());
            $this->dispatch('alert', ['message' => $exception->getMessage(), 'type' => 'error']);
            return;
        }

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

    public function getAvailableMaterialBatchesProperty()
    {
        if (empty($this->production_material_id)) {
            return collect();
        }

        $rows = ProductionMaterialBatch::query()
            ->where('production_material_id', (int) $this->production_material_id)
            ->where('remaining_quantity', '>', 0)
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        return $rows->groupBy(function ($row) {
            return $this->normalizePurchaseBatchNo((string) $row->batch_no);
        })->map(function ($group, $purchaseBatchNo) {
            $sizeBreakdown = $group->groupBy(fn($row) => strtoupper((string) $row->size))
                ->map(function ($sizeRows, $size) {
                    return [
                        'size' => $size,
                        'remaining_quantity' => (float) $sizeRows->sum('remaining_quantity'),
                    ];
                })
                ->sortKeys()
                ->values()
                ->all();

            return [
                'purchase_batch_no' => $purchaseBatchNo,
                'representative_id' => (int) $group->first()->id,
                'remaining_quantity' => (float) $group->sum('remaining_quantity'),
                'size_breakdown' => $sizeBreakdown,
                'raw_batch_nos' => $group->pluck('batch_no')->values()->all(),
                'created_at' => optional($group->sortBy('created_at')->first())->created_at,
            ];
        })->sortBy(function ($group) {
            return optional($group['created_at'])->timestamp ?? 0;
        })->values();
    }

    public function updatedSize(): void
    {
        $this->refreshSuggestedMaterialBatch();
        $this->recalculateEstimatedTarget();
    }

    public function updatedProductionMaterialId(): void
    {
        $this->refreshSuggestedMaterialBatch();
        $this->recalculateEstimatedTarget();
    }

    public function updatedProductionMaterialBatchId(): void
    {
        $this->allocatedTonBreakdown = [];
        $this->refreshAvailableMaterialTon();
        $this->recalculateEstimatedTarget();
    }

    public function updatedPurchaseBatchNo(): void
    {
        $this->allocatedTonBreakdown = [];
        $this->refreshAvailableMaterialTon();
        $this->recalculateEstimatedTarget();
    }

    public function updatedPlannedMaterialTon(): void
    {
        $this->recalculateEstimatedTarget();
    }

    public function updated($property, $value = null): void
    {
        if (str_starts_with($property, 'allocatedTonBreakdown.')) {
            $this->handleAllocatedTonBreakdownUpdate();
        }
    }

    private function handleAllocatedTonBreakdownUpdate(): void
    {
        $summary = $this->selectedBatchSummary ?: $this->availableMaterialBatches->firstWhere('purchase_batch_no', $this->purchase_batch_no);
        if (!$summary) {
            return;
        }

        // Clamp each allocated value to 0..available and rebuild
        $clamped = [];
        foreach (($summary['size_breakdown'] ?? []) as $sizeRow) {
            $size = (string) ($sizeRow['size'] ?? '');
            $available = (float) ($sizeRow['remaining_quantity'] ?? 0);
            $allocated = isset($this->allocatedTonBreakdown[$size])
                ? (float) $this->allocatedTonBreakdown[$size]
                : $available;
            $clamped[$size] = max(0, min($allocated, $available));
        }
        $this->allocatedTonBreakdown = $clamped;

        // Recalculate planned_material_ton as sum of allocated
        $totalAllocated = array_sum($clamped);
        $this->planned_material_ton = $totalAllocated > 0
            ? (string) number_format($totalAllocated, 3, '.', '')
            : '';

        $this->recalculateEstimatedTarget();
    }

    private function getAvailableMaterialBatchQuery()
    {
        if (empty($this->production_material_id)) {
            return ProductionMaterialBatch::query()->whereRaw('1 = 0');
        }

        return ProductionMaterialBatch::query()
            ->where('production_material_id', (int) $this->production_material_id)
            ->where('remaining_quantity', '>', 0)
            ->orderBy('created_at')
            ->orderBy('id');
    }

    private function refreshSuggestedMaterialBatch(): void
    {
        if (empty($this->production_material_id)) {
            $this->production_material_batch_id = '';
            $this->availableMaterialTon = 0;
            $this->purchase_batch_no = '';
            $this->selectedBatchSummary = [];
            $this->estimatedTargetBreakdown = [];
            $this->allocatedTonBreakdown = [];
            return;
        }

        $availableBatches = $this->availableMaterialBatches;
        $suggestedBatch = $availableBatches->first();

        if (!$suggestedBatch) {
            $this->production_material_batch_id = '';
            $this->availableMaterialTon = 0;
            $this->planned_material_ton = '';
            $this->purchase_batch_no = '';
            $this->selectedBatchSummary = [];
            $this->estimatedTargetBreakdown = [];
            $this->allocatedTonBreakdown = [];
            $this->recalculateEstimatedTarget();
            return;
        }

        $selectedId = (int) $this->production_material_batch_id;
        $matchedBatch = $availableBatches->firstWhere('representative_id', $selectedId)
            ?: $availableBatches->firstWhere('purchase_batch_no', $this->purchase_batch_no);

        if (!$matchedBatch || $this->isEditMode === false) {
            $this->production_material_batch_id = (string) $suggestedBatch['representative_id'];
            $this->purchase_batch_no = (string) $suggestedBatch['purchase_batch_no'];
            $matchedBatch = $suggestedBatch;
        }

        $this->selectedBatchSummary = $matchedBatch;
        $this->availableMaterialTon = (float) ($matchedBatch['remaining_quantity'] ?? 0);

        // Initialize allocatedTonBreakdown with full available stock per size
        $this->initAllocatedBreakdownFromSummary($matchedBatch);

        $this->planned_material_ton = $this->availableMaterialTon > 0
            ? (string) number_format($this->availableMaterialTon, 3, '.', '')
            : '';

        $this->recalculateEstimatedTarget();
    }

    private function refreshAvailableMaterialTon(): void
    {
        if (empty($this->purchase_batch_no)) {
            $this->refreshSuggestedMaterialBatch();
            return;
        }

        $group = $this->availableMaterialBatches->firstWhere('purchase_batch_no', $this->purchase_batch_no);

        if (!$group && $this->isEditMode && $this->purchase_batch_no) {
            $rows = ProductionMaterialBatch::query()
                ->where('production_material_id', (int) $this->production_material_id)
                ->get()
                ->filter(fn($b) => $this->normalizePurchaseBatchNo((string) $b->batch_no) === $this->normalizePurchaseBatchNo((string) $this->purchase_batch_no))
                ->values();
                
            if ($rows->isNotEmpty()) {
                $sizeBreakdown = $rows->groupBy(fn($row) => strtoupper((string) $row->size))
                    ->map(function ($sizeRows, $size) {
                        return [
                            'size' => $size,
                            'remaining_quantity' => (float) $sizeRows->sum('remaining_quantity'),
                        ];
                    })->sortKeys()->values()->all();
                    
                $group = [
                    'purchase_batch_no' => $this->purchase_batch_no,
                    'representative_id' => (int) $rows->first()->id,
                    'remaining_quantity' => (float) $rows->sum('remaining_quantity'),
                    'size_breakdown' => $sizeBreakdown,
                    'raw_batch_nos' => $rows->pluck('batch_no')->values()->all(),
                ];
            }
        }

        if (!$group) {
            $this->refreshSuggestedMaterialBatch();
            return;
        }

        if ($this->isEditMode && $this->editingBatchId) {
            $batch = ProductionBatch::find($this->editingBatchId);
            $oldAllocated = $batch ? ($batch->allocated_breakdown ?: [strtoupper(trim((string)$batch->size)) => (float)$batch->planned_material_ton]) : [];
            
            $totalRemaining = 0;
            foreach ($group['size_breakdown'] as &$sizeRow) {
                $size = $sizeRow['size'];
                $allocated = (float) ($oldAllocated[$size] ?? 0);
                $sizeRow['remaining_quantity'] += $allocated;
                $totalRemaining += $sizeRow['remaining_quantity'];
            }
            $group['remaining_quantity'] = $totalRemaining;
        }

        $this->production_material_batch_id = (string) ($group['representative_id'] ?? '');
        $this->availableMaterialTon = (float) ($group['remaining_quantity'] ?? 0);
        $this->purchase_batch_no = (string) ($group['purchase_batch_no'] ?? '');
        $this->selectedBatchSummary = $group;

        if (empty($this->allocatedTonBreakdown)) {
            $this->initAllocatedBreakdownFromSummary($group);
        }

        // Always sync planned_material_ton with the current allocation!
        $totalAllocated = array_sum($this->allocatedTonBreakdown ?: []);
        $this->planned_material_ton = $totalAllocated > 0
            ? (string) number_format($totalAllocated, 3, '.', '')
            : '';
            
        $this->recalculateEstimatedTarget();
    }

    private function initAllocatedBreakdownFromSummary(array $summary): void
    {
        $allocated = [];
        foreach (($summary['size_breakdown'] ?? []) as $sizeRow) {
            $size = (string) ($sizeRow['size'] ?? '');
            $allocated[$size] = (float) ($sizeRow['remaining_quantity'] ?? 0);
        }
        $this->allocatedTonBreakdown = $allocated;
    }

    private function normalizePurchaseBatchNo(string $batchNo): string
    {
        return preg_replace('/^(BT\d{8})[A-Z](-\d{4})$/i', '$1$2', trim($batchNo)) ?? trim($batchNo);
    }

    private function resolveSizeForStorage(Collection $batches): string
    {
        $validSizes = ['S', 'M', 'L'];
        $firstValid = $batches->pluck('size')
            ->map(fn($size) => strtoupper(trim((string) $size)))
            ->first(fn($size) => in_array($size, $validSizes, true));

        return $firstValid ?: 'S';
    }

    private function getSelectedGroupedMaterialBatches(): Collection
    {
        if (empty($this->production_material_id) || empty($this->purchase_batch_no)) {
            return collect();
        }

        $normalizedPurchaseBatchNo = $this->normalizePurchaseBatchNo($this->purchase_batch_no);

        $query = ProductionMaterialBatch::query()
            ->where('production_material_id', (int) $this->production_material_id);

        if (!$this->isEditMode) {
            $query->where('remaining_quantity', '>', 0);
        }

        return $query->orderBy('created_at')
            ->orderBy('id')
            ->get()
            ->filter(function ($batch) use ($normalizedPurchaseBatchNo) {
                return $this->normalizePurchaseBatchNo((string) $batch->batch_no) === $normalizedPurchaseBatchNo;
            })
            ->values();
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
        $summary = $this->selectedBatchSummary ?: $this->availableMaterialBatches->firstWhere('purchase_batch_no', $this->purchase_batch_no);

        if (!$summary) {
            $this->estimated_target_qty = 0;
            $this->target_qty = 0;
            $this->estimatedTargetBreakdown = [];
            return;
        }

        $totalEstimated = 0;
        $breakdown = [];

        foreach (($summary['size_breakdown'] ?? []) as $sizeRow) {
            $size = (string) ($sizeRow['size'] ?? '');
            $availableTon = (float) ($sizeRow['remaining_quantity'] ?? 0);

            // Use allocated amount if set, otherwise use full available
            $allocatedTon = isset($this->allocatedTonBreakdown[$size])
                ? (float) $this->allocatedTonBreakdown[$size]
                : $availableTon;
            // Clamp to 0..available
            $allocatedTon = max(0, min($allocatedTon, $availableTon));

            $factor = (float) ($this->sizeFactors[$size] ?? 0);
            $estimated = ($allocatedTon > 0 && $factor > 0) ? (int) floor(($allocatedTon * 1000) / $factor) : 0;

            $breakdown[] = [
                'size' => $size,
                'available_ton' => $availableTon,
                'allocated_ton' => $allocatedTon,
                'estimated' => $estimated,
            ];

            $totalEstimated += $estimated;
        }

        $this->estimatedTargetBreakdown = $breakdown;
        $this->estimated_target_qty = $totalEstimated;
        $this->target_qty = $totalEstimated;

        // Update planned_material_ton as sum of allocations
        $totalAllocated = array_sum(array_column($breakdown, 'allocated_ton'));
        $this->planned_material_ton = $totalAllocated > 0
            ? (string) number_format($totalAllocated, 3, '.', '')
            : '';
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
            'availableMaterialBatches' => $this->availableMaterialBatches,
            'sizeFactors' => $this->sizeFactors,
            'supervisors' => $this->supervisors,
        ]);
    }
}
