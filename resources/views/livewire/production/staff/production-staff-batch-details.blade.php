<div class="dashboard-wrapper">
    @push('styles')
    <style>
        .dashboard-wrapper {
            background-color: #f8faff;
            min-height: 100vh;
            padding: 1rem 0;
        }

        .panel {
            background: #fff;
            border: 1px solid #e8eef5;
            border-radius: 12px;
            padding: 1.25rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .stat-card {
            border: 1px solid #e8eef5;
            border-radius: 10px;
            padding: 0.9rem;
            background: #fbfdff;
        }

        .stat-label {
            font-size: 0.75rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 0.35rem;
        }

        .stat-value {
            font-size: 1.4rem;
            font-weight: 800;
            color: #0f172a;
        }

        @media (max-width: 900px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    @endpush

    <div class="panel mb-3">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h4 class="fw-bold mb-1">Batch {{ $batch->batch_code }}</h4>
                <p class="text-muted mb-0">Size {{ $batch->size }} | Target {{ number_format($batch->target_qty) }} | Status: {{ ucfirst($batch->status) }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('production.staff.batches') }}" class="btn btn-light">Back</a>
                <button class="btn btn-primary" wire:click="openDayModal">
                    <i class="bi bi-plus-lg me-1"></i> Add Daily Log
                </button>
            </div>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total Days</div>
            <div class="stat-value">{{ number_format($totals['days']) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Made Items Count</div>
            <div class="stat-value">{{ number_format($totals['produced']) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Expenses</div>
            <div class="stat-value">{{ number_format($totals['expense'], 2) }}</div>
        </div>
    </div>

    <div class="panel">
        <h5 class="fw-bold mb-3">Daily Logs</h5>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr class="small text-uppercase">
                        <th>Day</th>
                        <th>Date</th>
                        <th class="text-end">Made Items</th>
                        <th class="text-end">Expenses</th>
                        <th>Notes / Other Details</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dayLogs as $log)
                    <tr>
                        <td class="fw-bold">Day {{ $log->day_no }}</td>
                        <td>{{ optional($log->work_date)->format('Y-m-d') }}</td>
                        <td class="text-end fw-bold">{{ number_format($log->produced_qty) }}</td>
                        <td class="text-end fw-bold">{{ number_format($log->expense_amount, 2) }}</td>
                        <td>{{ $log->expense_note ?: '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">No daily logs added yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($showDayModal)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="border-radius: 14px;">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add Daily Production Log</h5>
                    <button type="button" class="btn-close" wire:click="closeDayModal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Day No</label>
                            <input type="number" class="form-control" wire:model="day_no" min="1">
                            @error('day_no') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Work Date</label>
                            <input type="date" class="form-control" wire:model="work_date">
                            @error('work_date') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Made Items Count</label>
                            <input type="number" class="form-control" wire:model="produced_qty" min="0">
                            @error('produced_qty') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Expenses</label>
                            <input type="number" class="form-control" wire:model="expense_amount" min="0" step="0.01">
                            @error('expense_amount') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Other Details / Notes</label>
                            <input type="text" class="form-control" wire:model="expense_note" placeholder="Machine issue, overtime, quality notes, etc.">
                            @error('expense_note') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" wire:click="closeDayModal">Cancel</button>
                    <button class="btn btn-primary" wire:click="saveDayLog">Save Daily Log</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>