@props(['selectedSalary', 'errors' => null])
<div wire:ignore.self class="modal fade" id="payslip-modal" tabindex="-1" aria-labelledby="payslip-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 235mm;">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="payslip-modal-label">
                    <i class="bi bi-file-earmark-person-fill me-2"></i>Employee Salary Slip
                </h5>
                <div class="ms-auto d-flex align-items-center">
                    <button type="button" class="btn btn-sm btn-light me-2" onclick="window.print()">
                        <i class="bi bi-printer me-1"></i>Print A4
                    </button>
                    <!-- Legacy print and download buttons kept for compatibility if needed -->
                    @if(request()->routeIs('*.staff-salary'))
                        <button type="button" class="btn btn-sm btn-light me-2" onclick="printPayslipContent()">
                            <i class="bi bi-printer me-1"></i>Legacy Print
                        </button>
                        <button type="button" class="btn btn-sm btn-light me-2" onclick="downloadPayslip('Payslip.html')">
                            <i class="bi bi-download me-1"></i>Download
                        </button>
                    @endif
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body bg-light p-4 d-flex justify-content-center overflow-auto">
                @if (!empty($selectedSalary) && $selectedSalary)
                <style>
                    /* A4 Portrait Print Styles */
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
                        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
                        line-height: 1.5;
                    }
                    .payslip-a4 .text-sys { color: #0e7490; }
                    .payslip-a4 .bg-sys { background-color: #0e7490; color: #fff; }
                    .payslip-a4 .border-sys { border-color: #0e7490; }
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

                    @media print {
                        body * { visibility: hidden; }
                        #payslip-modal { position: absolute !important; left: 0 !important; top: 0 !important; width: 100% !important; height: 100% !important; margin: 0 !important; padding: 0 !important; display: block !important; opacity: 1 !important; visibility: visible !important; }
                        .modal-dialog, .modal-content, .modal-body { margin: 0 !important; padding: 0 !important; width: 100% !important; height: 100% !important; max-width: none !important; box-shadow: none !important; border: none !important; background: #fff !important; }
                        .modal-header, .btn-close, .modal-footer { display: none !important; }
                        #payslipContent, #payslipContent * { visibility: visible !important; }
                        #payslipContent { position: absolute !important; left: 0 !important; top: 0 !important; width: 210mm !important; min-height: 297mm !important; padding: 20mm !important; box-shadow: none !important; }
                        @page { size: A4 portrait; margin: 0; }
                    }
                </style>

                <div id="payslipContent" class="payslip-a4">
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
                                <p>Reference: #{{ $selectedSalary->id ?? ($selectedSalary->salary_id ?? 'N/A') }}</p>
                                <p>Date: {{ $selectedSalary->updated_at ? $selectedSalary->updated_at->format('d M, Y') : now()->format('d M, Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Info Section -->
                    <div class="row-flex">
                        <div class="col-6">
                            <div class="section-box">
                                <div class="section-header bg-sys">Employee Details</div>
                                <div class="section-content">
                                    <p class="font-bold" style="font-size: 12pt; margin-bottom: 4px;">{{ $selectedSalary->user->name ?? 'N/A' }}</p>
                                    <p style="color: #64748b;">{{ $selectedSalary->user->userDetail->work_role ?? ($selectedSalary->user->detail->work_role ?? 'N/A') }}</p>
                                    <div style="margin-top: 12px; font-size: 10pt;">
                                        <p><strong>Employee ID:</strong> {{ $selectedSalary->user->userDetail->user_id ?? ($selectedSalary->user->detail->user_id ?? 'N/A') }}</p>
                                        <p><strong>Contact:</strong> {{ $selectedSalary->user->phone ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="section-box">
                                <div class="section-header bg-sys">Payment Information</div>
                                <div class="section-content">
                                    @php
                                        $monthName = 'N/A';
                                        if(!empty($selectedSalary->month) && !empty($selectedSalary->year)) {
                                            $monthName = \Carbon\Carbon::create()->month($selectedSalary->month)->format('F Y');
                                        } elseif (!empty($selectedSalary->salary_month)) {
                                            $monthName = \Carbon\Carbon::parse($selectedSalary->salary_month)->format('F, Y');
                                        }
                                    @endphp
                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; font-size: 10pt;">
                                        <div>
                                            <p style="color: #64748b; font-size: 8pt; text-transform: uppercase;">Pay Period</p>
                                            <p class="font-bold">{{ $monthName }}</p>
                                        </div>
                                        <div>
                                            <p style="color: #64748b; font-size: 8pt; text-transform: uppercase;">Status</p>
                                            <p class="font-bold text-sys">{{ strtoupper($selectedSalary->status ?? ($selectedSalary->payment_status ?? 'GENERATED')) }}</p>
                                        </div>
                                        <div>
                                            <p style="color: #64748b; font-size: 8pt; text-transform: uppercase;">Method</p>
                                            <p class="font-bold">Bank Transfer</p>
                                        </div>
                                        <div>
                                            <p style="color: #64748b; font-size: 8pt; text-transform: uppercase;">Attendance</p>
                                            <p class="font-bold">{{ $selectedSalary->attendance_days ?? 0 }} / {{ $selectedSalary->working_days ?? 0 }} Days</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @php
                        // Get full basic salary for hourly rate calculation
                        $fullBasicSalary = (float)($selectedSalary->user->detail->basic_salary ?? ($selectedSalary->user->userDetail->basic_salary ?? 0));
                        $stdWorkingDays = (int)($selectedSalary->working_days ?? 25);
                        $stdHoursPerDay = 8; // Default to 8 if setting is not accessible
                        $totalStdHours = $stdWorkingDays * $stdHoursPerDay;
                        $hRate = $totalStdHours > 0 ? ($fullBasicSalary / $totalStdHours) : 0;

                        // Normalize the fields
                        $earnedBasicSalary = (float)($selectedSalary->basic_salary ?? 0);
                        
                        // Calculate hours worked from earned basic salary
                        $totalRegularHoursWorked = $hRate > 0 ? ($earnedBasicSalary / $hRate) : 0;
                        $paidLeaveDays = (int)($selectedSalary->paid_leave_days ?? 0);
                        $paidLeaveHours = $paidLeaveDays * $stdHoursPerDay;
                        $actualRegularHoursWorked = max(0, $totalRegularHoursWorked - $paidLeaveHours);

                        $overtimeHours = (float)($selectedSalary->overtime_hours ?? 0);
                        $overtimeAmount = (float)($selectedSalary->overtime_amount ?? ($selectedSalary->overtime ?? 0));
                        $attendanceBonus = (float)($selectedSalary->attendance_bonus ?? ($selectedSalary->bonus ?? 0));
                        $commission = (float)($selectedSalary->commission ?? 0);
                        $allowance = (float)($selectedSalary->allowance ?? 0);
                        
                        $grossSalary = (float)($selectedSalary->gross_salary ?? ($earnedBasicSalary + $overtimeAmount + $attendanceBonus + $commission + $allowance));
                        
                        $epfEmployee = (float)($selectedSalary->epf_employee ?? 0);
                        $otherDeductions = (float)($selectedSalary->other_deductions ?? ($selectedSalary->deductions ?? 0));
                        $loanDeduction = (float)($selectedSalary->loan_deduction ?? 0);
                        
                        $totalDeductions = 0;
                        if (!empty($selectedSalary->include_epf_etf) && $selectedSalary->include_epf_etf) {
                            $totalDeductions += $epfEmployee;
                        }
                        $totalDeductions += $otherDeductions + $loanDeduction;
                        
                        $netSalary = (float)($selectedSalary->net_salary ?? ($grossSalary - $totalDeductions));
                    @endphp

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
                                    <td>
                                        <div class="font-bold">Basic Salary (Hours Based)</div>
                                        <div style="font-size: 8pt; color: #64748b;">Actual Regular Hours: {{ number_format($actualRegularHoursWorked, 2) }} hrs</div>
                                    </td>
                                    <td class="text-center">{{ number_format($totalRegularHoursWorked, 2) }}</td>
                                    <td class="text-right">{{ number_format($hRate, 2) }}</td>
                                    <td class="text-right font-bold">{{ number_format($earnedBasicSalary, 2) }}</td>
                                </tr>
                                @if($paidLeaveDays > 0)
                                <tr>
                                    <td>
                                        <div>Paid Leave Days</div>
                                        <div style="font-size: 8pt; color: #64748b;">{{ $paidLeaveDays }} days @ {{ $stdHoursPerDay }} hrs</div>
                                    </td>
                                    <td class="text-center">-</td>
                                    <td class="text-right">-</td>
                                    <td class="text-right">Included</td>
                                </tr>
                                @endif
                                @if($allowance > 0)
                                <tr>
                                    <td>Allowance</td>
                                    <td class="text-center">-</td>
                                    <td class="text-right">-</td>
                                    <td class="text-right">{{ number_format($allowance, 2) }}</td>
                                </tr>
                                @endif
                                @if($overtimeAmount > 0)
                                <tr>
                                    <td>
                                        <div>Overtime (OT)</div>
                                        <div style="font-size: 8pt; color: #64748b;">30 min+ rule applied</div>
                                    </td>
                                    <td class="text-center">{{ number_format($overtimeHours, 2) }}</td>
                                    <td class="text-right">
                                        @php
                                            $otMultiplier = 1.5; // Default if not found
                                            // Ideally we'd pass this from the component, but we can infer it
                                            $otRate = $overtimeHours > 0 ? ($overtimeAmount / $overtimeHours) : ($hRate * $otMultiplier);
                                        @endphp
                                        {{ number_format($otRate, 2) }}
                                    </td>
                                    <td class="text-right">{{ number_format($overtimeAmount, 2) }}</td>
                                </tr>
                                @endif
                                @if($attendanceBonus > 0)
                                <tr>
                                    <td>Attendance Bonus / Holiday Pay</td>
                                    <td class="text-center">{{ $selectedSalary->attendance_days ?? 0 }} days</td>
                                    <td class="text-right">-</td>
                                    <td class="text-right">{{ number_format($attendanceBonus, 2) }}</td>
                                </tr>
                                @endif
                                @if($commission > 0)
                                <tr>
                                    <td>Production Commission</td>
                                    <td class="text-center">-</td>
                                    <td class="text-right">-</td>
                                    <td class="text-right">{{ number_format($commission, 2) }}</td>
                                </tr>
                                @endif
                                <tr class="totals-row">
                                    <td colspan="3" class="text-right">Total Gross Earnings</td>
                                    <td class="text-right text-sys">Rs. {{ number_format($grossSalary, 2) }}</td>
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
                                @if(!empty($selectedSalary->include_epf_etf) && $selectedSalary->include_epf_etf)
                                <tr>
                                    <td>EPF Contribution (Employee 8%)</td>
                                    <td class="text-right text-danger">{{ number_format($epfEmployee, 2) }}</td>
                                </tr>
                                @endif
                                @if($otherDeductions > 0)
                                <tr>
                                    <td>Advances / Other Deductions</td>
                                    <td class="text-right text-danger">{{ number_format($otherDeductions, 2) }}</td>
                                </tr>
                                @endif
                                @if($loanDeduction > 0)
                                <tr>
                                    <td>Loan Repayment</td>
                                    <td class="text-right text-danger">{{ number_format($loanDeduction, 2) }}</td>
                                </tr>
                                @endif
                                @php
                                    $dedCount = (!empty($selectedSalary->include_epf_etf) ? 1 : 0) + ($otherDeductions > 0 ? 1 : 0) + ($loanDeduction > 0 ? 1 : 0);
                                @endphp
                                @if($dedCount == 0)
                                <tr>
                                    <td style="color: #94a3b8; font-style: italic;">No deductions for this period</td>
                                    <td class="text-right">0.00</td>
                                </tr>
                                @endif
                                <tr class="totals-row">
                                    <td class="text-right">Total Deductions</td>
                                    <td class="text-right text-danger">Rs. {{ number_format($totalDeductions, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Net Pay -->
                    <div class="net-pay-box">
                        <div class="net-pay-content bg-sys">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-size: 10pt; text-transform: uppercase; letter-spacing: 0.1em; opacity: 0.9;">Net Payable Salary</span>
                                <span style="font-size: 18pt; font-weight: 800;">Rs. {{ number_format($netSalary, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Employer Contributions (Note) -->
                    @if(!empty($selectedSalary->include_epf_etf) && $selectedSalary->include_epf_etf && (!empty($selectedSalary->epf_employer) || !empty($selectedSalary->etf)))
                    <div class="contribution-note">
                        <p class="font-bold text-sys" style="margin-bottom: 4px;">Employer Contributions (Not deducted from salary)</p>
                        <p>EPF (12%): <strong>Rs. {{ number_format($selectedSalary->epf_employer ?? 0, 2) }}</strong> &nbsp;&bull;&nbsp; ETF (3%): <strong>Rs. {{ number_format($selectedSalary->etf ?? 0, 2) }}</strong></p>
                    </div>
                    @endif

                </div>
                @else
                <div class="text-center p-5 w-100">
                    <div class="spinner-border text-primary mb-3" role="status"></div>
                    <p class="text-muted">Preparing payslip document...</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

