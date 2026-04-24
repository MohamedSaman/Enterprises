<?php

namespace App\Livewire\Production\Admin;

use App\Models\Setting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.production.admin')]
#[Title('Production Settings')]
class ProductionSettings extends Component
{
    public float $size_s_ton_per_1000 = 0.3;
    public float $size_m_ton_per_1000 = 0.5;
    public float $size_l_ton_per_1000 = 0.75;
    public bool $showSizeSettingsSection = false;

    public int $commission_threshold_items = 10000;
    public float $commission_rate_upto_threshold = 10;
    public float $commission_rate_after_threshold = 15;
    public bool $showCommissionSettingsSection = false;

    public float $rmb_to_lkr_rate = 92;
    public bool $showCurrencySettingsSection = false;

    public int $salary_working_days_per_month = 25;
    public int $salary_working_hours_per_day = 8;
    public float $salary_paid_leave_days = 14;
    public float $salary_attendance_bonus = 500;
    public float $salary_overtime_multiplier = 1.5;
    public float $salary_etf_rate = 3;
    public float $salary_epf_employee_rate = 8;
    public float $salary_epf_employer_rate = 12;
    public float $salary_supervisor_commission_multiplier = 2;
    public int $salary_min_attendance_full_commission = 20;
    public int $salary_min_attendance_for_bonus = 22;
    public bool $showSalarySettingsSection = false;

    public function mount(): void
    {
        $this->loadSettings();
    }

    public function loadSettings(): void
    {
        $settings = Setting::query()
            ->whereIn('key', [
                'production_size_factor_s',
                'production_size_factor_m',
                'production_size_factor_l',
                'production_commission_threshold_items',
                'production_commission_rate_upto_threshold',
                'production_commission_rate_after_threshold',
                'production_rmb_to_lkr_rate',
                'production_salary_working_days_per_month',
                'production_salary_working_hours_per_day',
                'production_salary_paid_leave_days',
                'production_salary_attendance_bonus',
                'production_salary_overtime_multiplier',
                'production_salary_etf_rate',
                'production_salary_epf_employee_rate',
                'production_salary_epf_employer_rate',
                'production_salary_supervisor_commission_multiplier',
                'production_salary_min_attendance_full_commission',
                'production_salary_min_attendance_for_bonus',
            ])
            ->pluck('value', 'key');

        $this->size_s_ton_per_1000 = (float) ($settings['production_size_factor_s'] ?? 0.3);
        $this->size_m_ton_per_1000 = (float) ($settings['production_size_factor_m'] ?? 0.5);
        $this->size_l_ton_per_1000 = (float) ($settings['production_size_factor_l'] ?? 0.75);

        $this->commission_threshold_items = (int) ($settings['production_commission_threshold_items'] ?? 10000);
        $this->commission_rate_upto_threshold = (float) ($settings['production_commission_rate_upto_threshold'] ?? 10);
        $this->commission_rate_after_threshold = (float) ($settings['production_commission_rate_after_threshold'] ?? 15);

        $this->rmb_to_lkr_rate = (float) ($settings['production_rmb_to_lkr_rate'] ?? 92);

        $this->salary_working_days_per_month = (int) ($settings['production_salary_working_days_per_month'] ?? 25);
        $this->salary_working_hours_per_day = (int) ($settings['production_salary_working_hours_per_day'] ?? 8);
        $this->salary_paid_leave_days = (float) ($settings['production_salary_paid_leave_days'] ?? 14);
        $this->salary_attendance_bonus = (float) ($settings['production_salary_attendance_bonus'] ?? 500);
        $this->salary_overtime_multiplier = (float) ($settings['production_salary_overtime_multiplier'] ?? 1.5);
        $this->salary_etf_rate = (float) ($settings['production_salary_etf_rate'] ?? 3);
        $this->salary_epf_employee_rate = (float) ($settings['production_salary_epf_employee_rate'] ?? 8);
        $this->salary_epf_employer_rate = (float) ($settings['production_salary_epf_employer_rate'] ?? 12);
        $this->salary_supervisor_commission_multiplier = (float) ($settings['production_salary_supervisor_commission_multiplier'] ?? 2);
        $this->salary_min_attendance_full_commission = (int) ($settings['production_salary_min_attendance_full_commission'] ?? 20);
        $this->salary_min_attendance_for_bonus = (int) ($settings['production_salary_min_attendance_for_bonus'] ?? 22);
    }

