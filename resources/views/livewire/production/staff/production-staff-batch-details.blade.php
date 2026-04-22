<div class="dashboard-wrapper">
    @push('styles')
    <style>
        .dashboard-wrapper {
            min-height: 100vh;
            padding: 1.2rem 0 2rem;
            background:
                radial-gradient(circle at top left, rgba(14, 165, 233, 0.12), transparent 30%),
                radial-gradient(circle at top right, rgba(59, 130, 246, 0.10), transparent 24%),
                linear-gradient(180deg, #f8fbff 0%, #eef4fb 100%);
        }

        .panel {
            background: rgba(255, 255, 255, 0.94);
            border: 1px solid rgba(148, 163, 184, 0.22);
            border-radius: 18px;
            padding: 1.25rem;
            box-shadow: 0 14px 36px rgba(15, 23, 42, 0.06);
            backdrop-filter: blur(10px);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .stat-card {
            position: relative;
            overflow: hidden;
            border: 1px solid #dbe7f3;
            border-radius: 16px;
            padding: 1rem;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.04);
        }

        .stat-label {
            font-size: 0.75rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 0.35rem;
        }

        .stat-value {
            font-size: 1.6rem;
            font-weight: 800;
            color: #0f172a;
        }

        .stat-subtext {
            font-size: 0.75rem;
            color: #64748b;
            margin-top: 0.2rem;
        }

        .stat-progress-track {
            height: 6px;
            background: #e2e8f0;
            border-radius: 999px;
            overflow: hidden;
            margin-top: 0.6rem;
        }

        .stat-progress-fill {
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(90deg, #0ea5e9 0%, #2563eb 100%);
            transition: width 0.3s ease;
        }

        .stat-progress-text {
            font-size: 0.7rem;
            color: #475569;
            margin-top: 0.35rem;
            font-weight: 600;
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

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.35rem 0.75rem;
            border-radius: 999px;
            background: #e0f2fe;
            color: #0369a1;
            font-size: 0.72rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .hero-title {
            font-size: 1.35rem;
            font-weight: 900;
            color: #0f172a;
            margin-bottom: 0.25rem;
        }

        .hero-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 0.75rem;
        }

        .meta-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.35rem 0.7rem;
            border-radius: 999px;
            background: #f1f5f9;
            color: #334155;
            font-size: 0.8rem;
            font-weight: 700;
        }

        .section-title {
            font-size: 0.9rem;
            font-weight: 900;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 0.85rem;
        }

        .summary-table thead th {
            font-size: 0.76rem;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: #64748b;
        }

        .summary-table tbody td {
            padding-top: 0.9rem;
            padding-bottom: 0.9rem;
        }

        .day-card {
            border: 1px solid #dbe7f3;
            border-radius: 14px;
            padding: 0.95rem;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        }

        .day-card .label {
            font-size: 0.72rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 800;
            margin-bottom: 0.2rem;
        }

        .day-card .value {
            font-weight: 800;
            color: #0f172a;
        }

        .modal-content {
            border: 0;
            box-shadow: 0 22px 60px rgba(15, 23, 42, 0.18);
        }

        .modal-header {
            background: linear-gradient(135deg, #eff6ff 0%, #ffffff 100%);
            border-bottom: 1px solid #e2e8f0;
        }

        .modal-footer {
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
        }

        .performance-bar {
            min-width: 220px;
        }

        .performance-track {
            height: 8px;
            background: #e2e8f0;
            border-radius: 999px;
            overflow: hidden;
            position: relative;
            margin-bottom: 0.35rem;
        }

        .performance-fill {
            height: 100%;
            border-radius: inherit;
            transition: width 0.3s ease;
        }

        .performance-fill.on-target {
            background: linear-gradient(90deg, #10b981 0%, #059669 100%);
        }

        .performance-fill.exceeded {
            background: linear-gradient(90deg, #3b82f6 0%, #1d4ed8 100%);
        }

        .performance-fill.below-target {
            background: linear-gradient(90deg, #f59e0b 0%, #d97706 100%);
        }

        .performance-label {
            font-size: 0.72rem;
            font-weight: 700;
            color: #475569;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .performance-label .status-text {
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-size: 0.65rem;
            font-weight: 800;
            text-transform: uppercase;
        }

        .performance-label .status-text.on-target {
            background: #d1fae5;
            color: #065f46;
        }

        .performance-label .status-text.exceeded {
            background: #dbeafe;
            color: #0c2d6b;
        }

        .performance-label .status-text.below-target {
            background: #fed7aa;
            color: #7c2d12;
        }

        @media (max-width: 900px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    @endpush


    <div class="stats-grid">
        <!-- Card 1: Work Days Progress -->
        <div class="stat-card">
            @php
            $workedDays = (int) $totals['days'];
            $estimatedDays = max(1, (int) ($batch->estimated_days ?? 1));
            $dayProgressPercent = min(100, round(($workedDays / $estimatedDays) * 100));
            @endphp
            <div class="stat-label">Work Days</div>
            <div class="stat-value">{{ $workedDays }} / {{ $estimatedDays }}</div>
            <div class="stat-subtext">days completed</div>
            <div class="stat-progress-track">
                <div class="stat-progress-fill" style="width: {{ $dayProgressPercent }}%;"></div>
            </div>
            <div class="stat-progress-text">{{ $dayProgressPercent }}% complete</div>
        </div>

        <!-- Card 2: Target vs Achieved -->
        <div class="stat-card">
            @php
            $achieved = (int) $totals['produced'];
            $target = max(1, (int) $batch->target_qty);
            $targetProgressPercent = min(100, round(($achieved / $target) * 100));
            @endphp
            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 0.6rem; margin-bottom: 0.4rem;">
                <div class="stat-label">Target Progress</div>
                <div style="font-size: 0.75rem; font-weight: 800; color: #fff; background: linear-gradient(135deg, #10b981 0%, #059669 100%); padding: 0.3rem 0.6rem; border-radius: 6px; white-space: nowrap;">
                    Daily: {{ number_format($estimatedDailyTarget) }} pcs
                </div>
            </div>
            <div class="stat-value">{{ number_format($achieved) }}</div>
            <div class="stat-subtext">of {{ number_format($target) }} target</div>
            <div class="stat-progress-track">
                <div class="stat-progress-fill" style="width: {{ $targetProgressPercent }}%; background: linear-gradient(90deg, #10b981 0%, #059669 100%);"></div>
            </div>
            <div class="stat-progress-text">{{ $targetProgressPercent }}% achieved</div>
        </div>

        <!-- Card 3: Size-wise Production -->
        <div class="stat-card">
            @php
            $sizeS = (int) $totals['produced_s'];
            $sizeM = (int) $totals['produced_m'];
            $sizeL = (int) $totals['produced_l'];
            $totalBySize = max(1, $sizeS + $sizeM + $sizeL);
            $sizeSPercent = round(($sizeS / $totalBySize) * 100, 2);
            $sizeMPercent = round(($sizeM / $totalBySize) * 100, 2);
            $sizeLPercent = max(0, 100 - $sizeSPercent - $sizeMPercent);
            @endphp
            <div class="stat-label">Production Mix</div>
            <div style="font-size: 0.85rem; line-height: 1.4; margin-top: 0.5rem;">
                <div style="color: #0f172a; font-weight: 700;">S: <span style="color: #0369a1;">{{ number_format($sizeS) }}</span></div>
                <div style="color: #0f172a; font-weight: 700;">M: <span style="color: #059669;">{{ number_format($sizeM) }}</span></div>
                <div style="color: #0f172a; font-weight: 700;">L: <span style="color: #d97706;">{{ number_format($sizeL) }}</span></div>
            </div>
            <div class="stat-progress-track" style="margin-top: 0.6rem; display: flex; gap: 0; background: #e2e8f0;">
                <div style="width: {{ $sizeSPercent }}%; background: linear-gradient(90deg, #0ea5e9 0%, #2563eb 100%); height: 100%;"></div>
                <div style="width: {{ $sizeMPercent }}%; background: linear-gradient(90deg, #10b981 0%, #059669 100%); height: 100%;"></div>
                <div style="width: {{ $sizeLPercent }}%; background: linear-gradient(90deg, #f59e0b 0%, #d97706 100%); height: 100%;"></div>
            </div>
            <div class="stat-progress-text">Total: {{ number_format($totalBySize) }} items</div>
        </div>

        <!-- Card 4: Expenses -->
        <div class="stat-card">
            <div class="stat-label">Total Expenses</div>
            <div class="stat-value">Rs. {{ number_format($totals['expense'], 2) }}</div>
            <div class="stat-subtext">logged expenses</div>
            <div class="stat-progress-track">
                <div class="stat-progress-fill" style="width: 100%; background: linear-gradient(90deg, #ec4899 0%, #be185d 100%);"></div>
            </div>
            <div class="stat-progress-text">Total tracked</div>
        </div>
    </div>
    <div class="panel mb-3">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <span class="hero-badge mb-2">Daily Production Log</span>
                <div class="hero-title">Batch {{ $batch->batch_code }}</div>
                <div class="text-muted">Target {{ number_format($batch->target_qty) }} | Status: {{ ucfirst($batch->status) }}</div>
                <div class="hero-meta">
                    <span class="meta-pill">Supervisor: {{ $batch->supervisor->name ?? '-' }}</span>
                    <span class="meta-pill">Planned: {{ number_format((float) ($batch->planned_material_ton ?? 0), 3) }} ton</span>
                    <span class="meta-pill">Days: {{ number_format((int) ($batch->estimated_days ?? 0)) }}</span>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('production.staff.batches') }}" class="btn btn-light">Back</a>
                <button class="btn btn-primary" wire:click="openDayModal">
                    <i class="bi bi-plus-lg me-1"></i> Add Daily Log
                </button>
            </div>
        </div>
    </div>



    <div class="panel">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <div>
                <div class="section-title mb-1">Daily Logs</div>
                <h5 class="fw-bold mb-0">Production entries for this batch</h5>
            </div>
            <div class="text-muted small">S, M, and L are tracked separately for each day.</div>
        </div>
        <div class="table-responsive">
            <table class="table align-middle summary-table mb-0">
                <thead class="table-light">
                    <tr class="small text-uppercase">
                        <th>Day</th>
                        <th>Date</th>
                        <th class="text-end">S</th>
                        <th class="text-end">M</th>
                        <th class="text-end">L</th>
                        <th class="text-end">Total</th>
                        <th class="text-end">Estimate</th>
                        <th>Performance vs Target</th>
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
                        <td class="text-end fw-bold">{{ number_format($log->produced_s_qty ?? 0) }}</td>
                        <td class="text-end fw-bold">{{ number_format($log->produced_m_qty ?? 0) }}</td>
                        <td class="text-end fw-bold">{{ number_format($log->produced_l_qty ?? 0) }}</td>
                        <td class="text-end fw-bold">{{ number_format($log->produced_qty) }}</td>
                        <td class="text-end">
                            <span class="badge" style="background-color: #dbeafe; color: #0369a1;">{{ number_format((int) ($log->dynamic_estimate_target ?? 0)) }}</span>
                        </td>
                        <td>
                            @php
                            $produced = (int) $log->produced_qty;
                            $estimate = max(1, (int) ($log->dynamic_estimate_target ?? 0));
                            $percentage = ($produced / $estimate) * 100;
                            $displayPercent = min(100, round($percentage));

                            if ($percentage >= 100) {
                            $status = 'exceeded';
                            $statusText = '↑ Exceeded';
                            } elseif ($percentage >= 90) {
                            $status = 'on-target';
                            $statusText = '✓ On Target';
                            } else {
                            $status = 'below-target';
                            $statusText = '↓ Below';
                            }
                            @endphp
                            <div class="performance-bar">
                                <div class="performance-track">
                                    <div class="performance-fill {{ $status }}" style="width: {{ $displayPercent }}%;"></div>
                                </div>
                                <div class="performance-label">
                                    <span>{{ round($percentage) }}% of {{ number_format($estimate) }}</span>
                                    <span class="status-text {{ $status }}">{{ $statusText }}</span>
                                </div>
                            </div>
                        </td>
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
                        <td colspan="11" class="text-center   py-3   text-muted">No daily logs added yet.</td>
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
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Day No</label>
                            <input type="number" class="form-control" wire:model="day_no" min="1">
                            @error('day_no') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Work Date</label>
                            <input type="date" class="form-control" wire:model="work_date">
                            @error('work_date') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Size S</label>
                            <input type="number" class="form-control" wire:model.live="produced_s_qty" min="0">
                            @error('produced_s_qty') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Size M</label>
                            <input type="number" class="form-control" wire:model.live="produced_m_qty" min="0">
                            @error('produced_m_qty') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Size L</label>
                            <input type="number" class="form-control" wire:model.live="produced_l_qty" min="0">
                            @error('produced_l_qty') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mt-3 p-3 border rounded bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold">Total Produced</span>
                            <span class="fw-bold">{{ number_format($produced_qty) }} items</span>
                        </div>
                        <div class="small text-muted mt-1">Total is auto-calculated from S, M, and L counts.</div>
                    </div>

                    <div class="mt-2 p-3 border rounded" style="background: #fef3c7; border-color: #fcd34d;">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold">Estimated Daily Target</span>
                            <span class="fw-bold" style="color: #d97706;">{{ number_format($estimatedDailyTarget) }} items/day</span>
                        </div>
                        <div class="small" style="color: #92400e; margin-top: 0.4rem;">Your daily goal based on {{ number_format($batch->target_qty) }} total ÷ {{ number_format($batch->estimated_days) }} days</div>
                    </div>

                    <div class="mt-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-bold mb-0">Expense Lines</label>
                            <button type="button" class="btn btn-sm btn-outline-primary" wire:click="addExpenseRow">+ Add Expense</button>
                        </div>
                        <div class="small text-muted mb-2">Expenses are optional. Click the"+ Add Expense" button to add expense lines when needed.</div>
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
                @php
                $viewEstimateTarget = (int) (($dayLogs->firstWhere('id', $viewDay->id)->dynamic_estimate_target ?? $estimatedDailyTarget) ?: 0);
                @endphp
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">View Daily Log - Day {{ $viewDay->day_no }}</h5>
                    <button type="button" class="btn-close" wire:click="closeViewModal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <div class="day-card">
                                <div class="label">Date</div>
                                <div class="value">{{ optional($viewDay->work_date)->format('Y-m-d') }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="day-card">
                                <div class="label">Produced S / M / L</div>
                                <div class="value">{{ number_format($viewDay->produced_s_qty ?? 0) }} / {{ number_format($viewDay->produced_m_qty ?? 0) }} / {{ number_format($viewDay->produced_l_qty ?? 0) }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="day-card">
                                <div class="label">Total Produced</div>
                                <div class="value">{{ number_format($viewDay->produced_qty) }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="day-card">
                                <div class="label">Daily Estimate</div>
                                <div class="value" style="color: #0f172a;">{{ number_format($viewEstimateTarget) }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="day-card">
                                <div class="label">Total Expense</div>
                                <div class="value">Rs. {{ number_format($viewDay->expense_amount, 2) }}</div>
                            </div>
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