<?php

namespace Database\Seeders;

use App\Models\User;
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
        User::create([
            'name' => 'Staff',
            'email' => 'staff@gmail.com',
            'password' => Hash::make('staff@1213'),
            'role' => 'staff',
            'contact' => '0776657107',
            'module' => 'invontery',
        ]);

        // Create Admin User
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin@1213'),
            'role' => 'admin',
            'contact' => '0717894272',
            'module' => 'invontery',
        ]);

        // Create Admin User
        User::create([
            'name' => 'Production Admin',
            'email' => 'admin@production.com',
            'password' => Hash::make('admin@1213'),
            'role' => 'admin',
            'contact' => '0717894272',
            'module' => 'production',
        ]);


    }
}
