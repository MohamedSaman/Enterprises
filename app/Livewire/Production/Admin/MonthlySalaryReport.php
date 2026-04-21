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
    public $includeEpfEtf = false;
    public $salaryData = [];
    public $editingEmployees = [];

    // Settings cache (public so Livewire keeps it between requests)
    public array $settings = [];

    public function mount(): void
    {
        $now = now();
        $this->selectedYear = (int) $now->format('Y');
        $this->selectedMonth = (int) $now->subMonth()->format('m');
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
    }

    public function updatedSelectedEmployee(): void
    {
        if ($this->selectedEmployee) {
            $this->calculateSalary();
        } else {
            $this->salaryData = [];
        }
    }

    public function updatedIncludeEpfEtf(): void
    {
        if ($this->selectedEmployee) {
            $this->calculateSalary();
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

        $startDate = Carbon::createFromFormat('Y-m', "{$this->selectedYear}-{$this->selectedMonth}")->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        // Get attendance data
        $attendanceRecords = Attendance::where('user_id', $employee->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->where('status', 'present')
            ->get();

        $attendanceDays = $attendanceRecords->count();
        $workingDays = (int) $this->settings['production_salary_working_days_per_month'];
        $paidLeaveDays = round((int) $this->settings['production_salary_paid_leave_days'] / 12, 1);

        // Adjust working days for paid leave
        $effectiveWorkingDays = max($workingDays - $paidLeaveDays, 0);

        // Get basic salary
        $basicSalary = (float) ($employee->detail->basic_salary ?? 0);

        // Calculate attendance bonus
        $attendanceBonus = $attendanceDays * (float) $this->settings['production_salary_attendance_bonus'];

        // Calculate commission
        $commission = $this->calculateCommission($employee, $startDate, $endDate, $attendanceDays);

        // Calculate overtime
        $overtimeHours = $attendanceRecords->sum('over_time');
        $hourlyRate = $basicSalary / 160; // 160 hours per month (20 days * 8 hours)
        $overtimeAmount = $overtimeHours * $hourlyRate * (float) $this->settings['production_salary_overtime_multiplier'];

        // Calculate gross salary
        $grossSalary = $basicSalary + $attendanceBonus + $commission + $overtimeAmount;

        // Calculate deductions (EPF/ETF should be based on basic salary only)
        $epfEmployee = 0;
        $epfEmployer = 0;
        $etf = 0;
        $epfEtfBase = $basicSalary;

        if ($this->includeEpfEtf) {
            $epfEmployee = round($epfEtfBase * ((float) $this->settings['production_salary_epf_employee_rate'] / 100), 2);
            $epfEmployer = round($epfEtfBase * ((float) $this->settings['production_salary_epf_employer_rate'] / 100), 2);
            $etf = round($epfEtfBase * ((float) $this->settings['production_salary_etf_rate'] / 100), 2);
        }

        $netSalary = $grossSalary - $epfEmployee;

        $this->salaryData = [
            'employee_id' => $employee->id,
            'employee_name' => $employee->name,
            'employee_role' => $employee->detail->work_role ?? 'N/A',
            'working_days' => $workingDays,
            'paid_leave_days' => $paidLeaveDays,
            'effective_working_days' => $effectiveWorkingDays,
            'attendance_days' => $attendanceDays,
            'basic_salary' => round($basicSalary, 2),
            'attendance_bonus' => round($attendanceBonus, 2),
            'commission' => round($commission, 2),
            'overtime_hours' => round($overtimeHours, 2),
            'overtime_amount' => round($overtimeAmount, 2),
            'gross_salary' => round($grossSalary, 2),
            'epf_employee' => $epfEmployee,
            'epf_employer' => $epfEmployer,
            'etf' => $etf,
            'net_salary' => round($netSalary, 2),
            'month_label' => $startDate->format('F Y'),
            'month' => $this->selectedMonth,
            'year' => $this->selectedYear,
        ];
    }

    private function calculateCommission($employee, $startDate, $endDate, $attendanceDays): float
    {
        // Get all production batches that were active during this period
        $batches = ProductionBatch::whereBetween('start_date', [$startDate, $endDate])
            ->orWhereBetween('end_date', [$startDate, $endDate])
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

            // Get employee's attendance days in this batch period
            $batchStart = $startDate->max($batch->start_date);
            $batchEnd = $endDate->min($batch->end_date ?? $endDate);

            $employeeAttendanceInBatch = Attendance::where('user_id', $employee->id)
                ->whereBetween('date', [$batchStart, $batchEnd])
                ->where('status', 'present')
                ->count();

            // Get total produced items
            $producedItems = (int) ($batch->completed_qty ?? $batch->target_qty);
            $totalProducedItems += $producedItems;

            // Get total attendance days for all team members in this batch
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

            // Calculate batch commission
            if ($totalTeamAttendance > 0) {
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

        // Check if salary already exists for this month/year
        $existingRecord = MonthlySalary::where('user_id', $this->selectedEmployee)
            ->where('month', $this->selectedMonth)
            ->where('year', $this->selectedYear)
            ->first();

        if ($existingRecord && $existingRecord->status !== 'draft') {
            $this->dispatch('notify', type: 'error', message: 'Salary already generated for this month. Cannot regenerate.');
            return;
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
                'basic_salary' => $this->salaryData['basic_salary'] ?? 0,
                'attendance_bonus' => $this->salaryData['attendance_bonus'] ?? 0,
                'commission' => $this->salaryData['commission'] ?? 0,
                'overtime_hours' => $this->salaryData['overtime_hours'] ?? 0,
                'overtime_amount' => $this->salaryData['overtime_amount'] ?? 0,
                'gross_salary' => $this->salaryData['gross_salary'] ?? 0,
                'epf_employee' => $this->salaryData['epf_employee'] ?? 0,
                'epf_employer' => $this->salaryData['epf_employer'] ?? 0,
                'etf' => $this->salaryData['etf'] ?? 0,
                'net_salary' => $this->salaryData['net_salary'] ?? 0,
                'include_epf_etf' => $this->includeEpfEtf,
                'status' => 'generated',
            ]
        );

        $this->dispatch('notify', type: 'success', message: 'Salary generated successfully!');
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

    public function deleteSalary($salaryId): void
    {
        $salary = MonthlySalary::find($salaryId);
        if ($salary && $salary->status !== 'paid') {
            $salary->delete();
            $this->dispatch('notify', type: 'success', message: 'Salary deleted successfully!');
        } else {
            $this->dispatch('notify', type: 'error', message: 'Cannot delete paid salary records.');
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

        // Show only past months (up to 12 months back)
        for ($i = 1; $i <= 12; $i++) {
            $date = $now->copy()->subMonths($i);
            $months[$date->format('m')] = $date->format('F');
        }

        return array_reverse($months, true);
    }

    public function getGeneratedSalariesProperty()
    {
        return MonthlySalary::where('month', $this->selectedMonth)
            ->where('year', $this->selectedYear)
            ->with('user.detail')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function render()
    {
        return view('livewire.production.admin.monthly-salary-report', [
            'employees' => $this->employees,
            'availableMonths' => $this->availableMonths,
            'generatedSalaries' => $this->generatedSalaries,
            'settings' => $this->settings,
        ]);
    }
}
