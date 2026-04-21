<div class="salary-shell">
    @push('styles')
    <style>
        .salary-shell {
            background:
                radial-gradient(circle at 12% 8%, rgba(34, 197, 94, 0.08) 0%, transparent 35%),
                radial-gradient(circle at 88% 14%, rgba(6, 182, 212, 0.08) 0%, transparent 32%),
                linear-gradient(160deg, #f4f8ff 0%, #eef6f9 100%);
            min-height: auto;
            padding: 1rem 0;
        }

        .card-modern {
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid rgba(30, 41, 59, 0.08);
            box-shadow: 0 14px 34px rgba(15, 23, 42, 0.06);
            transition: all 0.3s ease;
        }

        .card-modern:hover {
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.08);
        }

        .hero-title {
            font-size: 1.35rem;
            font-weight: 900;
            color: #0f172a;
            background: linear-gradient(120deg, #0f766e 0%, #0369a1 55%, #0891b2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-card {
            background: linear-gradient(135deg, rgba(14, 116, 144, 0.08) 0%, rgba(22, 163, 74, 0.08) 100%);
            border: 1px solid rgba(14, 116, 144, 0.14);
            border-radius: 12px;
            padding: 0.85rem;
            text-align: center;
            min-height: 96px;
        }

        .stat-label {
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #64748b;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .stat-value {
            font-size: 1.25rem;
            font-weight: 900;
            color: #0f172a;
        }

        .form-control-lg {
            padding: 0.55rem 0.85rem;
            font-size: 0.88rem;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .form-control-lg:focus {
            border-color: #0e7490;
            box-shadow: 0 0 0 3px rgba(14, 116, 144, 0.12);
        }

        .btn-gradient {
            background: linear-gradient(135deg, #0f766e 0%, #0369a1 100%);
            border: none;
            color: white;
            font-weight: 700;
            padding: 0.55rem 1.1rem;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .salary-shell .btn {
            font-size: 0.88rem;
        }

        .salary-shell p,
        .salary-shell small,
        .salary-shell li,
        .salary-shell label,
        .salary-shell span,
        .salary-shell td,
        .salary-shell th {
            font-size: 0.92rem;
        }

        .salary-shell .text-muted {
            font-size: 0.88rem;
        }

        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(3, 105, 161, 0.28);
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 0.55rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.01em;
            font-size: 1rem;
        }

        .section-title i {
            color: #0e7490;
        }

        .salary-table {
            border-collapse: collapse;
        }

        .salary-table thead th {
            background: #f8fafc;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            padding: 1rem;
            border-bottom: 2px solid #e2e8f0;
        }

        .salary-table tbody td {
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        .salary-table tbody tr:hover {
            background: #f8fafc;
        }

        .badge-status {
            display: inline-block;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .badge-generated {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-approved {
            background: #dcfce7;
            color: #166534;
        }

        .badge-paid {
            background: #f0fdf4;
            color: #15803d;
        }

        .salary-highlight {
            background: linear-gradient(135deg, #e6fff6 0%, #d9f2ff 100%);
            border-radius: 12px;
            padding: 0.9rem;
            border-left: 4px solid #0891b2;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 0.55rem;
        }

        .breakdown-panel {
            background: #fbfdff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 0.75rem 0.85rem 0.5rem;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            padding: 0.55rem 0;
            border-bottom: 1px dashed #e2e8f0;
        }

        .info-label {
            color: #64748b;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .info-value {
            color: #0f172a;
            font-weight: 700;
            font-size: 0.95rem;
        }

        .salary-table thead th {
            padding: 0.65rem;
            font-size: 0.68rem;
        }

        .salary-table tbody td {
            padding: 0.6rem;
        }

        .text-danger-custom {
            color: #dc2626;
        }

        .text-success-custom {
            color: #16a34a;
        }

        .toggle-switch {
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
        }

        .toggle-switch input {
            width: 50px;
            height: 26px;
            appearance: none;
            cursor: pointer;
            background: #cbd5e1;
            border-radius: 13px;
            outline: none;
            transition: background 0.3s;
        }

        .toggle-switch input:checked {
            background: #0e7490;
        }

        .toggle-switch input::after {
            content: '';
            position: absolute;
            width: 22px;
            height: 22px;
            background: white;
            border-radius: 50%;
            top: 2px;
            left: 2px;
            transition: left 0.3s;
        }

        .toggle-switch input:checked::after {
            left: 26px;
        }

        .commission-note {
            border: 1px solid #bde9ff;
            background: linear-gradient(180deg, #f0fbff 0%, #e7f8ff 100%);
            border-radius: 14px;
            color: #0c4a6e;
            padding: 0.75rem 0.9rem;
        }

        .commission-note ul {
            margin-top: 0.4rem !important;
            margin-bottom: 0 !important;
        }

        .commission-note li {
            margin-bottom: 0.15rem;
        }

        .no-data-message {
            text-align: center;
            padding: 3rem 1rem;
            color: #94a3b8;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .btn-sm-icon {
            padding: 0.4rem 0.8rem;
            font-size: 0.75rem;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-edit {
            background: #dbeafe;
            color: #1e40af;
        }

        .btn-edit:hover {
            background: #bfdbfe;
        }

        .btn-delete {
            background: #fee2e2;
            color: #991b1b;
        }

        .btn-delete:hover {
            background: #fecaca;
        }

        @media (max-width: 991px) {
            .stat-value {
                font-size: 1.35rem;
            }

            .hero-title {
                font-size: 1.4rem;
            }
        }
    </style>
    @endpush

    <div class="container-fluid py-2">
        <!-- Header -->
        <div class="card-modern p-3 mb-3">
            <div class="row align-items-start gap-3">
                <div class="col">
                    <h1 class="hero-title mb-2">Monthly Salary Management</h1>
                    <p class="text-muted mb-0">Calculate and manage employee salaries on a monthly basis with attendance tracking and commission calculations</p>
                </div>
                <div class="col-auto">
                    <a href="{{ route('production.admin.salary') }}" class="btn btn-outline-secondary">← Back to Batch Salary</a>
                </div>
            </div>
        </div>

        <!-- Month and Employee Selection -->
        <div class="card-modern p-3 mb-3">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-bold mb-2">Year</label>
                    <select class="form-control-lg w-100" wire:model.live="selectedYear">
                        <option value="">-- Choose Year --</option>
                        @foreach($availableYears as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold mb-2">Select Month</label>
                    <select class="form-control-lg w-100" wire:model.live="selectedMonth" @disabled(!$selectedYear)>
                        <option value="">-- Choose Month --</option>
                        @forelse($availableMonths as $month)
                        <option value="{{ $month['value'] }}">{{ $month['label'] }}</option>
                        @empty
                        <option value="">No months available</option>
                        @endforelse
                    </select>

                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold mb-2">Select Employee</label>
                    <select class="form-control-lg w-100" wire:model.live="selectedEmployee">
                        <option value="">-- Choose Employee --</option>
                        @foreach($employees as $employee)
                        <option value="{{ $employee->id }}">
                            {{ $employee->name }} - {{ $employee->detail->work_role ?? 'Staff' }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        @if($salaryData)
        <!-- Employee Details Section -->
        <div class="card-modern p-3 mb-3">
            <h5 class="section-title mb-3">
                <i class="bi bi-person-circle me-2"></i>{{ $salaryData['employee_name'] }}
                <span class="badge bg-secondary ms-2">{{ $salaryData['employee_role'] }}</span>
            </h5>

            <div class="row g-2 mb-3">
                <div class="col-md-2">
                    <div class="stat-card">
                        <div class="stat-label">Working Days</div>
                        <div class="stat-value">{{ $salaryData['working_days'] }}</div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="stat-card">
                        <div class="stat-label">Attendance Days</div>
                        <div class="stat-value">{{ $salaryData['attendance_days'] }}</div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="stat-card">
                        <div class="stat-label">Paid Leave Days</div>
                        <div class="stat-value">{{ $salaryData['paid_leave_days'] }}</div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="stat-card">
                        <div class="stat-label">Basic Salary</div>
                        <div class="stat-value">{{ number_format($salaryData['basic_salary'], 0) }}</div>
                        <small class="text-muted fw-semibold">LKR</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="salary-highlight">
                        <div class="small fw-bold text-muted mb-2">Include EPF / ETF Deductions</div>
                        <div class="small text-muted mb-2">Calculated on basic salary only</div>
                        <label class="toggle-switch">
                            <input type="checkbox" wire:model.live="includeEpfEtf">
                            <span class="small fw-bold">{{ $includeEpfEtf ? 'Enabled' : 'Disabled' }}</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Salary Breakdown -->
            <div class="row g-3">
                <div class="col-lg-6">
                    <h6 class="section-title mb-3"><i class="bi bi-calculator"></i>Salary Components</h6>
                    <div class="breakdown-panel">
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">Basic Salary</span>
                                <span class="info-value">Rs. {{ number_format($salaryData['basic_salary'], 2) }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Attendance Bonus</span>
                                <span class="info-value text-success-custom">+ Rs. {{ number_format($salaryData['attendance_bonus'], 2) }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Commission</span>
                                <span class="info-value text-success-custom">+ Rs. {{ number_format($salaryData['commission'], 2) }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Overtime ({{ $salaryData['overtime_hours'] }} hrs)</span>
                                <span class="info-value text-success-custom">+ Rs. {{ number_format($salaryData['overtime_amount'], 2) }}</span>
                            </div>
                            <div class="info-item" style="border-bottom: 2px solid #e2e8f0;">
                                <span class="info-label fw-bold">Gross Salary</span>
                                <span class="info-value fw-bold">Rs. {{ number_format($salaryData['gross_salary'], 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <h6 class="section-title mb-3"><i class="bi bi-cash-stack"></i>Deductions & Net Salary</h6>
                    <div class="breakdown-panel">
                        <div class="info-grid">
                            @if($includeEpfEtf)
                            <div class="info-item">
                                <span class="info-label">EPF Employee ({{ $settings['production_salary_epf_employee_rate'] ?? 8 }}% of Basic)</span>
                                <span class="info-value text-danger-custom">- Rs. {{ number_format($salaryData['epf_employee'], 2) }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">EPF Employer ({{ $settings['production_salary_epf_employer_rate'] ?? 12 }}% of Basic)</span>
                                <span class="info-value text-muted">(Company) Rs. {{ number_format($salaryData['epf_employer'], 2) }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">ETF ({{ $settings['production_salary_etf_rate'] ?? 3 }}% of Basic)</span>
                                <span class="info-value text-muted">(Company) Rs. {{ number_format($salaryData['etf'], 2) }}</span>
                            </div>
                            @endif
                            <div class="info-item" style="border-bottom: 2px solid #e2e8f0;">
                                <span class="info-label fw-bold">Net Salary Payable</span>
                                <span class="info-value fw-bold text-success-custom">Rs. {{ number_format($salaryData['net_salary'], 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            @if($salaryAlreadyExists)
            <div class="alert alert-warning mt-3 mb-0 py-2" role="alert">
                Salary already exists for this employee in this year and month. Generation is disabled.
            </div>
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="card-modern p-3 mb-3">
            <div class="row g-2">
                <div class="col-auto">
                    <button class="btn btn-gradient" wire:click="generateSalary" @disabled($salaryAlreadyExists || !$selectedEmployee || !$selectedMonth || !$selectedYear)>
                        <i class="bi bi-check-circle me-2"></i>Generate Salary
                    </button>
                </div>
                <div class="col-auto">
                    <button class="btn btn-outline-secondary" onclick="window.print()">
                        <i class="bi bi-printer me-2"></i>Print Preview
                    </button>
                </div>
            </div>
        </div>
        @endif

        <!-- Generated Salaries Table -->
        <div class="card-modern p-3">
            <h5 class="fw-bold mb-3">
                <i class="bi bi-list-check me-2"></i>Generated Salaries for {{ isset($salaryData['month_label']) ? $salaryData['month_label'] : 'Selected Month' }}
            </h5>

            @if($generatedSalaries->count() > 0)
            <div class="table-responsive">
                <table class="table salary-table">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Role</th>
                            <th>Basic Salary</th>
                            <th>Commission</th>
                            <th>Gross Salary</th>
                            <th>Net Salary</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($generatedSalaries as $salary)
                        <tr>
                            <td class="fw-bold">{{ $salary->user->name }}</td>
                            <td>{{ $salary->user->detail->work_role ?? 'N/A' }}</td>
                            <td>Rs. {{ number_format($salary->basic_salary, 2) }}</td>
                            <td class="text-success-custom fw-bold">Rs. {{ number_format($salary->commission, 2) }}</td>
                            <td>Rs. {{ number_format($salary->gross_salary, 2) }}</td>
                            <td class="fw-bold">Rs. {{ number_format($salary->net_salary, 2) }}</td>
                            <td>
                                <span class="badge-status badge-{{ $salary->status }}">
                                    {{ ucfirst($salary->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-sm-icon btn-edit"
                                        wire:click="editSalary({{ $salary->id }})"
                                        @if($salary->status === 'paid') disabled @endif
                                        title="View/Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn-sm-icon btn-delete"
                                        wire:click="deleteSalary({{ $salary->id }})"
                                        @if($salary->status === 'paid') disabled @endif
                                        onclick="confirm('Are you sure?') || event.stopImmediatePropagation()"
                                        title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="no-data-message">
                <i class="bi bi-inbox" style="font-size: 2rem; display: block; margin-bottom: 1rem;"></i>
                <p>No salaries generated for the selected month yet.</p>
                <small class="text-muted">Select an employee and month above to generate salary</small>
            </div>
            @endif
        </div>
    </div>

    <!-- Toast Notification Script -->
    <script>
        document.addEventListener('notify', function(event) {
            // You can integrate with your notification system here
            console.log('Notification:', event.detail);
        });
    </script>
</div>