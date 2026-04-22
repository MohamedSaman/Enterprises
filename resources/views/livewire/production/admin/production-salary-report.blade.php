<div class="salary-report-shell">
    @push('styles')
    <style>
        .salary-report-shell {
            background: linear-gradient(135deg, #f5f7fb 0%, #f0f4fa 100%);
            min-height: 100vh;
            padding: 1rem 0;
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

    <div class="panel-card  p-3  mb-3">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <div class="hero-title">Production Cost Statement</div>
                <div class="text-muted">Salary and expense calculation for the selected production batch</div>
                <a href="{{ route('production.admin.expenses') }}" class="small text-decoration-none">Manage fixed-cost expenses batch wise</a>
            </div>
            <div class="d-flex gap-2 flex-wrap" style="min-width: 520px; max-width: 100%;">
                <div class="flex-grow-1">
                    <label class="form-label small fw-bold text-uppercase text-muted mb-1">Batch</label>
                    <select class="form-select" wire:model.live="selectedBatchId">
                        @foreach($batches as $batch)
                        <option value="{{ $batch->id }}">{{ $batch->batch_code }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-grow-1">
                    <label class="form-label small fw-bold text-uppercase text-muted mb-1">Manufactured Pcs</label>
                    <input type="number" min="0" class="form-control" wire:model.live="manufactured_qty">
                </div>
                <div class="flex-grow-1">
                    <label class="form-label small fw-bold text-uppercase text-muted mb-1">Extra Salary</label>
                    <input type="number" min="0" step="0.01" class="form-control" wire:model.live="extra_salary">
                </div>
            </div>
        </div>
    </div>

    @if($costStatement)
    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <div class="summary-stat h-100">
                <div class="label">Basic Salary Total</div>
                <div class="value">Rs. {{ number_format($costStatement['basic_salary_total'], 2) }}</div>
                <div class="text-muted small">Supervisor + workers in this batch</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-stat h-100">
                <div class="label">Target Commission</div>
                <div class="value">Rs. {{ number_format($costStatement['target_commission'], 2) }}</div>
                <div class="text-muted small">Based on produced pieces</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-stat h-100">
                <div class="label">Extra Salary</div>
                <div class="value">Rs. {{ number_format($costStatement['extra_salary'], 2) }}</div>
                <div class="text-muted small">Editable manual amount</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-stat h-100">
                <div class="label">Net Salary Payable</div>
                <div class="value {{ $costStatement['net_salary'] >= 0 ? 'text-success' : 'text-danger' }}">Rs. {{ number_format($costStatement['net_salary'], 2) }}</div>
                <div class="text-muted small">Gross salary after EPF deduction</div>
            </div>
        </div>
    </div>

    <div class="panel-card  p-3  mb-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <div>
                <h5 class="fw-bold mb-1">Payroll Structure</h5>
                <div class="text-muted small">Basic salary is pulled from the selected batch supervisor and workers.</div>
            </div>
            <div class="soft-note py-2 px-3 mb-0" style="min-width: 280px;">
                Gross salary = basic salary total + extra salary.
            </div>
        </div>
        <div class="row g-3">
            <div class="col-lg-7">
                <h5 class="fw-bold mb-3">Batch Basic Salary Breakdown</h5>
                <div class="table-responsive">
                    <table class="table table-bordered calc-table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Role</th>
                                <th>Name</th>
                                <th class="text-end">Basic Salary</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($basicSalaryBreakdown as $person)
                            <tr>
                                <td>{{ $person['role'] }}</td>
                                <td>{{ $person['name'] }}</td>
                                <td class="text-end">Rs. {{ number_format((float) $person['basic_salary'], 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted  py-3">No salary records found for this batch.</td>
                            </tr>
                            @endforelse
                            <tr class="table-light">
                                <td colspan="2" class="fw-bold">Total Basic Salary</td>
                                <td class="text-end fw-bold">Rs. {{ number_format($costStatement['basic_salary_total'], 2) }}</td>
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
                                <td>Basic Salary Total</td>
                                <td class="text-end">Rs. {{ number_format($costStatement['basic_salary_total'], 2) }}</td>
                            </tr>
                            <tr>
                                <td>Target Commission</td>
                                <td class="text-end">Rs. {{ number_format($costStatement['target_commission'], 2) }}</td>
                            </tr>
                            <tr>
                                <td>Extra Salary</td>
                                <td class="text-end">Rs. {{ number_format($costStatement['extra_salary'], 2) }}</td>
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
                    <div class="mt-2">EPF and ETF are calculated from gross salary after basic salary and extra salary are combined.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="panel-card  p-3  mb-3">
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