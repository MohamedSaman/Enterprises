<?php

namespace App\Livewire\Production\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.production.admin')]
class ProductionAdminDashboard extends Component
{
    public function render()
    {
        return view('livewire.production.admin.production-admin-dashboard');
    }
}
