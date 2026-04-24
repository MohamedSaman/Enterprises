<?php

namespace App\Livewire\Production\Admin;

use App\Models\Attendance;
use App\Models\MonthlySalary;
use App\Models\ProductionBatch;
use App\Models\ProductionBatchDay;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.production.admin')]
#[Title('Monthly Salary Report')]
class MonthlySalaryReport extends Component
{
    public $selectedYear = null;
    public $selectedMonth = null;
    public $selectedEmployee = null;
    public $salaryData = [];
    public $editingEmployees = [];
    public bool $salaryAlreadyExists = false;

    // Settings cache (public so Livewire keeps it between requests)
    public array $settings = [];

    public function mount(): void
    {
        $now = now();
        $previousMonth = $now->copy()->subMonth();
        $this->selectedYear = (int) $previousMonth->format('Y');
        $this->selectedMonth = (int) $previousMonth->format('m');
        $this->loadSettings();
    }

    public function hydrate(): void
    {
        if (empty($this->settings)) {
            $this->loadSettings();
        }
    }

    public function updatedSelectedMonth(): void
    {
        $this->selectedEmployee = null;
        $this->salaryData = [];
        $this->salaryAlreadyExists = false;
    }

    public function updatedSelectedYear(): void
    {
        $this->selectedMonth = $this->getAvailableMonthsProperty()[0]['value'] ?? null;
        $this->selectedEmployee = null;
        $this->salaryData = [];
        $this->salaryAlreadyExists = false;
    }

    public function updatedSelectedEmployee(): void
    {
        if ($this->selectedEmployee) {
            $this->calculateSalary();
        } else {
            $this->salaryData = [];
            $this->salaryAlreadyExists = false;
        }
    }



    private function loadSettings(): void
    {
        // Initialize default settings first
        $this->settings = [
            'production_salary_working_days_per_month' => 25,
            'production_salary_paid_leave_days' => 14,
            'production_salary_attendance_bonus' => 500,
            'production_commission_threshold_items' => 10000,
            'production_commission_rate_upto_threshold' => 10,
            'production_commission_rate_after_threshold' => 15,
            'production_salary_overtime_multiplier' => 1.5,
            'production_salary_etf_rate' => 3,
            'production_salary_epf_employee_rate' => 8,
            'production_salary_epf_employer_rate' => 12,
            'production_salary_supervisor_commission_multiplier' => 2,
            'production_salary_min_attendance_full_commission' => 20,
            'production_salary_min_attendance_for_bonus' => 22,
        ];

        // Override with database settings if they exist
        $settingKeys = array_keys($this->settings);
        $dbSettings = Setting::whereIn('key', $settingKeys)->pluck('value', 'key');

        foreach ($settingKeys as $key) {
            if ($dbSettings->has($key)) {
                $this->settings[$key] = (float) $dbSettings[$key];
            }
        }
    }

