<?php

namespace App\Livewire\Production\Admin;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.production.admin')]
#[Title('Production Overview')]
class ProductionAdminDashboard extends Component
{
    public function render()
    {
        $stats = [
            ['label' => 'Material Stock', 'value' => '3,420', 'sub' => 'Units available', 'trend' => null, 'color' => '#fbbf24', 'icon' => 'bi-box-seam'],
            ['label' => 'Total Production', 'value' => '8,124', 'sub' => '+12% from last month', 'trend' => 'up', 'color' => '#3b82f6', 'icon' => 'bi-lightning-charge'],
            ['label' => 'Expenses', 'value' => '$12,450', 'sub' => 'Within budget (92%)', 'trend' => null, 'color' => '#ef4444', 'icon' => 'bi-wallet2'],
            ['label' => 'Upcoming Orders', 'value' => '42', 'sub' => '8 Priority items', 'trend' => null, 'color' => '#10b981', 'icon' => 'bi-truck'],
        ];

        $dailyLabels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        $dailyValues = [155, 178, 120, 192, 166, 110, 105]; // Modified to match bar look in image

        $recentProductions = [
            ['unit' => 'UNIT #8892-A', 'activity' => 'Quality Control', 'time' => '14:22 PM', 'status_color' => '#8a6114'],
            ['unit' => 'UNIT #8891-B', 'activity' => 'Completed', 'time' => '13:45 PM', 'status_color' => '#3b82f6'],
            ['unit' => 'UNIT #8890-X', 'activity' => 'Assembly Line 4', 'time' => '12:30 PM', 'status_color' => '#3b82f6'],
            ['unit' => 'UNIT #8889-C', 'activity' => 'Completed', 'time' => '11:15 AM', 'status_color' => '#3b82f6'],
            ['unit' => 'UNIT #8888-A', 'activity' => 'Maintenance Required', 'time' => '09:50 AM', 'status_color' => '#ef4444'],
        ];

        return view('livewire.production.admin.production-admin-dashboard', compact('stats', 'dailyLabels', 'dailyValues', 'recentProductions'));
    }
}
