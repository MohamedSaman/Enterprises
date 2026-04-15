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
            border: 1px solid #eef2f6;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        }

        .left-tabs {
            min-height: 520px;
            border-right: 1px solid #eef2f6;
        }

        .tab-item {
            border: 0;
            background: transparent;
            width: 100%;
            text-align: left;
            font-weight: 700;
            color: #475569;
            padding: 0.75rem 1rem;
            border-radius: 10px;
        }

        .tab-item.active {
            background: #e0f5fe;
            color: #0284c7;
        }

        .summary-chip {
            border-radius: 999px;
            padding: 0.45rem 0.85rem;
            background: #f1f5f9;
            font-weight: 700;
            color: #334155;
        }
    </style>
    @endpush

    <div class="section-card p-4 mb-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4 class="fw-bold mb-1">{{ $batch->batch_code }}</h4>
                <div class="text-muted small">
                    Size {{ $batch->size }} | Material: {{ $batch->material->name ?? '-' }} | Supervisor: {{ $batch->supervisor->name ?? '-' }}
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="summary-chip">Produced: {{ number_format($totals['produced']) }}</span>
                <span class="summary-chip">Expenses: {{ number_format($totals['expenses'], 2) }}</span>
                <span class="summary-chip">Commissions: {{ number_format($totals['commissions'], 2) }}</span>
                <span class="summary-chip">Status: {{ strtoupper($batch->status) }}</span>
                @if($batch->status !== 'completed')
                <button class="btn btn-sm btn-success" wire:click="completeBatch">Mark Completed</button>
                @endif
            </div>
        </div>
    </div>

    <div class="section-card p-4 mb-3">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="p-3 border rounded h-100 bg-light">
                    <div class="text-muted small mb-1">Planned Material</div>
                    <div class="fw-bold">{{ number_format((float) ($batch->planned_material_ton ?? 0), 3) }} ton</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 border rounded h-100 bg-light">
                    <div class="text-muted small mb-1">Estimated Target</div>
                    <div class="fw-bold">{{ number_format((int) ($batch->target_qty ?? 0)) }} pcs</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 border rounded h-100 bg-light">
                    <div class="text-muted small mb-1">Estimated Days</div>
                    <div class="fw-bold">{{ number_format((int) ($batch->estimated_days ?? 0)) }} days</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 border rounded h-100 bg-light">
                    <div class="text-muted small mb-1">Workers Assigned</div>
                    <div class="fw-bold">{{ $batch->staffMembers->count() }} workers</div>
                </div>
            </div>
        </div>
    </div>

    <div class="section-card">
        <div class="row g-0">
            <div class="col-lg-3 p-3 left-tabs">
                <button class="tab-item {{ $activeTab === 'all' ? 'active' : '' }}" wire:click="$set('activeTab', 'all')">All Days</button>
                @foreach($dayTabs as $day)
                <button class="tab-item {{ $activeTab === 'day_' . $day->day_no ? 'active' : '' }}" wire:click="$set('activeTab', 'day_{{ $day->day_no }}')">
                    Day {{ $day->day_no }}
                </button>
                @endforeach

                @if($batch->status !== 'completed')
                <div class="mt-3">
                    <button class="btn btn-primary w-100" wire:click="openDayModal">
                        <i class="bi bi-plus-lg me-1"></i> Add Day Log
                    </button>
                </div>
                @endif
            </div>

            <div class="col-lg-9 p-4">
                @if($activeTab === 'all')
                <h5 class="fw-bold mb-3">All Days Summary</h5>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Day</th>
                                <th>Date</th>
                                <th class="text-end">Produced</th>
                                <th class="text-end">Expenses</th>
                                <th class="text-end">Commissions</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dayTabs as $day)
                            <tr>
                                <td class="fw-bold">Day {{ $day->day_no }}</td>
                                <td>{{ $day->work_date?->format('M d, Y') }}</td>
                                <td class="text-end">{{ number_format($day->produced_qty) }}</td>
                                <td class="text-end">{{ number_format($day->expense_amount, 2) }}</td>
                                <td class="text-end">
                                    {{ number_format(collect($day->staff_commissions ?? [])->sum('amount'), 2) }}

                                </td>
                                <td>
                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="button" class="btn btn-sm btn-light" wire:click="openViewModal({{ $day->id }})">View</button>
                                        <button type="button" class="btn btn-sm btn-outline-primary" wire:click="openEditModal({{ $day->id }})">Edit</button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" wire:click="confirmDeleteDay({{ $day->id }})">Delete</button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No day logs yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @elseif($selectedDay)
                <h5 class="fw-bold mb-3">Day {{ $selectedDay->day_no }} Details</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="p-3 border rounded">Date<br><b>{{ $selectedDay->work_date?->format('M d, Y') }}</b></div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 border rounded">Produced Items<br><b>{{ number_format($selectedDay->produced_qty) }}</b></div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 border rounded">Expense<br><b>{{ number_format($selectedDay->expense_amount, 2) }}</b></div>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="fw-bold">Materials Used</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Material</th>
                                    <th class="text-end">Qty (ton)</th>
                                    <th>Note</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($selectedDay->material_usages ?? [] as $usage)
                                <tr>
                                    <td>
                                        {{ optional($materials->firstWhere('id', $usage['material_id'] ?? null))->name ?? 'Unknown' }}
                                    </td>
                                    <td class="text-end">{{ number_format((float) ($usage['qty_ton'] ?? 0), 2) }}</td>
                                    <td>{{ $usage['note'] ?? '-' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No materials logged.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="fw-bold">Staff Commissions</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Staff</th>
                                    <th class="text-end">Commission</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($selectedDay->staff_commissions ?? [] as $row)
                                <tr>
                                    <td>{{ $row['name'] ?? ('Staff #' . ($row['user_id'] ?? '')) }}</td>
                                    <td class="text-end">${{ number_format((float) ($row['amount'] ?? 0), 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted">No commissions logged.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div>
                    <h6 class="fw-bold">Expense Note</h6>
                    <div class="border rounded p-3 bg-light">{{ $selectedDay->expense_note ?: 'No note added.' }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>

    @if($showDayModal)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content" style="border-radius: 14px;">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add Day {{ $day_no }} Log</h5>
                    <button type="button" class="btn-close" wire:click="closeDayModal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Work Date</label>
                            <input type="date" class="form-control" wire:model="work_date">
                            @error('work_date') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Produced Items</label>
                            <input type="number" class="form-control" min="0" wire:model.live="produced_qty">
                            @error('produced_qty') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Expense Amount</label>
                            <input type="number" step="0.01" class="form-control" min="0" wire:model="expense_amount">
                            @error('expense_amount') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Day No</label>
                            <input type="number" class="form-control" wire:model="day_no" readonly>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-bold mb-0">Expense Lines</label>
                            <button type="button" class="btn btn-sm btn-outline-primary" wire:click="addExpenseRow">+ Add Expense</button>
                        </div>
                        @foreach($expense_rows as $index => $expenseRow)
                        <div class="row g-2 mb-2 align-items-end">
                            <div class="col-md-4">
                                <input type="text" class="form-control" wire:model="expense_rows.{{ $index }}.label" placeholder="Electricity, Packing, Transport">
                            </div>
                            <div class="col-md-3">
                                <input type="number" class="form-control" wire:model="expense_rows.{{ $index }}.amount" min="0" step="0.01" placeholder="Amount">
                            </div>
                            <div class="col-md-4">
                                <input type="text" class="form-control" wire:model="expense_rows.{{ $index }}.note" placeholder="Note">
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-outline-danger w-100" wire:click="removeExpenseRow({{ $index }})">X</button>
                            </div>
                        </div>
                        @endforeach
                        @error('expense_rows') <span class="text-danger small d-block">{{ $message }}</span> @enderror
                    </div>

                    <div class="p-3 border rounded bg-light mt-3 mb-4">
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold">Expense Total</span>
                            <span class="fw-bold">Rs. {{ number_format($expenseRowsTotal, 2) }}</span>
                        </div>
                    </div>

                    <div class="alert alert-info border-0 mb-4">
                        <div class="fw-bold mb-1">Commission Calculation</div>
                        <div class="small mb-1">
                            Threshold: {{ number_format($commissionSettings['threshold_items'] ?? 10000) }} items |
                            Rate up to threshold: ${{ number_format($commissionSettings['rate_upto_threshold'] ?? 10, 2) }} |
                            Rate after threshold: ${{ number_format($commissionSettings['rate_after_threshold'] ?? 15, 2) }}
                        </div>
                        <div class="small fw-semibold">
                            Total commission for this log: ${{ number_format($calculatedTotalCommission, 2) }}
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Expense Note</label>
                        <textarea class="form-control" rows="2" wire:model="expense_note"></textarea>
                        @error('expense_note') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <h6 class="fw-bold mb-2">Materials Used (tons)</h6>
                    @foreach($material_rows as $index => $row)
                    <div class="row g-2 mb-2">
                        <div class="col-md-5">
                            <select class="form-select" wire:model="material_rows.{{ $index }}.material_id">
                                <option value="">Select material</option>
                                @foreach($materials as $material)
                                <option value="{{ $material->id }}">{{ $material->name }} ({{ $material->code }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="number" step="0.01" min="0" class="form-control" placeholder="Qty ton" wire:model="material_rows.{{ $index }}.qty_ton">
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" placeholder="Note" wire:model="material_rows.{{ $index }}.note">
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-light w-100" wire:click="removeMaterialRow({{ $index }})"><i class="bi bi-x"></i></button>
                        </div>
                    </div>
                    @endforeach
                    <button type="button" class="btn btn-sm btn-outline-primary" wire:click="addMaterialRow">+ Add Material Row</button>

                    <h6 class="fw-bold mt-4 mb-2">Staff Commissions</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Staff</th>
                                    <th class="text-end">Commission</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($commission_rows as $index => $row)
                                <tr>
                                    <td>{{ $row['name'] }}</td>
                                    <td>
                                        <input type="number" step="0.01" min="0" class="form-control text-end" wire:model="commission_rows.{{ $index }}.amount">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="small text-muted mt-2">Commission rows are pre-filled from the configured rules and can still be adjusted before saving.</div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" wire:click="closeDayModal">Cancel</button>
                    <button class="btn btn-primary" wire:click="saveDayLog">Save Day Log</button>
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
                    <h5 class="modal-title fw-bold">Daily Log - Day {{ $viewDay->day_no }}</h5>
                    <button type="button" class="btn-close" wire:click="closeViewModal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <div class="p-3 border rounded">Date<br><b>{{ $viewDay->work_date?->format('M d, Y') }}</b></div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded">Produced<br><b>{{ number_format($viewDay->produced_qty) }}</b></div>
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
                        <div class="fw-bold mb-2">Expense Note</div>
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
                    <p class="mb-0 text-muted">This will remove the log, expense lines, materials, and commissions.</p>
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