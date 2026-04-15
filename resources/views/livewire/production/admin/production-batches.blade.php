<div class="dashboard-wrapper">
    @push('styles')
    <style>
        .dashboard-wrapper {
            background: linear-gradient(135deg, #f5f7fb 0%, #f0f4fa 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }

        .section-card {
            background: #ffffff;
            border-radius: 14px;
            padding: 2rem;
            border: 1px solid rgba(30, 41, 59, 0.08);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
        }

        .section-card:hover {
            box-shadow: 0 12px 36px rgba(0, 0, 0, 0.08);
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 900;
            color: #0f172a;
            margin-bottom: 0.5rem;
            letter-spacing: -0.02em;
        }

        .section-subtitle {
            font-size: 0.9rem;
            color: #64748b;
            font-weight: 500;
            margin-bottom: 1.5rem;
        }

        .btn-custom {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            color: white;
            border: none;
            padding: 0.85rem 1.85rem;
            border-radius: 10px;
            font-weight: 700;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);
        }

        .btn-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(2, 132, 199, 0.35);
            color: white;
        }

        .status-badge {
            border-radius: 20px;
            padding: 0.55rem 1.1rem;
            font-size: 0.72rem;
            font-weight: 800;
            text-transform: uppercase;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        }

        .status-active {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #1e40af;
        }

        .status-completed {
            background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
            color: #166534;
        }

        .batch-modal-content {
            border-radius: 16px;
            border: 1px solid rgba(30, 41, 59, 0.08);
            box-shadow: 0 25px 60px -12px rgba(0, 0, 0, 0.15);
        }

        .batch-modal-header {
            padding: 1.75rem 2rem;
            border-bottom: 1px solid #e2e8f0;
            background: linear-gradient(135deg, #f8fafc 0%, #f0f4fa 100%);
        }

        .planner-card {
            background: linear-gradient(135deg, #f8fafc 0%, #f0f4fa 100%);
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.5rem;
        }

        .section-label {
            font-size: 0.72rem;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 0.75rem;
        }

        .metric-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            border-radius: 20px;
            padding: 0.55rem 1.1rem;
            font-size: 0.74rem;
            font-weight: 700;
            border: none;
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            color: #0284c7;
            box-shadow: 0 2px 6px rgba(2, 132, 199, 0.15);
        }

        .metric-pill.low {
            border-color: #fecaca;
            background: #fef2f2;
            color: #b91c1c;
        }

        .workers-panel {
            border: 1px solid #e6eef7;
            border-radius: 12px;
            padding: 1rem;
            background: #fbfdff;
        }

        .helper-box {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 10px;
            padding: 0.65rem 0.85rem;
            font-size: 0.82rem;
            color: #475569;
        }

        .search-input {
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            padding: 0.8rem 1rem;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .search-input:focus {
            border-color: #0284c7;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.1);
            outline: none;
        }

        .table-batch thead th {
            border: none;
            font-size: 0.72rem;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 1rem 0.9rem;
            background: rgba(248, 250, 252, 0.8);
            border-bottom: 2px solid #e2e8f0;
        }

        .table-batch tbody td {
            border-color: #f1f5f9;
            padding: 1rem 0.9rem;
            font-weight: 500;
            color: #334155;
        }

        .table-batch tbody tr {
            transition: all 0.2s ease;
        }

        .table-batch tbody tr:hover {
            background: rgba(2, 132, 199, 0.04);
        }

        .action-btn {
            width: 34px;
            height: 34px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            transition: all 0.2s ease;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(15, 23, 42, 0.08);
            border-color: #cbd5e1;
        }

        .modal-body {
            padding: 1.75rem 2rem;
        }

        .modal-footer {
            border-top: 1px solid #e2e8f0;
            padding: 1rem 2rem 1.5rem;
        }

        .form-label {
            font-weight: 700;
            color: #475569;
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 0.45rem;
        }

        .form-control,
        .form-select {
            border-radius: 10px;
            border: 1px solid #dbe3ee;
            background: #ffffff;
            padding: 0.72rem 0.85rem;
            font-weight: 500;
            color: #0f172a;
            transition: all 0.2s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #0284c7;
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.1);
        }

        .workers-panel .border.rounded {
            border-color: #dbe3ee !important;
            background: #ffffff;
        }

        .workers-panel .btn-outline-primary,
        .workers-panel .btn-outline-danger {
            border-radius: 8px;
            font-weight: 600;
        }

        @media (max-width: 991px) {
            .modal-body {
                padding: 1.2rem;
            }

            .batch-modal-header {
                padding: 1.2rem 1.2rem;
            }
        }
    </style>
    @endpush

    <div class="section-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="section-title">Production Batches</h2>
                <p class="section-subtitle mb-0">Manage rat net production by size, team, and daily output</p>
            </div>
            <button class="btn-custom" wire:click="openCreateModal">
                <i class="bi bi-plus-lg me-1"></i> New Batch
            </button>
        </div>

        <div class="mb-4" style="max-width: 320px;">
            <input type="text" class="form-control search-input" placeholder="Search batch code, size, supervisor" wire:model.live="search">
        </div>

        <div class="table-responsive">
            <table class="table align-middle table-batch mb-0">
                <thead>
                    <tr class="text-uppercase small fw-bold">
                        <th>Batch</th>
                        <th>Size</th>
                        <th>Supervisor</th>
                        <th class="text-center">Staff</th>
                        <th class="text-center">Days</th>
                        <th class="text-end">Produced / Target</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($batches as $batch)
                    <tr style="cursor: pointer;" data-href="{{ route('production.admin.batch-details', $batch->id) }}" onclick="window.location.href=this.dataset.href;">
                        <td class="fw-bold">{{ $batch->batch_code }}</td>
                        <td><span class="badge rounded-pill bg-secondary-subtle text-dark">{{ $batch->size }}</span></td>
                        <td>{{ $batch->supervisor->name ?? '-' }}</td>
                        <td class="text-center">{{ $batch->staffMembers->count() }}</td>
                        <td class="text-center">{{ $batch->days->count() }}</td>
                        <td class="text-end fw-bold">{{ number_format($batch->completed_qty) }} / {{ number_format($batch->target_qty) }}</td>
                        <td class="text-center">
                            <span class="status-badge {{ $batch->status === 'completed' ? 'status-completed' : 'status-active' }}">
                                {{ $batch->status }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('production.admin.batch-details', $batch->id) }}" class="btn btn-sm action-btn" onclick="event.stopPropagation();">
                                <i class="bi bi-eye"></i>
                            </a>
                            <button type="button" class="btn btn-sm action-btn ms-1" wire:click.stop="openEditModal({{ $batch->id }})" onclick="event.stopPropagation();">
                                <i class="bi bi-pencil-fill"></i>
                            </button>
                            <button
                                type="button"
                                class="btn btn-sm action-btn ms-1"
                                wire:click.stop="confirmDeleteBatch({{ $batch->id }})"
                                onclick="event.stopPropagation();">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">No production batches yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">{{ $batches->links() }}</div>
    </div>

    @if($showCreateModal)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content batch-modal-content">
                <div class="modal-header batch-modal-header">
                    <div>
                        <h5 class="modal-title fw-bold mb-1">{{ $isEditMode ? 'Edit Production Batch' : 'Create Production Batch' }}</h5>
                        <p class="text-muted small mb-0">Plan size, material, workers, and duration for this production cycle</p>
                    </div>
                    <button type="button" class="btn-close" wire:click="closeCreateModal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="planner-card mb-4">
                        <div class="section-label">Batch Planning</div>
                        <div class="row g-3">
                            <div class="col-md-2">
                                <label class="form-label fw-bold">Size</label>
                                <select class="form-select" wire:model.live="size">
                                    <option value="S">S</option>
                                    <option value="M">M</option>
                                    <option value="L">L</option>
                                </select>
                                @error('size') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-label fw-bold mb-0">Material</label>
                                    <a href="{{ route('production.admin.settings') }}" class="small text-decoration-none">Size Settings</a>
                                </div>
                                <select class="form-select" wire:model.live="production_material_id">
                                    <option value="">Select Material</option>
                                    @foreach($materials as $material)
                                    <option value="{{ $material->id }}">{{ $material->name }} ({{ $material->code }})</option>
                                    @endforeach
                                </select>
                                @error('production_material_id') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-bold">Material (Ton)</label>
                                <input
                                    type="number"
                                    step="0.001"
                                    min="0.001"
                                    max="{{ number_format($availableMaterialTon, 3, '.', '') }}"
                                    class="form-control"
                                    wire:model.live="planned_material_ton"
                                    placeholder="e.g. 5">
                                <div class="mt-2">
                                    <span class="metric-pill {{ $availableMaterialTon <= 0 ? 'low' : '' }}">
                                        Available {{ $size }} stock: {{ number_format($availableMaterialTon, 3) }} ton
                                    </span>
                                </div>
                                @error('planned_material_ton') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-bold">Estimated Target</label>
                                <input type="number" class="form-control" value="{{ $estimated_target_qty }}" readonly>
                                <input type="hidden" wire:model="target_qty">
                                @error('target_qty') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-bold">Estimated Days</label>
                                <input
                                    type="number"
                                    min="1"
                                    step="1"
                                    class="form-control"
                                    wire:model.live="estimated_days"
                                    placeholder="e.g. 30">
                                <div class="small text-muted mt-1">User-entered production duration for this batch.</div>
                                @error('estimated_days') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-bold">Start Date</label>
                                <input type="date" class="form-control" wire:model="start_date">
                                @error('start_date') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-bold">Supervisor</label>
                                <select class="form-select" wire:model="supervisor_id">
                                    <option value="">Select Supervisor</option>
                                    @foreach($supervisors as $staff)
                                    <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                    @endforeach
                                </select>
                                @error('supervisor_id') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-12">
                                <div class="helper-box">
                                    Estimation rule: Target = (Material Ton x 1000) / Size Setting.
                                    Current settings: S {{ $sizeFactors['S'] ?? 0.3 }}, M {{ $sizeFactors['M'] ?? 0.5 }}, L {{ $sizeFactors['L'] ?? 0.75 }}.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="workers-panel">
                        <label class="form-label fw-bold">{{ $isEditMode ? 'Manage Workers (Add/Remove)' : 'Select Workers (1 or more)' }}</label>
                        @php
                        $selectedWorkers = $eligibleStaff->whereIn('id', array_map('intval', $staff_ids));
                        @endphp
                        <div class="mb-2">
                            <input
                                type="text"
                                class="form-control"
                                placeholder="Type to search workers by name, phone, NIC, role"
                                wire:model.live="workerSearch">
                        </div>

                        <div class="border rounded p-2 bg-light-subtle" style="max-height: 220px; overflow-y: auto;">
                            @if(trim($workerSearch) === '')
                            <div class="text-muted small">Type a worker name to see search results below.</div>
                            @else
                            @forelse($filteredEligibleStaff as $staff)
                            <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                <div>
                                    <div class="fw-semibold">{{ $staff->name }}</div>
                                    <div class="small text-muted">{{ $staff->email ?? $staff->contact ?? 'Worker' }}</div>
                                </div>
                                @if(in_array((int) $staff->id, array_map('intval', $staff_ids), true))
                                <button type="button" class="btn btn-sm btn-outline-danger" wire:click="removeWorker({{ $staff->id }})">Remove</button>
                                @else
                                <button type="button" class="btn btn-sm btn-outline-primary" wire:click="addWorker({{ $staff->id }})">Add</button>
                                @endif
                            </div>
                            @empty
                            <div class="text-muted small">No workers found for your search.</div>
                            @endforelse
                            @endif
                        </div>

                        <div class="mt-3">
                            <div class="small fw-bold mb-2">Selected Workers</div>
                            <div class="border rounded p-2" style="max-height: 200px; overflow-y: auto;">
                                @forelse($selectedWorkers as $selectedWorker)
                                <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                    <div>
                                        <div class="fw-semibold">{{ $selectedWorker->name }}</div>
                                        <div class="small text-muted">{{ $selectedWorker->email ?? $selectedWorker->contact ?? 'Worker' }}</div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger" wire:click="removeWorker({{ $selectedWorker->id }})">Remove</button>
                                </div>
                                @empty
                                <div class="text-muted small">No workers selected yet.</div>
                                @endforelse
                            </div>
                        </div>

                        <div class="small text-muted mt-2">Selected workers: {{ count($staff_ids) }}</div>
                        @error('staff_ids') <span class="text-danger small d-block">{{ $message }}</span> @enderror
                    </div>

                    <div class="mt-4">
                        <label class="form-label fw-bold">Notes</label>
                        <textarea class="form-control" rows="3" wire:model="notes" placeholder="Optional notes"></textarea>
                        @error('notes') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" wire:click="closeCreateModal">Cancel</button>
                    @if($isEditMode)
                    <button class="btn-custom" wire:click="updateBatch">Update Batch</button>
                    @else
                    <button class="btn-custom" wire:click="createBatch">Create Batch</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($showDeleteModal)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 14px;">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-danger">Delete Production Batch</h5>
                    <button type="button" class="btn-close" wire:click="cancelDeleteBatch"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-1">Are you sure you want to delete this batch?</p>
                    <p class="mb-0"><strong>{{ $deletingBatchCode }}</strong></p>
                    <small class="text-muted">This will permanently remove the batch and related day logs.</small>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" wire:click="cancelDeleteBatch">Cancel</button>
                    <button class="btn btn-danger" wire:click="performDeleteBatch">Yes, Delete</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>