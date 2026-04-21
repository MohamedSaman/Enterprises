<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Staff User
        $staffUser = User::create([
            'name' => 'Staff',
            'email' => 'staff@gmail.com',
            'password' => Hash::make('staff@1213'),
            'role' => 'staff',
            'contact' => '0776657107',
            'module' => 'invontery',
        ]);

        // Create Admin User
        $adminUser = User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin@1213'),
            'role' => 'admin',
            'contact' => '0717894272',
            'module' => 'both',
        ]);

        // Create dummy staff users for production module with details
        $staffNames = ['John Silva', 'Maria Perera', 'Kumara Jayalath', 'Pushpa De Silva', 'Arjun Wijesinghe', 'Lakshmi Samarasinghe'];
        $supervisorName = 'Supervisor - Nimal Kumara';

        // Create Supervisor
        $supervisor = User::create([
            'name' => $supervisorName,
            'email' => 'supervisor@gmail.com',
            'password' => Hash::make('pass@1213'),
            'role' => 'staff',
            'contact' => '0701234567',
            'module' => 'production',
        ]);

        UserDetail::create([
            'user_id' => $supervisor->id,
            'dob' => now()->subYears(45)->toDateString(),
            'age' => 45,
            'nic_num' => 'NIC123456789',
            'address' => '123 Main Street, Colombo',
            'work_role' => 'Supervisor',
            'work_type' => 'Full-time',
            'department' => 'Production',
            'gender' => 'Male',
            'join_date' => now()->subYears(5)->toDateString(),
            'fingerprint_id' => 'FP001',
            'allowance' => ['transport' => 2000, 'meal' => 1500],
            'basic_salary' => 45000,
            'user_image' => null,
            'description' => 'Senior Production Supervisor',
            'status' => 'active',
        ]);

        // Create staff workers
        foreach ($staffNames as $index => $name) {
            $staffWorker = User::create([
                'name' => $name,
                'email' => strtolower(str_replace(' ', '.', $name)) . '@gmail.com',
                'password' => Hash::make('pass@1213'),
                'role' => 'staff',
                'contact' => '071' . str_pad($index + 1, 7, '0', STR_PAD_LEFT),
                'module' => 'production',
            ]);

            $baseSalary = 25000 + ($index * 1000);
            UserDetail::create([
                'user_id' => $staffWorker->id,
                'dob' => now()->subYears(30 + $index)->toDateString(),
                'age' => 30 + $index,
                'nic_num' => 'NIC' . str_pad($index + 1, 8, '0', STR_PAD_LEFT),
                'address' => ($index + 100) . ' Worker Street, Colombo',
                'work_role' => 'Worker',
                'work_type' => 'Full-time',
                'department' => 'Production',
                'gender' => $index % 2 == 0 ? 'Male' : 'Female',
                'join_date' => now()->subYears(2 + $index)->toDateString(),
                'fingerprint_id' => 'FP' . str_pad($index + 2, 3, '0', STR_PAD_LEFT),
                'allowance' => ['transport' => 1500, 'meal' => 1000],
                'basic_salary' => $baseSalary,
                'user_image' => null,
                'description' => 'Production Worker ' . ($index + 1),
                'status' => 'active',
            ]);
        }
    }
}
