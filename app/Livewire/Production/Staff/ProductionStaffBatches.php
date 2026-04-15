<?php

namespace App\Livewire\Production\Staff;

use App\Models\ProductionBatch;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.production.staff')]
#[Title('My Production Batches')]
class ProductionStaffBatches extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $supervisorId = (int) Auth::id();

        $batches = ProductionBatch::query()
            ->where('supervisor_id', $supervisorId)
            ->with(['staffMembers', 'days'])
            ->when(trim($this->search) !== '', function ($query) {
                $query->where(function ($q) {
                    $q->where('batch_code', 'like', '%' . $this->search . '%')
                        ->orWhere('size', 'like', '%' . $this->search . '%')
                        ->orWhere('status', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.production.staff.production-staff-batches', [
            'batches' => $batches,
        ]);
    }
}
