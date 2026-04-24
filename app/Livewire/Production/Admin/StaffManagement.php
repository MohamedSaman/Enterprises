<?php

namespace App\Livewire\Production\Admin;

use App\Models\ProductionBatch;
use App\Models\ProductionBatchDay;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
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
    public bool $showViewModal = false;
    public bool $showDeleteModal = false;
    public bool $isEditMode = false;
    public ?int $editingStaffId = null;
    public ?int $deletingStaffId = null;
    public bool $deleteBlocked = false;
    public string $deleteBlockMessage = '';

    public string $view_name = '';
    public string $view_email = '';
    public string $view_phone = '';
    public string $view_address = '';
    public string $view_nic = '';
    public string $view_basic_salary = '0';
    public string $view_joining_date = '';
    public string $view_staff_role = '';
    public string $view_status = '';
    public bool $view_is_epf_eligible = true;

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $phone_number = '';
    public string $address = '';
    public string $nic = '';
    public $basic_salary = null;
    public string $joining_date = '';
    public string $staff_role = 'worker';
    public bool $is_epf_eligible = true;

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
        $this->isEditMode = false;
        $this->editingStaffId = null;
        $this->showCreateModal = true;
    }

    public function openViewModal(int $staffId): void
    {
        $staff = User::with('detail')->findOrFail($staffId);

        $this->view_name = (string) $staff->name;
        $this->view_email = (string) $staff->email;
        $this->view_phone = (string) $staff->contact;
        $this->view_address = (string) ($staff->detail->address ?? '-');
        $this->view_nic = (string) ($staff->detail->nic_num ?? '-');
        $this->view_basic_salary = (string) number_format((float) ($staff->detail->basic_salary ?? 0), 2, '.', '');
        $this->view_joining_date = $staff->detail?->join_date?->format('Y-m-d') ?? '-';
        $this->view_staff_role = (string) ($staff->detail->work_role ?? '-');
        $this->view_status = (string) ($staff->detail->status ?? '-');
        $this->view_is_epf_eligible = (bool) ($staff->detail->is_epf_eligible ?? true);

        $this->showViewModal = true;
    }

    public function closeViewModal(): void
    {
        $this->showViewModal = false;
        $this->view_name = '';
        $this->view_email = '';
        $this->view_phone = '';
        $this->view_address = '';
        $this->view_nic = '';
        $this->view_basic_salary = '0';
        $this->view_joining_date = '';
        $this->view_staff_role = '';
        $this->view_status = '';
        $this->view_is_epf_eligible = true;
    }

    public function openEditModal(int $staffId): void
    {
        $staff = User::with('detail')->findOrFail($staffId);

        $this->resetForm();
        $this->isEditMode = true;
        $this->editingStaffId = $staff->id;
        $this->name = (string) $staff->name;
        $this->email = (string) $staff->email;
        $this->phone_number = (string) $staff->contact;
        $this->address = (string) ($staff->detail->address ?? '');
        $this->nic = (string) ($staff->detail->nic_num ?? '');
        $this->basic_salary = $staff->detail?->basic_salary ?? null;
        $this->joining_date = $staff->detail?->join_date?->format('Y-m-d') ?? now()->format('Y-m-d');
        $this->staff_role = (string) ($staff->detail->work_role ?? 'worker');
        $this->is_epf_eligible = (bool) ($staff->detail->is_epf_eligible ?? true);
        $this->password = '';
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->isEditMode = false;
        $this->editingStaffId = null;
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
        $this->is_epf_eligible = true;
        $this->resetErrorBag();
    }

    public function saveStaff(): void
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->editingStaffId),
            ],
            'password' => $this->isEditMode ? 'nullable|string|min:8' : 'required|string|min:8',
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string|max:1000',
            'nic' => [
                'required',
                'string',
                'max:100',
                Rule::unique('user_details', 'nic_num')->ignore(
                    optional(User::with('detail')->find($this->editingStaffId)?->detail)->user_details_id ?? null,
                    'user_details_id'
                ),
            ],
            'basic_salary' => 'required|numeric|min:0',
            'joining_date' => 'required|date',
            'staff_role' => 'required|in:worker,supervisor,cleaner,oditer',
            'is_epf_eligible' => 'required|boolean',
        ];

        $this->validate($rules);

        DB::transaction(function () {
            $user = $this->editingStaffId ? User::findOrFail($this->editingStaffId) : new User();

            $user->name = $this->name;
            $user->email = $this->email;
            $user->contact = $this->phone_number;
            $user->role = 'staff';
            $user->module = 'production';

            if (!$this->editingStaffId || trim($this->password) !== '') {
                $user->password = Hash::make($this->password);
            }

            $user->save();

            $detail = $user->detail ?: new UserDetail(['user_id' => $user->id]);
            $detail->user_id = $user->id;
            $detail->address = $this->address;
            $detail->nic_num = $this->nic;
            $detail->basic_salary = $this->basic_salary;
            $detail->join_date = $this->joining_date;
            $detail->work_role = $this->staff_role;
            $detail->status = 'active';
            $detail->work_type = 'monthly';
            $detail->is_epf_eligible = $this->is_epf_eligible;
            $detail->save();
        });

        $message = $this->isEditMode ? 'Production staff updated successfully!' : 'Production staff created successfully!';
        $this->dispatch('alert', ['message' => $message, 'type' => 'success']);
        $this->closeCreateModal();
        $this->resetForm();
        $this->resetPage();
    }

    public function openDeleteModal(int $staffId): void
    {
        $staff = User::with('detail')->findOrFail($staffId);

        $this->deletingStaffId = $staff->id;
        $this->deleteBlocked = false;
        $this->deleteBlockMessage = '';

        $isUsedAsSupervisor = ProductionBatch::where('supervisor_id', $staff->id)->exists();
        $isUsedAsBatchStaff = DB::table('production_batch_staff')->where('user_id', $staff->id)->exists();
        $isUsedInLogs = ProductionBatchDay::where('recorded_by', $staff->id)->exists();

        if ($isUsedAsSupervisor || $isUsedAsBatchStaff || $isUsedInLogs) {
            $this->deleteBlocked = true;
            $this->deleteBlockMessage = 'This staff member is linked with production batches/logs and cannot be deleted.';
        }

        $this->showDeleteModal = true;
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->deletingStaffId = null;
        $this->deleteBlocked = false;
        $this->deleteBlockMessage = '';
    }

    public function deleteStaff(): void
    {
        if (!$this->deletingStaffId) {
            $this->closeDeleteModal();
            return;
        }

        if ($this->deleteBlocked) {
            return;
        }

        DB::transaction(function () {
            $staff = User::with('detail')->findOrFail($this->deletingStaffId);
            $staff->detail()?->delete();
            $staff->delete();
        });

        $this->closeDeleteModal();
        $this->dispatch('alert', ['message' => 'Production staff deleted successfully!', 'type' => 'success']);
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
