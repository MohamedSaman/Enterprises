<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get staff users (excluding admin)
        $staffUsers = User::where('role', 'staff')->get();

        if ($staffUsers->isEmpty()) {
            $this->command->info('No staff users found. Please run UserSeeder first.');
            return;
        }

        $currentMonth = now()->startOfMonth();
        $endOfMonth = $currentMonth->copy()->endOfMonth();

        // Generate attendance records for the current month
        foreach ($staffUsers as $user) {
            for ($date = $currentMonth->copy(); $date->lte($endOfMonth); $date->addDay()) {
                // Skip weekends (Saturday and Sunday)
                if ($date->isWeekend()) {
                    continue;
                }

                // 90% attendance probability
                if (rand(1, 100) > 90) {
                    continue;
                }

                $checkInTime = $date->copy()->setTime(rand(8, 9), rand(0, 59));
                $breakStartTime = $checkInTime->copy()->addHours(3)->addMinutes(rand(0, 30));
                $breakEndTime = $breakStartTime->copy()->addMinutes(rand(45, 90));
                $checkOutTime = $breakEndTime->copy()->addHours(4)->addMinutes(rand(0, 30));

                $timeWorked = $checkOutTime->diffInMinutes($checkInTime) - $breakStartTime->diffInMinutes($breakEndTime);
                $timeWorkedHours = round($timeWorked / 60, 2);

                Attendance::create([
                    'user_id' => $user->id,
                    'fingerprint_id' => null,
                    'date' => $date->toDateString(),
                    'check_in' => $checkInTime->toTimeString(),
                    'break_start' => $breakStartTime->toTimeString(),
                    'break_end' => $breakEndTime->toTimeString(),
                    'check_out' => $checkOutTime->toTimeString(),
                    'time_worked' => $timeWorkedHours,
                    'late_hours' => rand(0, 2),
                    'over_time' => $timeWorkedHours > 8 ? round($timeWorkedHours - 8, 2) : 0,
                    'status' => 'present',
                    'present_status' => 'ontime',
                    'description' => 'Regular attendance',
                ]);
            }
        }

        $this->command->info('Attendance seeder completed!');
    }
}
