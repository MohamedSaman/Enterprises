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
            border: 1px solid rgba(30, 41, 59, 0.08);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
        }

        .left-tabs {
            min-height: 520px;
            border-right: 1px solid #e2e8f0;
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
            transition: all 0.2s ease;
        }

        .tab-item.active {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #0284c7;
        }

        .summary-chip {
            border-radius: 20px;
            padding: 0.55rem 1.1rem;
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            font-weight: 700;
            color: #0284c7;
            box-shadow: 0 2px 6px rgba(2, 132, 199, 0.15);
        }

        .estimate-card {
            margin-top: 1.2rem;
            border: 1px solid #dbeafe;
            border-radius: 14px;
            background:
                radial-gradient(circle at top right, rgba(14, 165, 233, 0.08), transparent 32%),
                linear-gradient(135deg, #f8fbff 0%, #eef8ff 100%);
            padding: 1rem;
        }

        .estimate-card-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 0.8rem;
            flex-wrap: wrap;
        }

        .estimate-card-title {
            font-size: 1rem;
            font-weight: 900;
            color: #0f172a;
            margin: 0;
        }

        .estimate-card-sub {
            font-size: 0.8rem;
            color: #475569;
            margin-top: 0.2rem;
        }

        .estimate-toggle {
            border: 0;
            border-radius: 10px;
            padding: 0.55rem 0.95rem;
            font-size: 0.78rem;
            font-weight: 800;
            color: #ffffff;
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            box-shadow: 0 6px 16px rgba(2, 132, 199, 0.24);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            cursor: pointer;
        }

        .estimate-toggle:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 20px rgba(2, 132, 199, 0.28);
        }

        .estimate-toggle i {
            transition: transform 0.25s ease;
        }

        .estimate-collapse {
            max-height: 0;
            opacity: 0;
            overflow: hidden;
            transform: translateY(-6px);
            transition: max-height 0.45s ease, opacity 0.3s ease, transform 0.3s ease, margin-top 0.3s ease;
            margin-top: 0;
        }

        .estimate-collapse.show {
            max-height: 700px;
            opacity: 1;
            transform: translateY(0);
            margin-top: 0.9rem;
        }

        .estimate-toggle[aria-expanded="true"] i {
            transform: rotate(180deg);
        }

        .estimate-table-wrap {
            border: 1px solid #cfe3fb;
            border-radius: 12px;
            overflow: hidden;
            background: #ffffff;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6);
        }

        .estimate-table thead th {
            background: linear-gradient(135deg, #eff6ff 0%, #e0f2fe 100%);
            color: #0f172a;
            font-size: 0.82rem;
            font-weight: 900;
            letter-spacing: 0.02em;
            border-bottom: 1px solid #dbeafe;
        }

        .estimate-table td,
        .estimate-table th {
            padding: 0.62rem 0.78rem;
        }

        .estimate-table tbody tr:nth-child(odd) {
            background: #fbfdff;
        }

        .estimate-table tbody tr:hover {
            background: #f0f9ff;
        }

        .estimate-table tfoot th {
            background: #f8fafc;
            font-size: 0.84rem;
            border-top: 1px solid #dbeafe;
        }

        .summary-progress-card {
            border: 1px solid #dbeafe;
            border-radius: 12px;
            padding: 0.9rem;
            height: 100%;
            background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.04);
        }

        .summary-progress-card .label {
            font-size: 0.73rem;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 0.4rem;
        }

        .summary-progress-card .value {
            font-size: 1.05rem;
            font-weight: 900;
            color: #0f172a;
            margin-bottom: 0.2rem;
        }

        .summary-progress-card .sub {
            font-size: 0.77rem;
            color: #64748b;
            margin-bottom: 0.45rem;
        }

        .mini-progress-track {
            height: 8px;
            border-radius: 999px;
            background: #e2e8f0;
            overflow: hidden;
            box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.08);
        }

        .mini-progress-fill {
            height: 100%;
            border-radius: inherit;
            transition: width 0.3s ease;
        }

        .mini-progress-fill.material {
            background: linear-gradient(90deg, #0284c7 0%, #0ea5e9 100%);
        }

        .mini-progress-fill.target {
            background: linear-gradient(90deg, #2563eb 0%, #1d4ed8 100%);
        }

        .mini-progress-fill.days {
            background: linear-gradient(90deg, #8b5cf6 0%, #7c3aed 100%);
        }

        .mini-progress-fill.workers {
            background: linear-gradient(90deg, #10b981 0%, #059669 100%);
        }

        .mini-progress-meta {
            margin-top: 0.35rem;
            display: flex;
            justify-content: space-between;
            gap: 0.4rem;
            font-size: 0.73rem;
            font-weight: 700;
            color: #475569;
        }
    </style>
    @endpush

    <div class="section-card p-4 mb-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4 class="fw-bold mb-1">{{ $batch->batch_code }}</h4>
                <div class="text-muted small">
                    Material: {{ $batch->material->name ?? '-' }} | Supervisor: {{ $batch->supervisor->name ?? '-' }}
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="summary-chip">Produced: {{ number_format($totals['produced']) }}</span>
                <span class="summary-chip">Expenses: {{ number_format($totals['expenses'], 2) }}</span>
                <span class="summary-chip">Commissions: {{ number_format($totals['commissions'], 2) }}</span>
                <span class="summary-chip">Status: {{ strtoupper($batch->status) }}</span>
            </div>
        </div>
    </div>

    <div class="section-card p-4 mb-3">
        @php
        $materialAvailableTon = max((float) collect($estimatedTargetSizeBreakdown)->sum('ton'), (float) ($batch->planned_material_ton ?? 0));
        $materialUsedTon = (float) ($totals['approx_used_ton'] ?? 0);
        $materialProgress = $materialAvailableTon > 0 ? min(100, round(($materialUsedTon / $materialAvailableTon) * 100)) : 0;

        $targetQty = max(1, (int) ($batch->target_qty ?? 0));
        $producedQty = (int) ($totals['produced'] ?? 0);
        $targetProgress = min(100, round(($producedQty / $targetQty) * 100));

        $estimatedDays = max(1, (int) ($batch->estimated_days ?? 0));
        $finishedDays = (int) ($totals['days'] ?? 0);
        $daysProgress = min(100, round(($finishedDays / $estimatedDays) * 100));

        $workerCount = (int) $batch->staffMembers->count();
        $hasSupervisor = !empty($batch->supervisor_id);
        $teamReadyUnits = $workerCount + ($hasSupervisor ? 1 : 0);
        $workersProgress = min(100, round(($teamReadyUnits / 2) * 100));
        @endphp
        <div class="row g-3">
            <div class="col-md-3">
                <div class="summary-progress-card">
                    <div class="label">Product Material</div>
                    <div class="value">{{ number_format($materialUsedTon, 3) }} / {{ number_format($materialAvailableTon, 3) }} ton</div>
                    <div class="sub">Approx used from produced size-wise counts</div>
                    <div class="mini-progress-track">
                        <div class="mini-progress-fill material" style="width: {{ $materialProgress }}%;"></div>
                    </div>
                    <div class="mini-progress-meta">
                        <span>Available: {{ number_format(max($materialAvailableTon - $materialUsedTon, 0), 3) }} ton</span>
                        <span>{{ $materialProgress }}%</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-progress-card">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 0.8rem; margin-bottom: 0.6rem; flex-wrap: wrap;">
                        <div class="label" style="flex-shrink: 0;">Estimated Target</div>
                        <div style="font-size: 0.85rem; font-weight: 800; color: #fff; background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); padding: 0.4rem 0.8rem; border-radius: 8px; white-space: nowrap; box-shadow: 0 2px 6px rgba(37, 99, 235, 0.3);">
                            ⚡ Daily: {{ number_format($estimatedDailyTarget) }} pcs
                        </div>
                    </div>
                    <div class="value">{{ number_format($producedQty) }} / {{ number_format($targetQty) }} pcs</div>
                    <div class="sub">Produced vs target quantity</div>
                    <div class="mini-progress-track">
                        <div class="mini-progress-fill target" style="width: {{ $targetProgress }}%;"></div>
                    </div>
                    <div class="mini-progress-meta">
                        <span>Remaining: {{ number_format(max($targetQty - $producedQty, 0)) }} pcs</span>
                        <span>{{ $targetProgress }}%</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-progress-card">
                    <div class="label">Estimated Days</div>
                    <div class="value">{{ number_format($finishedDays) }} / {{ number_format($estimatedDays) }} days</div>
                    <div class="sub">Completed days vs estimated</div>
                    <div class="mini-progress-track">
                        <div class="mini-progress-fill days" style="width: {{ $daysProgress }}%;"></div>
                    </div>
                    <div class="mini-progress-meta">
                        <span>Remaining: {{ number_format(max($estimatedDays - $finishedDays, 0)) }} days</span>
                        <span>{{ $daysProgress }}%</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-progress-card">
                    <div class="label">Workers</div>
                    <div class="value">{{ $workerCount }} workers{{ $hasSupervisor ? ' + supervisor' : '' }}</div>
                    <div class="sub">Team assignment readiness</div>
                    <div class="mini-progress-track">
                        <div class="mini-progress-fill workers" style="width: {{ $workersProgress }}%;"></div>
                    </div>
                    <div class="mini-progress-meta">
                        <span>{{ $hasSupervisor ? 'Supervisor assigned' : 'Supervisor missing' }}</span>
                        <span>{{ $workersProgress }}%</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="estimate-card" id="estimate-target-card">
            <div class="estimate-card-head">
                <div>
                    <h6 class="estimate-card-title">Estimated Target Size Wise</h6>
                    <div class="estimate-card-sub">Click to view detailed size allocation and totals</div>
                </div>
                <button
                    type="button"
                    class="estimate-toggle"
                    id="estimate-target-toggle"
                    aria-expanded="false"
                    aria-controls="estimate-target-collapse">
                    <span class="estimate-toggle-label">View Breakdown</span> <i class="bi bi-chevron-down ms-1"></i>
                </button>
            </div>

            <div class="estimate-collapse" id="estimate-target-collapse">
                <div class="estimate-table-wrap">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle mb-0 estimate-table">
                            <thead>
                                <tr>
                                    <th>Size</th>
                                    <th class="text-end">Material (ton)</th>
                                    <th class="text-end">Estimated Target</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($estimatedTargetSizeBreakdown as $row)
                                <tr>
                                    <td class="fw-bold">{{ $row['size'] }}</td>
                                    <td class="text-end">{{ number_format((float) $row['ton'], 3) }}</td>
                                    <td class="text-end fw-bold">{{ number_format((int) $row['estimated']) }} pcs</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No size-wise estimate data available for this batch.</td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if(!empty($estimatedTargetSizeBreakdown))
                            <tfoot>
                                <tr>
                                    <th>Total</th>
                                    <th class="text-end">{{ number_format(collect($estimatedTargetSizeBreakdown)->sum('ton'), 3) }}</th>
                                    <th class="text-end">{{ number_format((int) collect($estimatedTargetSizeBreakdown)->sum('estimated')) }} pcs</th>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
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
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No day logs yet.</td>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleButton = document.getElementById('estimate-target-toggle');
        const collapsePanel = document.getElementById('estimate-target-collapse');

        if (!toggleButton || !collapsePanel) {
            return;
        }

        toggleButton.addEventListener('click', function() {
            const isOpen = collapsePanel.classList.toggle('show');
            toggleButton.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            const label = toggleButton.querySelector('.estimate-toggle-label');
            if (label) {
                label.textContent = isOpen ? 'Hide Breakdown' : 'View Breakdown';
            }
        });
    });
</script>