    private function calculateSalary(): void
    {
        if (empty($this->settings)) {
            $this->loadSettings();
        }

        if (!$this->selectedEmployee || !$this->selectedMonth || !$this->selectedYear) {
            $this->salaryData = [];
            return;
        }

        $employee = User::find($this->selectedEmployee);
        if (!$employee) {
            $this->salaryData = [];
            return;
        }

        $this->salaryAlreadyExists = MonthlySalary::where('user_id', $employee->id)
            ->where('month', $this->selectedMonth)
            ->where('year', $this->selectedYear)
            ->exists();

        $startDate = Carbon::createFromFormat('Y-m', "{$this->selectedYear}-{$this->selectedMonth}")->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();


        // 1. Paid Leave Calculation (Fill the gap between working days and attendance)
        $workingDaysInMonth = (int) ($this->settings['production_salary_working_days_per_month'] ?? 25);
        
        // 2. Attendance Data Retrieval
        $attendanceRecords = Attendance::where('user_id', $employee->id)
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get();

        $presentRecords = $attendanceRecords->where('status', 'present');
        $attendanceDays = $presentRecords->count();
        $isEpfEligible = (bool) ($employee->detail->is_epf_eligible ?? true);
        
        // Missing days that could be covered by paid leave
        $missingDays = max(0, $workingDaysInMonth - $attendanceDays);

        $yearlyPaidLeaveQuota = (float) ($this->settings['production_salary_paid_leave_days'] ?? 14);
        
        // Count leaves taken earlier in the current year (explicitly marked as 'leave' in DB)
        $leavesTakenBeforeThisMonth = Attendance::where('user_id', $employee->id)
            ->whereYear('date', $this->selectedYear)
            ->where('date', '<', $startDate->toDateString())
            ->where('status', 'leave')
            ->count();
            
        $remainingYearlyQuota = max($yearlyPaidLeaveQuota - $leavesTakenBeforeThisMonth, 0);
        
        // Apply paid leave to missing days
        $paidLeavesInThisMonth = (int) min($missingDays, $remainingYearlyQuota);
        $unpaidLeavesInThisMonth = $missingDays - $paidLeavesInThisMonth;

        // 3. Working Hours & Hourly Rate Calculation
        $hoursPerDay = (int) ($this->settings['production_salary_working_hours_per_day'] ?? 8);
        $totalStandardHoursInMonth = $workingDaysInMonth * $hoursPerDay;
        
        $basicSalary = (float) ($employee->detail->basic_salary ?? 0);
        $hourlyRate = $totalStandardHoursInMonth > 0 ? ($basicSalary / $totalStandardHoursInMonth) : 0;
        
        $totalRegularHours = 0;
        $totalOTHours = 0;

        foreach ($presentRecords as $record) {
            $recordTotalHours = (float) $record->time_worked;
            $calculatedOTHours = 0;

            // Apply OT rule: Only if staying at least 30 mins after 5:00 PM
            if ($record->check_out) {
                try {
                    $recordDate = Carbon::parse($record->date);
                    $checkoutTime = Carbon::parse($record->check_out);
                    $checkoutTime->setDate($recordDate->year, $recordDate->month, $recordDate->day);
                    
                    $fivePM = $recordDate->copy()->setTime(17, 0, 0);
                    $fiveThirtyPM = $recordDate->copy()->setTime(17, 30, 0);

                    if ($checkoutTime->greaterThanOrEqualTo($fiveThirtyPM)) {
                        $calculatedOTHours = $checkoutTime->diffInMinutes($fivePM) / 60;
                    }
                } catch (\Exception $e) {
                    $calculatedOTHours = (float) $record->over_time;
                }
            } else {
                // Fallback to pre-calculated OT if checkout time is missing
                $calculatedOTHours = (float) $record->over_time;
            }

            $totalOTHours += $calculatedOTHours;
            $totalRegularHours += max(0, $recordTotalHours - $calculatedOTHours);
        }

        // totalRegularHours and totalOTHours are already calculated above in hours
        
        
        // Paid leaves count as full working days (8 hours per day)
        $paidLeaveHours = $paidLeavesInThisMonth * $hoursPerDay;
        
        // Basic salary is earned based on actual regular hours + paid leave hours
        $earnedBasicSalary = ($totalRegularHours + $paidLeaveHours) * $hourlyRate;

        // Calculate attendance bonus (Only based on actual presence)
        $attendanceBonus = 0;
        $minAttendanceForBonus = (int) ($this->settings['production_salary_min_attendance_for_bonus'] ?? 22);
        if ($attendanceDays >= $minAttendanceForBonus) {
            $attendanceBonus = (float) $this->settings['production_salary_attendance_bonus'];
        }

        // Calculate commission
        $commission = $this->calculateCommission($employee, $startDate, $endDate, $attendanceDays);

        // Calculate overtime amount
        $overtimeAmount = $totalOTHours * $hourlyRate * (float) ($this->settings['production_salary_overtime_multiplier'] ?? 1.5);

        // Calculate gross salary
        $grossSalary = $earnedBasicSalary + $attendanceBonus + $commission + $overtimeAmount;

        // Calculate deductions (EPF/ETF should be based on basic salary only)
        $epfEmployee = 0;
        $epfEmployer = 0;
        $etf = 0;
        $epfEtfBase = $basicSalary;

        if ($isEpfEligible) {
            $epfEmployee = round($epfEtfBase * ((float) $this->settings['production_salary_epf_employee_rate'] / 100), 2);
            $epfEmployer = round($epfEtfBase * ((float) $this->settings['production_salary_epf_employer_rate'] / 100), 2);
            $etf = round($epfEtfBase * ((float) $this->settings['production_salary_etf_rate'] / 100), 2);
        }

        $netSalary = $grossSalary - $epfEmployee;

        $this->salaryData = [
            'employee_id' => $employee->id,
            'employee_emp_id' => $employee->detail->user_id ?? 'N/A',
            'employee_name' => $employee->name,
            'employee_role' => $employee->detail->work_role ?? 'N/A',
            'employee_phone' => $employee->phone ?? 'N/A',
            'employee_email' => $employee->email ?? 'N/A',
            'working_days' => $workingDaysInMonth,
            'paid_leave_days' => $paidLeavesInThisMonth,
            'unpaid_leave_days' => $unpaidLeavesInThisMonth,
            'attendance_days' => $attendanceDays,
            'basic_salary' => round($basicSalary, 2),
            'earned_basic_salary' => round($earnedBasicSalary, 2),
            'attendance_bonus' => round($attendanceBonus, 2),
            'commission' => round($commission, 2),
            'overtime_hours' => round($totalOTHours, 2),
            'overtime_amount' => round($overtimeAmount, 2),
            'gross_salary' => round($grossSalary, 2),
            'epf_employee' => $epfEmployee,
            'epf_employer' => $epfEmployer,
            'etf' => $etf,
            'net_salary' => round($netSalary, 2),
            'month_label' => $startDate->format('F Y'),
            'month' => $this->selectedMonth,
            'year' => $this->selectedYear,
            'include_epf_etf' => $isEpfEligible,
            'hourly_rate' => round($hourlyRate, 2),
            'total_regular_hours' => round($totalRegularHours, 2),
        ];
    }

