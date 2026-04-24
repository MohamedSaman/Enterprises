<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class ProductionSalarySettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'production_salary_working_days_per_month',
                'value' => '25',
                'description' => 'Default working days per month for salary calculation',
            ],
            [
                'key' => 'production_salary_paid_leave_days',
                'value' => '14',
                'description' => 'Yearly paid leave days (converted to monthly deduction)',
            ],
            [
                'key' => 'production_salary_attendance_bonus',
                'value' => '500',
                'description' => 'Attendance bonus amount per day',
            ],
            [
                'key' => 'production_commission_threshold_items',
                'value' => '10000',
                'description' => 'Item threshold for commission rate change',
            ],
            [
                'key' => 'production_commission_rate_upto_threshold',
                'value' => '10',
                'description' => 'Commission rate per item up to threshold',
            ],
            [
                'key' => 'production_commission_rate_after_threshold',
                'value' => '15',
                'description' => 'Commission rate per item after threshold',
            ],
            [
                'key' => 'production_salary_overtime_multiplier',
                'value' => '1.5',
                'description' => 'Overtime hour multiplier (basic hourly rate * multiplier)',
            ],
            [
                'key' => 'production_salary_etf_rate',
                'value' => '3',
                'description' => 'ETF (Employee Trust Fund) rate percentage',
            ],
            [
                'key' => 'production_salary_epf_employee_rate',
                'value' => '8',
                'description' => 'EPF (Employees\' Provident Fund) employee contribution rate',
            ],
            [
                'key' => 'production_salary_epf_employer_rate',
                'value' => '12',
                'description' => 'EPF employer contribution rate',
            ],
            [
                'key' => 'production_salary_supervisor_commission_multiplier',
                'value' => '2',
                'description' => 'Supervisor gets double commission (multiplier)',
            ],
            [
                'key' => 'production_salary_min_attendance_full_commission',
                'value' => '20',
                'description' => 'Minimum attendance days for full commission (below this gets half)',
            ],
            [
                'key' => 'production_salary_min_attendance_for_bonus',
                'value' => '22',
                'description' => 'Minimum attendance days required to receive the flat monthly attendance bonus',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('Production salary settings seeded successfully!');
    }
}