    public function saveSettings(): void
    {
        try {
            $this->validate([
                'size_s_ton_per_1000' => 'required|numeric|min:0.01',
                'size_m_ton_per_1000' => 'required|numeric|min:0.01',
                'size_l_ton_per_1000' => 'required|numeric|min:0.01',
                'commission_threshold_items' => 'required|integer|min:1',
                'commission_rate_upto_threshold' => 'required|numeric|min:0',
                'commission_rate_after_threshold' => 'required|numeric|min:0',
                'rmb_to_lkr_rate' => 'required|numeric|min:0.01',
                'salary_working_days_per_month' => 'required|integer|min:1',
                'salary_working_hours_per_day' => 'required|integer|min:1',
                'salary_paid_leave_days' => 'required|numeric|min:0',
                'salary_attendance_bonus' => 'required|numeric|min:0',
                'salary_overtime_multiplier' => 'required|numeric|min:0',
                'salary_etf_rate' => 'required|numeric|min:0',
                'salary_epf_employee_rate' => 'required|numeric|min:0',
                'salary_epf_employer_rate' => 'required|numeric|min:0',
                'salary_supervisor_commission_multiplier' => 'required|numeric|min:0',
                'salary_min_attendance_full_commission' => 'required|integer|min:1',
                'salary_min_attendance_for_bonus' => 'required|integer|min:1',
            ]);

            $this->saveSettingKey('production_size_factor_s', $this->size_s_ton_per_1000, 'Ton consumed for 1000 cages (Size S)');
            $this->saveSettingKey('production_size_factor_m', $this->size_m_ton_per_1000, 'Ton consumed for 1000 cages (Size M)');
            $this->saveSettingKey('production_size_factor_l', $this->size_l_ton_per_1000, 'Ton consumed for 1000 cages (Size L)');
            $this->saveSettingKey('production_commission_threshold_items', (float) $this->commission_threshold_items, 'Commission threshold items');
            $this->saveSettingKey('production_commission_rate_upto_threshold', $this->commission_rate_upto_threshold, 'Commission rate per item up to threshold');
            $this->saveSettingKey('production_commission_rate_after_threshold', $this->commission_rate_after_threshold, 'Commission rate per item after threshold');
            $this->saveSettingKey('production_rmb_to_lkr_rate', $this->rmb_to_lkr_rate, 'Exchange rate for 1 RMB to LKR');

            $this->saveSettingKey('production_salary_working_days_per_month', (float) $this->salary_working_days_per_month, 'Default working days per month for salary calculation');
            $this->saveSettingKey('production_salary_working_hours_per_day', (float) $this->salary_working_hours_per_day, 'Standard working hours per day');
            $this->saveSettingKey('production_salary_paid_leave_days', $this->salary_paid_leave_days, 'Yearly paid leave days');
            $this->saveSettingKey('production_salary_attendance_bonus', $this->salary_attendance_bonus, 'Attendance bonus amount per day');
            $this->saveSettingKey('production_salary_overtime_multiplier', $this->salary_overtime_multiplier, 'Overtime hour multiplier (basic hourly rate * multiplier)');
            $this->saveSettingKey('production_salary_etf_rate', $this->salary_etf_rate, 'ETF (Employee Trust Fund) rate percentage');
            $this->saveSettingKey('production_salary_epf_employee_rate', $this->salary_epf_employee_rate, 'EPF (Employees Provident Fund) employee contribution rate');
            $this->saveSettingKey('production_salary_epf_employer_rate', $this->salary_epf_employer_rate, 'EPF employer contribution rate');
            $this->saveSettingKey('production_salary_supervisor_commission_multiplier', $this->salary_supervisor_commission_multiplier, 'Supervisor gets double commission (multiplier)');
            $this->saveSettingKey('production_salary_min_attendance_full_commission', (float) $this->salary_min_attendance_full_commission, 'Minimum attendance days for full commission (below this gets half)');
            $this->saveSettingKey('production_salary_min_attendance_for_bonus', (float) $this->salary_min_attendance_for_bonus, 'Minimum attendance days required to receive the flat monthly attendance bonus');

            $this->dispatch('alert', message: 'Production settings saved successfully.', type: 'success');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('alert', message: 'Please check the form for invalid or missing values.', type: 'error');
            throw $e;
        } catch (\Exception $e) {
            $this->dispatch('alert', message: 'An unexpected error occurred while saving.', type: 'error');
        }
    }

    private function saveSettingKey(string $key, float $value, string $description): void
    {
        Setting::updateOrCreate(
            ['key' => $key],
            [
                'value' => (string) $value,
                'status' => 'active',
                'description' => $description,
                'date' => now(),
            ]
        );
    }

    public function render()
    {
        return view('livewire.production.admin.production-settings');
    }
}
