<div class="dashboard-wrapper">
    @push('styles')
    <style>
        .dashboard-wrapper {
            background-color: #f8faff;
            min-height: 100vh;
            padding: 1rem 0;
        }

        .section-card {
            background: #fff;
            border-radius: 12px;
            padding: 2rem;
            border: 1px solid #eef2f6;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        }

        .btn-custom {
            background: #00a3e0;
            color: #fff;
            border: 0;
            border-radius: 10px;
            font-weight: 800;
            padding: 0.75rem 1.25rem;
        }

        .btn-custom:hover {
            color: #fff;
        }
    </style>
    @endpush

    <div class="section-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">Production Suppliers</h2>
                <p class="text-muted mb-0">Manage suppliers used in production purchase orders.</p>
            </div>
            <button class="btn-custom" wire:click="openCreateModal">
                <i class="bi bi-plus-lg me-1"></i> Add Supplier
            </button>
        </div>

        <div class="mb-3" style="max-width: 320px;">
            <input type="text" class="form-control" placeholder="Search supplier" wire:model.live="search">
        </div>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr class="small text-uppercase fw-bold">
                        <th>Name</th>
                        <th>Business</th>
                        <th>Contact</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                    <tr>
                        <td class="fw-bold">{{ $supplier->name }}</td>
                        <td>{{ $supplier->businessname ?: '-' }}</td>
                        <td>{{ $supplier->contact ?: '-' }}</td>
                        <td>{{ $supplier->phone ?: '-' }}</td>
                        <td>{{ $supplier->email ?: '-' }}</td>
                        <td>{{ ucfirst($supplier->status ?: 'active') }}</td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-primary" wire:click="openEditModal({{ $supplier->id }})">Edit</button>
                            <button class="btn btn-sm btn-outline-danger" onclick="confirm('Delete this supplier?') || event.stopImmediatePropagation()" wire:click="deleteSupplier({{ $supplier->id }})">Delete</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">No suppliers found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">{{ $suppliers->links() }}</div>
    </div>

    @if($showModal)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="border-radius: 14px;">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">{{ $editingSupplierId ? 'Edit Supplier' : 'Add Supplier' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Supplier Name</label>
                            <input type="text" class="form-control" wire:model="name">
                            @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Business Name</label>
                            <input type="text" class="form-control" wire:model="businessname">
                            @error('businessname') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Contact</label>
                            <input type="text" class="form-control" wire:model="contact">
                            @error('contact') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Phone</label>
                            <input type="text" class="form-control" wire:model="phone">
                            @error('phone') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" class="form-control" wire:model="email">
                            @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Status</label>
                            <select class="form-select" wire:model="status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                            @error('status') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Address</label>
                            <input type="text" class="form-control" wire:model="address">
                            @error('address') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Notes</label>
                            <textarea class="form-control" rows="2" wire:model="notes"></textarea>
                            @error('notes') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" wire:click="closeModal">Cancel</button>
                    <button class="btn btn-primary" wire:click="saveSupplier">Save Supplier</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>