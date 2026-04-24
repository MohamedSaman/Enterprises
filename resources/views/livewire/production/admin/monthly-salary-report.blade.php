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
            padding: 1.5rem 1rem;
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
        .payslip-a4 {
            width: 210mm;
            min-height: 297mm;
            padding: 20mm 20mm 50mm 20mm;
            margin: 0 auto;
            box-sizing: border-box;
            font-family: 'Inter', 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            font-size: 11pt;
            color: #1e293b;
            background-color: #fff;
            position: relative;
            line-height: 1.5;
        }
        .payslip-a4 .text-sys { color: #0e7490; }
        .payslip-a4 .bg-sys { background-color: #0e7490; color: #fff; }
        .payslip-a4 .bg-gray { background-color: #f8fafc; }
        
        .payslip-a4 h1 { font-size: 24pt; font-weight: 800; margin: 0; letter-spacing: -0.025em; }
        .payslip-a4 h2 { font-size: 14pt; font-weight: 700; margin: 0; text-transform: uppercase; letter-spacing: 0.05em; }
        .payslip-a4 p { margin: 0; }
        
        .payslip-a4 .row-flex { display: flex; justify-content: space-between; gap: 30px; margin-bottom: 25px; }
        .payslip-a4 .col-6 { width: 50%; }
        
        .payslip-a4 .section-box { border: 1px solid #e2e8f0; height: 100%; border-radius: 4px; overflow: hidden; }
        .payslip-a4 .section-header { padding: 8px 12px; font-weight: 700; font-size: 9pt; text-transform: uppercase; letter-spacing: 0.05em; }
        .payslip-a4 .section-content { padding: 12px; }
        
        .payslip-a4 table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .payslip-a4 th, .payslip-a4 td { padding: 10px 12px; border-bottom: 1px solid #e2e8f0; }
        .payslip-a4 th { text-align: left; font-size: 9pt; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; font-weight: 700; background-color: #f8fafc; border-bottom: 2px solid #e2e8f0; }
        
        .payslip-a4 .text-right { text-align: right; }
        .payslip-a4 .text-center { text-align: center; }
        .payslip-a4 .font-bold { font-weight: 700; }
        
        .payslip-a4 .totals-row td { background-color: #f1f5f9; border-top: 2px solid #e2e8f0; font-weight: 700; color: #0f172a; }
        .payslip-a4 .net-pay-box { margin-top: 30px; display: flex; justify-content: flex-end; }
        .payslip-a4 .net-pay-content { padding: 15px 25px; border-radius: 4px; min-width: 250px; }
        
        .payslip-a4 .footer { position: absolute; bottom: 20mm; left: 20mm; right: 20mm; border-top: 1px solid #e2e8f0; padding-top: 15px; font-size: 9pt; color: #64748b; text-align: center; }
        
        .payslip-a4 .contribution-note { margin-top: 20px; padding: 12px; border-radius: 4px; border-left: 4px solid #0e7490; background-color: #f0f9ff; font-size: 9pt; }
        @page { size: A4 portrait; margin: 0; }

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
                <div class="d-flex align-items-end">
                    @if($salaryData['include_epf_etf'])
                        <div class="badge bg-success text-white p-2 w-100">
                            <i class="bi bi-check-circle me-1"></i> EPF / ETF Calculation Active
                        </div>
                    @else
                        <div class="badge bg-danger text-white p-2 w-100">
                            <i class="bi bi-x-circle me-1"></i> EPF / ETF Not Applicable
                        </div>
                    @endif
                </div>
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
                        <div class="small text-muted mt-1">Quota: {{ (int)($settings['production_salary_paid_leave_days'] ?? 14) }} days/yr</div>
                        @if(isset($salaryData['unpaid_leave_days']) && $salaryData['unpaid_leave_days'] > 0)
                            <small class="text-danger fw-bold">+{{ $salaryData['unpaid_leave_days'] }} Unpaid</small>
                        @endif
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="stat-card">
                        <div class="stat-label">Hours Worked</div>
                        <div class="stat-value">{{ number_format($salaryData['total_regular_hours'], 1) }}</div>
                        <small class="text-muted fw-semibold">Hrs</small>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="stat-card">
                        <div class="stat-label">Hourly Rate</div>
                        <div class="stat-value">{{ number_format($salaryData['hourly_rate'], 2) }}</div>
                        <small class="text-muted fw-semibold">LKR/Hr</small>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="stat-card">
                        <div class="stat-label">Net Basic</div>
                        <div class="stat-value">{{ number_format($salaryData['earned_basic_salary'], 0) }}</div>
                        <small class="text-muted fw-semibold">LKR</small>
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
                                <span class="info-label">Basic Salary ({{ number_format($salaryData['total_regular_hours'], 2) }} hrs)</span>
                                <span class="info-value">Rs. {{ number_format($salaryData['earned_basic_salary'], 2) }}</span>
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
                            @if($salaryData['include_epf_etf'])
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
            <!-- Action Buttons -->
            <div class="row g-2 justify-content-end mt-2">
                <div class="col-auto">
                    <button class="btn btn-gradient" wire:click="generateSalary" @disabled($salaryAlreadyExists || !$selectedEmployee || !$selectedMonth || !$selectedYear)>
                        <i class="bi bi-check-circle me-2"></i>Generate Salary
                    </button>
                </div>
            </div>
        </div>

       
        </div>
        @endif

        <!-- Generated Salaries List -->
        <div class="card-modern">
            <div class="card-header-modern d-flex justify-content-between align-items-center">
                <div class="p-2">
                    <h5 class="fw-bold fs-5">Generated Salaries List</h5>
                    <small class="text-muted">Salaries generated for {{ date('F Y', strtotime("$selectedYear-$selectedMonth-01")) }}</small>
                </div>
                <div class="d-flex align-items-center">
                    <span class="badge-status badge-generated fw-normal me-3">{{ $generatedSalaries->count() }} Generated</span>
                </div>
            </div>
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
                                    <button type="button" class="btn-sm-icon" style="background: #e0f2fe; color: #0284c7;"
                                        wire:click="viewPayslip({{ $salary->id }})"
                                        title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </button>

                                    <button type="button" class="btn-sm-icon btn-delete"
                                        wire:click="confirmDelete({{ $salary->id }})"
                                        @if($salary->status === 'paid') disabled @endif
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

    <!-- Hidden Print Preview Container -->
    <div id="preview-payslip-content" style="display: none;">
        <div class="payslip-a4">
            <!-- Header -->
        <div class="row-flex" style="align-items: center; border-bottom: 4px solid #0e7490; padding-bottom: 20px; margin-bottom: 30px;">
            <div class="col-6">
                <h1 class="text-sys">{{ config('shop.name', 'Company Name') }}</h1>
                <div style="margin-top: 8px; color: #64748b; font-size: 10pt;">
                    <p>{{ config('shop.address', '12345 Court Road, London W1T 1JY, UK') }}</p>
                    <p>T: {{ config('shop.phone', '+44 00 0000 0000') }} | E: {{ config('shop.email', 'name@provider.com') }}</p>
                </div>
            </div>
            <div class="col-6 text-right">
                <h2 class="text-sys">PAYSLIP</h2>
                <div style="margin-top: 8px; color: #64748b; font-size: 10pt;">
                    <p>Reference: #PREVIEW</p>
                    <p>Date: {{ now()->format('d M, Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Info Section -->
        <div class="row-flex">
            <div class="col-6">
                <div class="section-box">
                    <div class="section-header bg-sys">Employee Details</div>
                    <div class="section-content">
                        <p class="font-bold" style="font-size: 12pt; margin-bottom: 4px;">{{ $salaryData['employee_name'] ?? 'N/A' }}</p>
                        <p style="color: #64748b;">{{ $salaryData['employee_role'] ?? 'N/A' }}</p>
                        <div style="margin-top: 12px; font-size: 10pt;">
                            <p><strong>Employee ID:</strong> {{ $salaryData['employee_emp_id'] ?? 'N/A' }}</p>
                            <p><strong>Contact:</strong> {{ $salaryData['employee_phone'] ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="section-box">
                    <div class="section-header bg-sys">Payment Information</div>
                    <div class="section-content">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; font-size: 10pt;">
                            <div>
                                <p style="color: #64748b; font-size: 8pt; text-transform: uppercase;">Pay Period</p>
                                <p class="font-bold">{{ $salaryData['month_label'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p style="color: #64748b; font-size: 8pt; text-transform: uppercase;">Status</p>
                                <p class="font-bold text-sys">PREVIEW</p>
                            </div>
                            <div>
                                <p style="color: #64748b; font-size: 8pt; text-transform: uppercase;">Method</p>
                                <p class="font-bold">Cash</p>
                            </div>
                            <div>
                                <p style="color: #64748b; font-size: 8pt; text-transform: uppercase;">Attendance</p>
                                <p class="font-bold">{{ $salaryData['attendance_days'] ?? 0 }} / {{ $salaryData['working_days'] ?? 0 }} Days</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Earnings Table -->
        <div style="margin-top: 10px;">
            <h2 style="font-size: 10pt; color: #0e7490; margin-bottom: 10px; border-left: 4px solid #0e7490; padding-left: 10px;">Earnings</h2>
            <table class="main-table">
                <thead>
                    <tr>
                        <th style="width: 50%;">Description</th>
                        <th class="text-center" style="width: 15%;">Units</th>
                        <th class="text-right" style="width: 15%;">Rate</th>
                        <th class="text-right" style="width: 20%;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="font-bold">Basic Salary</td>
                        <td class="text-center">-</td>
                        <td class="text-right">-</td>
                        <td class="text-right font-bold">{{ number_format($salaryData['basic_salary'] ?? 0, 2) }}</td>
                    </tr>
                    @if(($salaryData['overtime_amount'] ?? 0) > 0)
                    <tr>
                        <td>Overtime Payment</td>
                        <td class="text-center">{{ $salaryData['overtime_hours'] ?? 0 }} hrs</td>
                        <td class="text-right">-</td>
                        <td class="text-right">{{ number_format($salaryData['overtime_amount'] ?? 0, 2) }}</td>
                    </tr>
                    @endif
                    @if(($salaryData['attendance_bonus'] ?? 0) > 0)
                    <tr>
                        <td>Attendance Bonus / Holiday Pay</td>
                        <td class="text-center">{{ $salaryData['attendance_days'] ?? 0 }} days</td>
                        <td class="text-right">-</td>
                        <td class="text-right">{{ number_format($salaryData['attendance_bonus'] ?? 0, 2) }}</td>
                    </tr>
                    @endif
                    @if(($salaryData['commission'] ?? 0) > 0)
                    <tr>
                        <td>Production Commission</td>
                        <td class="text-center">-</td>
                        <td class="text-right">-</td>
                        <td class="text-right">{{ number_format($salaryData['commission'] ?? 0, 2) }}</td>
                    </tr>
                    @endif
                    <tr class="totals-row">
                        <td colspan="3" class="text-right">Total Gross Earnings</td>
                        <td class="text-right text-sys">Rs. {{ number_format($salaryData['gross_salary'] ?? 0, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Deductions Table -->
        <div style="margin-top: 20px;">
            <h2 style="font-size: 10pt; color: #ef4444; margin-bottom: 10px; border-left: 4px solid #ef4444; padding-left: 10px;">Deductions</h2>
            <table class="main-table">
                <thead>
                    <tr>
                        <th style="width: 80%;">Description</th>
                        <th class="text-right" style="width: 20%;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!empty($salaryData['include_epf_etf']))
                    <tr>
                        <td>EPF Contribution (Employee 8%)</td>
                        <td class="text-right text-danger">{{ number_format($salaryData['epf_employee'] ?? 0, 2) }}</td>
                    </tr>
                    @endif
                    @php
                        $totalDeds = !empty($salaryData['include_epf_etf']) ? ($salaryData['epf_employee'] ?? 0) : 0;
                    @endphp
                    @if($totalDeds == 0)
                    <tr>
                        <td style="color: #94a3b8; font-style: italic;">No deductions for this period</td>
                        <td class="text-right">0.00</td>
                    </tr>
                    @endif
                    <tr class="totals-row">
                        <td class="text-right">Total Deductions</td>
                        <td class="text-right text-danger">Rs. {{ number_format($totalDeds, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Net Pay -->
        <div class="net-pay-box">
            <div class="net-pay-content bg-sys">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 10pt; text-transform: uppercase; letter-spacing: 0.1em; opacity: 0.9;">Net Payable Salary</span>
                    <span style="font-size: 18pt; font-weight: 800;">Rs. {{ number_format($salaryData['net_salary'] ?? 0, 2) }}</span>
                </div>
            </div>
        </div>

        @if(!empty($salaryData['include_epf_etf']))
        <div class="contribution-note">
            <p class="font-bold text-sys" style="margin-bottom: 4px;">Employer Contributions (Not deducted from salary)</p>
            <p>EPF (12%): <strong>Rs. {{ number_format($salaryData['epf_employer'] ?? 0, 2) }}</strong> &nbsp;&bull;&nbsp; ETF (3%): <strong>Rs. {{ number_format($salaryData['etf'] ?? 0, 2) }}</strong></p>
        </div>
        @endif
        </div>
    </div>

    <!-- Include Payslip Modal -->
    <x-salary.payslip-modal :selectedSalary="$selectedSalary" />

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteSalaryModal" tabindex="-1" aria-labelledby="deleteSalaryModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 bg-light">
                    <h5 class="modal-title text-danger fw-bold" id="deleteSalaryModalLabel">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>Confirm Deletion
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <p class="fs-5 mb-1">Are you sure you want to delete this salary record?</p>
                    <p class="text-muted small mb-0">This action cannot be undone.</p>
                </div>
                <div class="modal-footer border-0 justify-content-center bg-light">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger px-4" wire:click="deleteSalaryConfirmed">
                        <span wire:loading.remove wire:target="deleteSalaryConfirmed">Yes, Delete</span>
                        <span wire:loading wire:target="deleteSalaryConfirmed">Deleting...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification Script -->
    <script>
        document.addEventListener('notify', function(event) {
            // You can integrate with your notification system here
            console.log('Notification:', event.detail);
        });

        // Listen for the event dispatched by the component
        window.addEventListener('open-payslip-modal', event => {
            var payslipModal = new bootstrap.Modal(document.getElementById('payslip-modal'));
            payslipModal.show();
        });

        // Listen for delete modal events
        window.addEventListener('open-delete-modal', event => {
            var deleteModal = new bootstrap.Modal(document.getElementById('deleteSalaryModal'));
            deleteModal.show();
        });

        window.addEventListener('close-delete-modal', event => {
            var modalEl = document.getElementById('deleteSalaryModal');
            var deleteModal = bootstrap.Modal.getInstance(modalEl);
            if (deleteModal) {
                deleteModal.hide();
            }
        });

        // Function to print the preview slip
        function printPreviewSlip() {
            const content = document.getElementById('preview-payslip-content');
            if (!content) return;
            
            const printWindow = window.open('', '', 'height=600,width=800');
            printWindow.document.write('<html><head><title>Salary Payslip Preview</title>');
            printWindow.document.write('</head><body onload="setTimeout(function(){ window.print(); window.close(); }, 200);">');
            printWindow.document.write(content.innerHTML);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
        }
    </script>
</div>