    private function calculateCommission($employee, $startDate, $endDate, $attendanceDays): float
    {
        // Get batches that either started/ended in this month OR had daily production logged in this month
        $activeBatchIds = ProductionBatchDay::whereBetween('work_date', [$startDate, $endDate])
            ->pluck('production_batch_id')
            ->unique()
            ->toArray();

        $batches = ProductionBatch::whereIn('id', $activeBatchIds)
            ->orWhere(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate]);
            })
            ->get();

        $totalCommission = 0;
        $totalProducedItems = 0;
        $totalAttendanceDays = 0;

        foreach ($batches as $batch) {
            // Check if employee was part of this batch
            $isSupervisor = $batch->supervisor_id === $employee->id;
            $isWorker = $batch->staffMembers()->where('user_id', $employee->id)->exists();

            if (!$isSupervisor && !$isWorker) {
                continue;
            }

            // Get employee's attendance days in this batch period within the month
            $batchStart = $startDate->max(Carbon::parse($batch->start_date));
            $batchEnd = $endDate->min($batch->end_date ? Carbon::parse($batch->end_date) : $endDate);

            $employeeAttendanceInBatch = Attendance::where('user_id', $employee->id)
                ->whereBetween('date', [$batchStart, $batchEnd])
                ->where('status', 'present')
                ->count();

            // Get total produced items for this specific month
            $producedItems = (int) ProductionBatchDay::where('production_batch_id', $batch->id)
                ->whereBetween('work_date', [$startDate, $endDate])
                ->sum('produced_qty');

            // If no daily records exist, but the batch was fully contained in this month, use completed_qty as fallback
            if ($producedItems === 0 && $batch->start_date >= $startDate && $batch->end_date && $batch->end_date <= $endDate) {
                $producedItems = (int) ($batch->completed_qty ?? $batch->target_qty);
            }

            $totalProducedItems += $producedItems;

            // Get total attendance days for all team members in this batch within the month
            $allStaffInBatch = [$batch->supervisor_id];
            $allStaffInBatch = array_merge($allStaffInBatch, $batch->staffMembers->pluck('id')->toArray());

            $totalTeamAttendance = 0;
            foreach (array_unique($allStaffInBatch) as $staffId) {
                $staffAttendance = Attendance::where('user_id', $staffId)
                    ->whereBetween('date', [$batchStart, $batchEnd])
                    ->where('status', 'present')
                    ->count();
                $totalTeamAttendance += $staffAttendance;
            }

            // Calculate batch commission based on this month's production
            if ($totalTeamAttendance > 0 && $producedItems > 0) {
                $batchCommission = $this->calculateBatchCommission($producedItems);
                $dailyCommissionRate = $batchCommission / $totalTeamAttendance;

                // Determine if employee gets full or half commission
                $minAttendanceForFull = (int) $this->settings['production_salary_min_attendance_full_commission'];
                $commissionMultiplier = $employeeAttendanceInBatch >= $minAttendanceForFull ? 1 : 0.5;

                $employeeCommission = $dailyCommissionRate * $employeeAttendanceInBatch * $commissionMultiplier;

                // Supervisor gets double commission
                if ($isSupervisor) {
                    $employeeCommission *= (float) $this->settings['production_salary_supervisor_commission_multiplier'];
                }

                $totalCommission += $employeeCommission;
            }

            $totalAttendanceDays += $employeeAttendanceInBatch;
        }

        return $totalCommission;
    }

    private function calculateBatchCommission($producedItems): float
    {
        $threshold = (int) $this->settings['production_commission_threshold_items'];
        $rateUpto = (float) $this->settings['production_commission_rate_upto_threshold'];
        $rateAfter = (float) $this->settings['production_commission_rate_after_threshold'];

        if ($producedItems <= $threshold) {
            return $producedItems * $rateUpto;
        }

        return ($threshold * $rateUpto) + (($producedItems - $threshold) * $rateAfter);
    }

    public function generateSalary(): void
    {
        if (!$this->selectedEmployee || !$this->selectedMonth || !$this->selectedYear) {
            $this->dispatch('notify', type: 'error', message: 'Please select employee and month');
            return;
        }

        // Block duplicate salary generation for the same employee/month/year
        $existingRecord = MonthlySalary::withTrashed()
            ->where('user_id', $this->selectedEmployee)
            ->where('month', $this->selectedMonth)
            ->where('year', $this->selectedYear)
            ->first();

        if ($existingRecord) {
            if ($existingRecord->trashed()) {
                // If it was previously soft-deleted, completely remove it to allow re-generation
                $existingRecord->forceDelete();
            } else {
                $this->salaryAlreadyExists = true;
                $this->dispatch('notify', type: 'error', message: 'Salary already exists for this employee in this month. Cannot generate again.');
                return;
            }
        }

        MonthlySalary::updateOrCreate(
            [
                'user_id' => $this->selectedEmployee,
                'month' => $this->selectedMonth,
                'year' => $this->selectedYear,
            ],
            [
                'working_days' => $this->salaryData['working_days'] ?? 0,
                'attendance_days' => $this->salaryData['attendance_days'] ?? 0,
                'paid_leave_days' => $this->salaryData['paid_leave_days'] ?? 0,
                'basic_salary' => $this->salaryData['earned_basic_salary'] ?? 0,
                'attendance_bonus' => $this->salaryData['attendance_bonus'] ?? 0,
                'commission' => $this->salaryData['commission'] ?? 0,
                'overtime_hours' => $this->salaryData['overtime_hours'] ?? 0,
                'overtime_amount' => $this->salaryData['overtime_amount'] ?? 0,
                'gross_salary' => $this->salaryData['gross_salary'] ?? 0,
                'epf_employee' => $this->salaryData['epf_employee'] ?? 0,
                'epf_employer' => $this->salaryData['epf_employer'] ?? 0,
                'etf' => $this->salaryData['etf'] ?? 0,
                'net_salary' => $this->salaryData['net_salary'] ?? 0,
                'include_epf_etf' => $this->salaryData['include_epf_etf'] ?? false,
                'status' => 'generated',
            ]
        );

        $this->dispatch('notify', type: 'success', message: 'Salary generated successfully!');
        $this->salaryAlreadyExists = true;
    }

    public function editSalary($salaryId): void
    {
        // Toggle editing mode
        if (in_array($salaryId, $this->editingEmployees)) {
            $key = array_search($salaryId, $this->editingEmployees);
            unset($this->editingEmployees[$key]);
        } else {
            $this->editingEmployees[] = $salaryId;
        }
    }

    public $confirmingSalaryDeletion = null;

    public function confirmDelete($salaryId): void
    {
        $this->confirmingSalaryDeletion = $salaryId;
        $this->dispatch('open-delete-modal');
    }

    public function deleteSalaryConfirmed(): void
    {
        if ($this->confirmingSalaryDeletion) {
            $salary = MonthlySalary::find($this->confirmingSalaryDeletion);
            if ($salary && $salary->status !== 'paid') {
                $salary->forceDelete();
                $this->dispatch('notify', type: 'success', message: 'Salary deleted successfully!');
                $this->dispatch('close-delete-modal');
            } else {
                $this->dispatch('notify', type: 'error', message: 'Cannot delete paid salary records.');
            }
            $this->confirmingSalaryDeletion = null;
        }
    }

    public function getEmployeesProperty()
    {
        return User::where('role', 'staff')
            ->where('module', 'production')
            ->with('detail')
            ->orderBy('name')
            ->get();
    }

    public function getAvailableMonthsProperty()
    {
        $months = [];
        $now = now();

        // Show only the last 5 previous months, excluding the current month
        for ($i = 1; $i <= 5; $i++) {
            $date = $now->copy()->subMonths($i);

            if ((int) $date->format('Y') !== (int) $this->selectedYear) {
                continue;
            }

            $months[] = [
                'value' => (int) $date->format('m'),
                'label' => $date->format('F'),
            ];
        }

        return array_reverse($months);
    }

    public function getAvailableYearsProperty()
    {
        $now = now();
        $years = [];

        for ($i = 1; $i <= 5; $i++) {
            $years[] = (int) $now->copy()->subMonths($i)->format('Y');
        }

        return array_values(array_unique($years));
    }

    public function getGeneratedSalariesProperty()
    {
        return MonthlySalary::where('month', $this->selectedMonth)
            ->where('year', $this->selectedYear)
            ->with('user.detail')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public $selectedSalary = null;

    public function viewPayslip($salaryId): void
    {
        $this->selectedSalary = MonthlySalary::with('user.detail')->find($salaryId);
        $this->dispatch('open-payslip-modal');
    }

    public function render()
    {
        return view('livewire.production.admin.monthly-salary-report', [
            'employees' => $this->employees,
            'availableYears' => $this->availableYears,
            'availableMonths' => $this->availableMonths,
            'generatedSalaries' => $this->generatedSalaries,
            'settings' => $this->settings,
        ]);
    }
}
