<?php

namespace App\Livewire\Production\Admin;

use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.production.admin')]
#[Title('Production Staff')]
class StaffManagement extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showCreateModal = false;

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $phone_number = '';
    public string $address = '';
    public string $nic = '';
    public $basic_salary = null;
    public string $joining_date = '';
    public string $staff_role = 'worker';

    public function mount()
    {
        $this->joining_date = now()->format('Y-m-d');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
    }

    private function resetForm(): void
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->phone_number = '';
        $this->address = '';
        $this->nic = '';
        $this->basic_salary = null;
        $this->joining_date = now()->format('Y-m-d');
        $this->staff_role = 'worker';
        $this->resetErrorBag();
    }

    public function saveStaff(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string|max:1000',
            'nic' => 'required|string|max:100|unique:user_details,nic_num',
            'basic_salary' => 'required|numeric|min:0',
            'joining_date' => 'required|date',
            'staff_role' => 'required|in:worker,supervisor,cleaner,oditer',
        ]);

        DB::transaction(function () {
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'contact' => $this->phone_number,
                'role' => 'staff',
                'module' => 'production',
            ]);

            UserDetail::create([
                'user_id' => $user->id,
                'address' => $this->address,
                'nic_num' => $this->nic,
                'basic_salary' => $this->basic_salary,
                'join_date' => $this->joining_date,
                'work_role' => $this->staff_role,
                'status' => 'active',
                'work_type' => 'monthly',
            ]);
        });

        $this->dispatch('alert', ['message' => 'Production staff created successfully!', 'type' => 'success']);
        $this->closeCreateModal();
        $this->resetForm();
        $this->resetPage();
    }

    public function render()
    {
        $staffs = User::with('detail')
            ->where('role', 'staff')
            ->where(function ($q) {
                $q->whereNull('module')
                    ->orWhere('module', 'production')
                    ->orWhere('module', 'both');
            })
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('contact', 'like', '%' . $this->search . '%')
                    ->orWhereHas('detail', function ($sub) {
                        $sub->where('nic_num', 'like', '%' . $this->search . '%')
                            ->orWhere('work_role', 'like', '%' . $this->search . '%');
                    });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.production.admin.staff-management', [
            'staffs' => $staffs,
        ]);
    }
}
