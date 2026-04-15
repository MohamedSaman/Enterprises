<div class="salary-report-shell">
    @push('styles')
    <style>
        .salary-report-shell {
            background: linear-gradient(135deg, #f5f7fb 0%, #f0f4fa 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }

        .panel-card {
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid rgba(30, 41, 59, 0.08);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
        }

        .hero-title {
            font-size: 1.35rem;
            font-weight: 800;
            color: #0f172a;
        }

        .summary-stat {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1rem;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        }

        .summary-stat .label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #64748b;
            font-weight: 800;
        }

        .summary-stat .value {
            font-size: 1.4rem;
            font-weight: 800;
            color: #0f172a;
        }

        .calc-table td,
        .calc-table th {
            vertical-align: middle;
        }

        .soft-note {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 12px;
            padding: 1rem;
            color: #475569;
        }
    </style>
    @endpush

    <div class="panel-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <div class="hero-title">Production Cost Statement</div>
                <div class="text-muted">Salary and expense calculation for the selected production batch</div>
                <a href="{{ route('production.admin.expenses') }}" class="small text-decoration-none">Manage fixed-cost expenses batch wise</a>
            </div>
            <div class="d-flex gap-2 flex-wrap" style="min-width: 340px; max-width: 100%;">
                <div class="flex-grow-1">
                    <label class="form-label small fw-bold text-uppercase text-muted mb-1">Batch</label>
                    <select class="form-select" wire:model.live="selectedBatchId">
                        @foreach($batches as $batch)
                        <option value="{{ $batch->id }}">{{ $batch->batch_code }} - {{ $batch->size }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-grow-1">
                    <label class="form-label small fw-bold text-uppercase text-muted mb-1">Manufactured Pcs</label>
                    <input type="number" min="0" class="form-control" wire:model.live="manufactured_qty">
                </div>
            </div>
        </div>
    </div>

    @if($costStatement)
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="summary-stat h-100">
                <div class="label">Manufactured Qty</div>
                <div class="value">{{ number_format($costStatement['produced_qty']) }} pcs</div>
                <div class="text-muted small">Target {{ number_format((int) $costStatement['batch']->target_qty) }} pcs</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-stat h-100">
                <div class="label">Material Cost</div>
                <div class="value">Rs. {{ number_format($costStatement['material_cost'], 2) }}</div>
                <div class="text-muted small">{{ number_format($costStatement['planned_ton'], 3) }} ton planned</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-stat h-100">
                <div class="label">Batch Expenses</div>
                <div class="value">Rs. {{ number_format($costStatement['batch_expenses'], 2) }}</div>
                <div class="text-muted small">Daily expenses logged by supervisor</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-stat h-100">
                <div class="label">Net Profit</div>
                <div class="value {{ $costStatement['profit'] >= 0 ? 'text-success' : 'text-danger' }}">Rs. {{ number_format($costStatement['profit'], 2) }}</div>
                <div class="text-muted small">Revenue minus total cost</div>
            </div>
        </div>
    </div>

    <div class="panel-card p-4 mb-4">
        <div class="row g-3">
            <div class="col-lg-7">
                <h5 class="fw-bold mb-3">Cost Breakdown</h5>
                <div class="table-responsive">
                    <table class="table table-bordered calc-table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Item</th>
                                <th class="text-end">Amount</th>
                                <th class="text-end">Per Pcs</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Material Cost</td>
                                <td class="text-end">Rs. {{ number_format($costStatement['material_cost'], 2) }}</td>
                                <td class="text-end">Rs. {{ number_format($costStatement['material_per_piece'], 2) }}</td>
                            </tr>
                            <tr>
                                <td>Electricity</td>
                                <td class="text-end">Rs. {{ number_format((float) ($costStatement['fixed_expenses']['Electricity'] ?? 0), 2) }}</td>
                                <td class="text-end">Rs. {{ number_format($costStatement['produced_qty'] > 0 ? ((float) ($costStatement['fixed_expenses']['Electricity'] ?? 0) / $costStatement['produced_qty']) : 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Packing Expenses</td>
                                <td class="text-end">Rs. {{ number_format((float) ($costStatement['fixed_expenses']['Packing Expenses'] ?? 0), 2) }}</td>
                                <td class="text-end">Rs. {{ number_format($costStatement['produced_qty'] > 0 ? ((float) ($costStatement['fixed_expenses']['Packing Expenses'] ?? 0) / $costStatement['produced_qty']) : 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Depreciation</td>
                                <td class="text-end">Rs. {{ number_format((float) ($costStatement['fixed_expenses']['Depreciation'] ?? 0), 2) }}</td>
                                <td class="text-end">Rs. {{ number_format($costStatement['produced_qty'] > 0 ? ((float) ($costStatement['fixed_expenses']['Depreciation'] ?? 0) / $costStatement['produced_qty']) : 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Transport</td>
                                <td class="text-end">Rs. {{ number_format((float) ($costStatement['fixed_expenses']['Transport'] ?? 0), 2) }}</td>
                                <td class="text-end">Rs. {{ number_format($costStatement['produced_qty'] > 0 ? ((float) ($costStatement['fixed_expenses']['Transport'] ?? 0) / $costStatement['produced_qty']) : 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Hosting Charges</td>
                                <td class="text-end">Rs. {{ number_format((float) ($costStatement['fixed_expenses']['Hosting Charges'] ?? 0), 2) }}</td>
                                <td class="text-end">Rs. {{ number_format($costStatement['produced_qty'] > 0 ? ((float) ($costStatement['fixed_expenses']['Hosting Charges'] ?? 0) / $costStatement['produced_qty']) : 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Sundry Expenses</td>
                                <td class="text-end">Rs. {{ number_format((float) ($costStatement['fixed_expenses']['Sundry Expenses'] ?? 0), 2) }}</td>
                                <td class="text-end">Rs. {{ number_format($costStatement['produced_qty'] > 0 ? ((float) ($costStatement['fixed_expenses']['Sundry Expenses'] ?? 0) / $costStatement['produced_qty']) : 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Supervisor Daily Expenses</td>
                                <td class="text-end">Rs. {{ number_format((float) ($costStatement['fixed_expenses']['Supervisor Daily Expenses'] ?? 0), 2) }}</td>
                                <td class="text-end">Rs. {{ number_format($costStatement['produced_qty'] > 0 ? ((float) ($costStatement['fixed_expenses']['Supervisor Daily Expenses'] ?? 0) / $costStatement['produced_qty']) : 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Fixed Cost Total</td>
                                <td class="text-end fw-bold">Rs. {{ number_format($costStatement['fixed_cost'], 2) }}</td>
                                <td class="text-end fw-bold">Rs. {{ number_format($costStatement['produced_qty'] > 0 ? $costStatement['fixed_cost'] / $costStatement['produced_qty'] : 0, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-lg-5">
                <h5 class="fw-bold mb-3">Salary Calculation</h5>
                <div class="table-responsive mb-3">
                    <table class="table table-bordered calc-table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Component</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Basic Salary</td>
                                <td class="text-end">Rs. {{ number_format($costStatement['basic_salary'], 2) }}</td>
                            </tr>
                            <tr>
                                <td>Target Commission</td>
                                <td class="text-end">Rs. {{ number_format($costStatement['target_commission'], 2) }}</td>
                            </tr>
                            <tr>
                                <td>Extra Salary</td>
                                <td class="text-end">Rs. {{ number_format($extra_salary, 2) }}</td>
                            </tr>
                            <tr class="table-warning">
                                <td class="fw-bold">Gross Salary</td>
                                <td class="text-end fw-bold">Rs. {{ number_format($costStatement['gross_salary'], 2) }}</td>
                            </tr>
                            <tr>
                                <td>EPF Employee ({{ $epf_employee_rate }}%)</td>
                                <td class="text-end">Rs. {{ number_format($costStatement['employee_epf'], 2) }}</td>
                            </tr>
                            <tr>
                                <td>Net Salary Payable</td>
                                <td class="text-end fw-bold">Rs. {{ number_format($costStatement['net_salary'], 2) }}</td>
                            </tr>
                            <tr>
                                <td>EPF Employer ({{ $epf_employer_rate }}%)</td>
                                <td class="text-end">Rs. {{ number_format($costStatement['employer_epf'], 2) }}</td>
                            </tr>
                            <tr>
                                <td>ETF ({{ $etf_rate }}%)</td>
                                <td class="text-end">Rs. {{ number_format($costStatement['etf'], 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="soft-note">
                    <div class="fw-bold mb-1">Commission Rules</div>
                    <div>First {{ number_format($commissionSettings['threshold_items']) }} pcs at Rs. {{ number_format($commissionSettings['rate_upto_threshold'], 2) }} each.</div>
                    <div>After threshold at Rs. {{ number_format($commissionSettings['rate_after_threshold'], 2) }} each.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="panel-card p-4 mb-4">
        <h5 class="fw-bold mb-3">Profit & Unit Analysis</h5>
        <div class="row g-3">
            <div class="col-md-4">
                <div class="summary-stat h-100">
                    <div class="label">Sale Revenue</div>
                    <div class="value">Rs. {{ number_format($costStatement['revenue'], 2) }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="summary-stat h-100">
                    <div class="label">Total Cost</div>
                    <div class="value">Rs. {{ number_format($costStatement['total_cost'], 2) }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="summary-stat h-100">
                    <div class="label">Profit per Pcs</div>
                    <div class="value">Rs. {{ number_format($costStatement['produced_qty'] > 0 ? $costStatement['profit'] / $costStatement['produced_qty'] : 0, 2) }}</div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>