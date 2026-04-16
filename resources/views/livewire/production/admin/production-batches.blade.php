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
            overflow: hidden;
        }

        .batch-modal-dialog {
            max-width: min(1180px, 96vw);
        }

        .batch-modal-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            background: linear-gradient(135deg, #f8fafc 0%, #eff6ff 55%, #f0fdf4 100%);
            position: relative;
        }

        .batch-modal-header .modal-title {
            font-size: 1.2rem;
            line-height: 1.3;
        }

        .batch-modal-header p {
            font-size: 0.82rem;
        }

        .batch-modal-header::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 5px;
            height: 100%;
            background: linear-gradient(180deg, #0284c7 0%, #22c55e 100%);
        }

        .batch-modal-body {
            padding: 1rem 1.5rem 1.25rem;
            background:
                radial-gradient(circle at top right, rgba(14, 165, 233, 0.06), transparent 26%),
                radial-gradient(circle at bottom left, rgba(34, 197, 94, 0.06), transparent 30%),
                #ffffff;
        }

        .batch-modal-footer {
            border-top: 1px solid #e2e8f0;
            padding: 0.85rem 1.5rem 1rem;
            background: linear-gradient(180deg, #f8fafc 0%, #f8fbff 100%);
        }

        .batch-modal-shell {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .batch-tab-toggle {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .batch-tab-buttons {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.75rem;
            padding: 0.5rem;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
        }

        .batch-tab-button {
            margin: 0;
            padding: 0.7rem 0.9rem;
            border-radius: 11px;
            background: transparent;
            color: #64748b;
            font-size: 0.8rem;
            font-weight: 800;
            letter-spacing: 0.02em;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            user-select: none;
        }

        .batch-tab-button:hover {
            color: #0f172a;
            background: rgba(255, 255, 255, 0.7);
        }

        #batch-tab-planning:checked~.batch-tab-buttons .batch-tab-button-planning,
        #batch-tab-workers:checked~.batch-tab-buttons .batch-tab-button-workers {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            color: #ffffff;
            box-shadow: 0 10px 20px rgba(2, 132, 199, 0.18);
        }

        .batch-tab-panels {
            display: grid;
        }

        .batch-tab-panel {
            display: none;
            padding: 0.85rem 0 0;
        }

        #batch-tab-planning:checked~.batch-tab-panels .batch-tab-panel-planning {
            display: block;
        }

        #batch-tab-workers:checked~.batch-tab-panels .batch-tab-panel-workers {
            display: block;
        }

        .batch-tab-panel-card {
            background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 1rem;
        }

        .batch-tab-panel-title {
            font-size: 0.78rem;
            font-weight: 900;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 1rem;
        }

        .planner-card {
            background: linear-gradient(135deg, #f8fafc 0%, #f0f4fa 100%);
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1rem;
        }

        .planning-grid .row {
            --bs-gutter-x: 0.85rem;
            --bs-gutter-y: 0.75rem;
        }

        .planning-field {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }

        .planning-field-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 1.2rem;
        }

        .field-link {
            font-size: 0.72rem;
            font-weight: 700;
            color: #0369a1;
            text-decoration: none;
        }

        .field-link:hover {
            color: #075985;
            text-decoration: underline;
        }

        .field-help {
            font-size: 0.74rem;
            color: #64748b;
            line-height: 1.3;
            margin-top: 0.15rem;
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
            padding: 0.45rem 0.85rem;
            font-size: 0.7rem;
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
            padding: 0.85rem;
            background: #fbfdff;
        }

        .helper-box {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 10px;
            padding: 0.5rem 0.7rem;
            font-size: 0.76rem;
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
            font-size: 0.74rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 0.35rem;
        }

        .form-control,
        .form-select {
            border-radius: 8px;
            border: 1px solid #dbe3ee;
            background: #ffffff;
            padding: 0.56rem 0.72rem;
            font-weight: 500;
            font-size: 0.9rem;
            color: #0f172a;
            transition: all 0.2s ease;
        }

        .batch-modal-footer .btn {
            font-size: 0.86rem;
            padding: 0.5rem 0.9rem;
            border-radius: 8px;
        }

        .batch-modal-footer .btn-custom {
            padding: 0.56rem 1rem;
            font-size: 0.86rem;
            box-shadow: 0 2px 8px rgba(2, 132, 199, 0.2);
        }

        .batch-modal-footer .btn-custom:disabled {
            opacity: 0.55;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
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
            .batch-modal-body {
                padding: 1rem 1rem 1.25rem;
            }

            .batch-modal-header {
                padding: 1.2rem 1.2rem;
            }

            .batch-modal-footer {
                padding: 1rem 1.2rem 1.25rem;
            }

            .batch-tab-buttons {
                grid-template-columns: 1fr;
            }

            .planning-grid .row {
                --bs-gutter-x: 0.7rem;
                --bs-gutter-y: 0.65rem;
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
        <div class="modal-dialog modal-dialog-centered batch-modal-dialog">
            <div class="modal-content batch-modal-content">
                <div class="modal-header batch-modal-header">
                    <div>
                        <h5 class="modal-title fw-bold mb-1">{{ $isEditMode ? 'Edit Production Batch' : 'Create Production Batch' }}</h5>
                        <p class="text-muted small mb-0">Plan size, material, workers, and duration for this production cycle</p>
                    </div>
                    <button type="button" class="btn-close" wire:click="closeCreateModal"></button>
                </div>
                <div class="modal-body batch-modal-body">
                    <div class="batch-modal-shell">
                        <input type="radio" name="batch-modal-tab" id="batch-tab-planning" class="batch-tab-toggle" checked>
                        <input type="radio" name="batch-modal-tab" id="batch-tab-workers" class="batch-tab-toggle">

                        <div class="batch-tab-buttons">
                            <label for="batch-tab-planning" class="batch-tab-button batch-tab-button-planning">1. Batch Planning</label>
                            <label for="batch-tab-workers" class="batch-tab-button batch-tab-button-workers">2. Workers & Notes</label>
                        </div>

                        <div class="batch-tab-panels">
                            <section class="batch-tab-panel batch-tab-panel-planning">
                                <div class="batch-tab-panel-card">
                                    <div class="batch-tab-panel-title">Batch Planning</div>
                                    <div class="planner-card">
                                        <div class="planning-grid">
                                            <div class="row">
                                                <div class="col-md-6 col-lg-2">
                                                    <div class="planning-field">
                                                        <div class="planning-field-head">
                                                            <label class="form-label fw-bold mb-0">Size</label>
                                                        </div>
                                                        <select class="form-select" wire:model.live="size">
                                                            <option value="S">S</option>
                                                            <option value="M">M</option>
                                                            <option value="L">L</option>
                                                        </select>
                                                        @error('size') <span class="text-danger small">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-6 col-lg-4">
                                                    <div class="planning-field">
                                                        <div class="planning-field-head">
                                                            <label class="form-label fw-bold mb-0">Material</label>
                                                            <a href="{{ route('production.admin.settings') }}" class="field-link">Size Settings</a>
                                                        </div>
                                                        <select class="form-select" wire:model.live="production_material_id">
                                                            <option value="">Select Material</option>
                                                            @foreach($materials as $material)
                                                            <option value="{{ $material->id }}">{{ $material->name }} ({{ $material->code }})</option>
                                                            @endforeach
                                                        </select>
                                                        @error('production_material_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-6 col-lg-3">
                                                    <div class="planning-field">
                                                        <div class="planning-field-head">
                                                            <label class="form-label fw-bold mb-0">Material (Ton)</label>
                                                        </div>
                                                        <input
                                                            type="number"
                                                            step="0.001"
                                                            min="0.001"
                                                            max="{{ number_format($availableMaterialTon, 3, '.', '') }}"
                                                            class="form-control"
                                                            wire:model.live="planned_material_ton"
                                                            placeholder="e.g. 5">
                                                        <div>
                                                            <span class="metric-pill {{ $availableMaterialTon <= 0 ? 'low' : '' }}">
                                                                Available {{ $size }} stock: {{ number_format($availableMaterialTon, 3) }} ton
                                                            </span>
                                                        </div>
                                                        @error('planned_material_ton') <span class="text-danger small">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-6 col-lg-3">
                                                    <div class="planning-field">
                                                        <div class="planning-field-head">
                                                            <label class="form-label fw-bold mb-0">Estimated Target</label>
                                                        </div>
                                                        <input type="number" class="form-control" value="{{ $estimated_target_qty }}" readonly>
                                                        <input type="hidden" wire:model="target_qty">
                                                        @error('target_qty') <span class="text-danger small">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-6 col-lg-4">
                                                    <div class="planning-field">
                                                        <div class="planning-field-head">
                                                            <label class="form-label fw-bold mb-0">Estimated Days</label>
                                                        </div>
                                                        <input
                                                            type="number"
                                                            min="1"
                                                            step="1"
                                                            class="form-control"
                                                            wire:model.live="estimated_days"
                                                            placeholder="e.g. 30">
                                                        <div class="field-help">User-entered production duration for this batch.</div>
                                                        @error('estimated_days') <span class="text-danger small">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-6 col-lg-4">
                                                    <div class="planning-field">
                                                        <div class="planning-field-head">
                                                            <label class="form-label fw-bold mb-0">Start Date</label>
                                                        </div>
                                                        <input type="date" class="form-control" wire:model="start_date">
                                                        @error('start_date') <span class="text-danger small">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-12 col-lg-4">
                                                    <div class="planning-field">
                                                        <div class="planning-field-head">
                                                            <label class="form-label fw-bold mb-0">Supervisor</label>
                                                        </div>
                                                        <select class="form-select" wire:model="supervisor_id">
                                                            <option value="">Select Supervisor</option>
                                                            @foreach($supervisors as $staff)
                                                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('supervisor_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="helper-box">
                                                        Estimation rule: Target = (Material Ton x 1000) / Size Setting.
                                                        Current settings: S {{ $sizeFactors['S'] ?? 0.3 }}, M {{ $sizeFactors['M'] ?? 0.5 }}, L {{ $sizeFactors['L'] ?? 0.75 }}.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>

                            <section class="batch-tab-panel batch-tab-panel-workers">
                                <div class="batch-tab-panel-card">
                                    <div class="batch-tab-panel-title">Workers & Notes</div>
                                    @php
                                    $selectedWorkers = $eligibleStaff->whereIn('id', array_map('intval', $staff_ids));
                                    @endphp
                                    <div class="workers-panel mb-3">
                                        <label class="form-label fw-bold">{{ $isEditMode ? 'Manage Workers (Add/Remove)' : 'Select Workers (1 or more)' }}</label>
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

                                    <div>
                                        <label class="form-label fw-bold">Notes</label>
                                        <textarea class="form-control" rows="4" wire:model="notes" placeholder="Optional notes"></textarea>
                                        @error('notes') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
                <div class="modal-footer batch-modal-footer">
                    @php
                    $canSubmitBatch = !blank($size)
                    && !blank($production_material_id)
                    && ((float) $planned_material_ton > 0)
                    && ((int) $estimated_days > 0)
                    && !blank($start_date)
                    && ((int) $target_qty > 0)
                    && !blank($supervisor_id)
                    && (count($staff_ids) > 0);
                    @endphp
                    <button class="btn btn-light" wire:click="closeCreateModal">Cancel</button>
                    @if($isEditMode)
                    <button class="btn-custom" wire:click="updateBatch" @disabled(!$canSubmitBatch)>Update Batch</button>
                    @else
                    <button class="btn-custom" wire:click="createBatch" @disabled(!$canSubmitBatch)>Create Batch</button>
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