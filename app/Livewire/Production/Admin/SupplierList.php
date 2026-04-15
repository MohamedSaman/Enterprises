<?php

namespace App\Livewire\Production\Admin;

use App\Models\ProductSupplier;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.production.admin')]
#[Title('Supplier List')]
class SupplierList extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $showModal = false;
    public ?int $editingSupplierId = null;

    public string $name = '';
    public string $businessname = '';
    public string $contact = '';
    public string $address = '';
    public string $email = '';
    public string $phone = '';
    public string $status = 'active';
    public string $notes = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(int $supplierId): void
    {
        $supplier = ProductSupplier::findOrFail($supplierId);

        $this->editingSupplierId = $supplier->id;
        $this->name = (string) ($supplier->name ?? '');
        $this->businessname = (string) ($supplier->businessname ?? '');
        $this->contact = (string) ($supplier->contact ?? '');
        $this->address = (string) ($supplier->address ?? '');
        $this->email = (string) ($supplier->email ?? '');
        $this->phone = (string) ($supplier->phone ?? '');
        $this->status = (string) ($supplier->status ?? 'active');
        $this->notes = (string) ($supplier->notes ?? '');
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->resetValidation();
        $this->editingSupplierId = null;
        $this->name = '';
        $this->businessname = '';
        $this->contact = '';
        $this->address = '';
        $this->email = '';
        $this->phone = '';
        $this->status = 'active';
        $this->notes = '';
    }

    public function saveSupplier(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'businessname' => 'nullable|string|max:255',
            'contact' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:1000',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'status' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000',
        ]);

        ProductSupplier::updateOrCreate(
            ['id' => $this->editingSupplierId],
            [
                'name' => $this->name,
                'businessname' => $this->businessname ?: null,
                'contact' => $this->contact ?: null,
                'address' => $this->address ?: null,
                'email' => $this->email ?: null,
                'phone' => $this->phone ?: null,
                'status' => $this->status ?: 'active',
                'notes' => $this->notes ?: null,
            ]
        );

        $message = $this->editingSupplierId ? 'Supplier updated successfully.' : 'Supplier created successfully.';
        $this->dispatch('alert', ['message' => $message, 'type' => 'success']);

        $this->closeModal();
    }

    public function deleteSupplier(int $supplierId): void
    {
        $supplier = ProductSupplier::findOrFail($supplierId);
        $supplier->delete();

        $this->dispatch('alert', ['message' => 'Supplier deleted successfully.', 'type' => 'success']);
        $this->resetPage();
    }

    public function render()
    {
        $suppliers = ProductSupplier::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('businessname', 'like', '%' . $this->search . '%')
                    ->orWhere('contact', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('phone', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.production.admin.supplier-list', [
            'suppliers' => $suppliers,
        ]);
    }
}
