<?php

namespace App\Livewire\Production\Staff;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.production.staff')]
#[Title('Production Settings')]
class ProductionStaffSettings extends Component
{
    public function render()
    {
        return view('livewire.production.staff.production-staff-settings');
    }
}
