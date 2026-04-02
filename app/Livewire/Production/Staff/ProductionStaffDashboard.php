<?php

namespace App\Livewire\Production\Staff;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.production.staff')]
class ProductionStaffDashboard extends Component
{
    public function render()
    {
        return view('livewire.production.staff.production-staff-dashboard');
    }
}
