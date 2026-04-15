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

        .log-actions {
            display: flex;
            gap: 0.35rem;
            justify-content: flex-end;
        }

        .expense-row {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 0.75rem;
            margin-bottom: 0.6rem;
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
                        <th class="text-end">Actions</th>
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
                        <td>
                            <div class="log-actions">
                                <button class="btn btn-sm btn-light" wire:click="openViewModal({{ $log->id }})">View</button>
                                <button class="btn btn-sm btn-outline-primary" wire:click="openEditModal({{ $log->id }})">Edit</button>
                                <button class="btn btn-sm btn-outline-danger" wire:click="confirmDeleteDay({{ $log->id }})">Delete</button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">No daily logs added yet.</td>
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
                    </div>

                    <div class="mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-bold mb-0">Expense Lines</label>
                            <button type="button" class="btn btn-sm btn-outline-primary" wire:click="addExpenseRow">+ Add Expense</button>
                        </div>
                        @foreach($expense_rows as $index => $expenseRow)
                        <div class="expense-row">
                            <div class="row g-2 align-items-start">
                                <div class="col-md-4">
                                    <label class="form-label small text-muted mb-1">Type</label>
                                    <input type="text" class="form-control" wire:model="expense_rows.{{ $index }}.label" placeholder="Electricity, Packing, Transport">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small text-muted mb-1">Amount</label>
                                    <input type="number" class="form-control" wire:model="expense_rows.{{ $index }}.amount" min="0" step="0.01">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small text-muted mb-1">Note</label>
                                    <input type="text" class="form-control" wire:model="expense_rows.{{ $index }}.note" placeholder="Optional note">
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="button" class="btn btn-outline-danger w-100" wire:click="removeExpenseRow({{ $index }})">X</button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        @error('expense_rows') <span class="text-danger small d-block">{{ $message }}</span> @enderror
                    </div>

                    <div class="mt-3 p-3 border rounded bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold">Total Expense</span>
                            <span class="fw-bold">Rs. {{ number_format($expenseRowsTotal, 2) }}</span>
                        </div>
                        <div class="small text-muted mt-1">This total will be saved to the daily log.</div>
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

    @if($showViewModal && $viewDay)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="border-radius: 14px;">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">View Daily Log - Day {{ $viewDay->day_no }}</h5>
                    <button type="button" class="btn-close" wire:click="closeViewModal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <div class="p-3 border rounded">Date<br><b>{{ optional($viewDay->work_date)->format('Y-m-d') }}</b></div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded">Made Items<br><b>{{ number_format($viewDay->produced_qty) }}</b></div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded">Total Expense<br><b>Rs. {{ number_format($viewDay->expense_amount, 2) }}</b></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="fw-bold mb-2">Expense Lines</div>
                        <ul class="list-group">
                            @forelse(($viewDay->expense_items ?? []) as $item)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>{{ $item['label'] ?? 'Expense' }} <span class="text-muted small">{{ $item['note'] ?? '' }}</span></span>
                                <strong>Rs. {{ number_format((float) ($item['amount'] ?? 0), 2) }}</strong>
                            </li>
                            @empty
                            <li class="list-group-item text-muted">No expense lines recorded.</li>
                            @endforelse
                        </ul>
                    </div>
                    <div>
                        <div class="fw-bold mb-2">Notes</div>
                        <div class="border rounded p-3 bg-light">{{ $viewDay->expense_note ?: 'No note added.' }}</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" wire:click="closeViewModal">Close</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($showDeleteModal && $dayToDelete)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 14px;">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-danger">Delete Daily Log</h5>
                    <button type="button" class="btn-close" wire:click="cancelDeleteDay"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-1">Are you sure you want to delete Day {{ $dayToDelete->day_no }}?</p>
                    <p class="mb-0 text-muted">This will remove the log, all expense lines, and its production count.</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" wire:click="cancelDeleteDay">Cancel</button>
                    <button class="btn btn-danger" wire:click="deleteDay">Delete</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>