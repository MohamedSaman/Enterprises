<div class="dashboard-wrapper">
    @push('styles')
    <style>
        .dashboard-wrapper {
            background-color: #f8faff;
            min-height: 100vh;
            padding: 1rem 0;
        }

        .section-card {
            background: #fff;
            border-radius: 12px;
            padding: 2rem;
            border: 1px solid #eef2f6;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 0.35rem;
        }

        .section-subtitle {
            font-size: 0.85rem;
            color: #94a3b8;
            font-weight: 600;
        }

        .btn-custom {
            background: #00a3e0;
            color: #fff;
            border: 0;
            border-radius: 10px;
            font-weight: 800;
            padding: 0.75rem 1.25rem;
        }

        .btn-custom:hover {
            color: #fff;
        }

        .status-badge {
            border-radius: 999px;
            padding: 0.4rem 0.8rem;
            font-size: 0.72rem;
            font-weight: 800;
            text-transform: uppercase;
        }

        .status-active {
            background: #eff6ff;
            color: #2563eb;
        }

        .status-completed {
            background: #ecfdf5;
            color: #059669;
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
            <input type="text" class="form-control" placeholder="Search batch code, size, supervisor" wire:model.live="search">
        </div>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
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
                            <a href="{{ route('production.admin.batch-details', $batch->id) }}" class="btn btn-sm btn-light" onclick="event.stopPropagation();">
                                <i class="bi bi-eye me-1"></i> View
                            </a>
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
            <div class="modal-content" style="border-radius: 14px;">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Create Production Batch</h5>
                    <button type="button" class="btn-close" wire:click="closeCreateModal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Size</label>
                            <select class="form-select" wire:model="size">
                                <option value="S">S</option>
                                <option value="M">M</option>
                                <option value="L">L</option>
                            </select>
                            @error('size') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Start Date</label>
                            <input type="date" class="form-control" wire:model="start_date">
                            @error('start_date') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Target Qty (pcs)</label>
                            <input type="number" min="1" class="form-control" wire:model="target_qty">
                            @error('target_qty') <span class="text-danger small">{{ $message }}</span> @enderror
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
                    </div>

                    <div class="mt-4">
                        <label class="form-label fw-bold">Select Workers (1 or more)</label>
                        <div class="mb-2">
                            <input
                                type="text"
                                class="form-control"
                                placeholder="Type to search workers by name, phone, NIC, role"
                                wire:model.live="workerSearch">
                        </div>
                        <div class="row g-2">
                            @if(trim($workerSearch) === '')
                            <div class="col-12">
                                <div class="text-muted small">Search workers to see results.</div>
                            </div>
                            @else
                            @forelse($filteredEligibleStaff as $staff)
                            <div class="col-md-3">
                                <div class="form-check border rounded p-2 bg-light">
                                    <input class="form-check-input" type="checkbox" value="{{ $staff->id }}" wire:model="staff_ids" id="staff_{{ $staff->id }}">
                                    <label class="form-check-label" for="staff_{{ $staff->id }}">{{ $staff->name }}</label>
                                </div>
                            </div>
                            @empty
                            <div class="col-12">
                                <div class="text-muted small">No workers found for your search.</div>
                            </div>
                            @endforelse
                            @endif
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
                    <button class="btn-custom" wire:click="createBatch">Create Batch</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>