<div class="dashboard-wrapper">
    @push('styles')
    <style>
        .dashboard-wrapper {
            background: linear-gradient(135deg, #f5f7fb 0%, #f0f4fa 100%);
            min-height: 100vh;
            padding: 1rem 0;
        }

        .section-card {
            background: #ffffff;
            border-radius: 14px;
            padding: 1.25rem;
            border: 1px solid rgba(30, 41, 59, 0.08);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
            position: relative;
            transition: all 0.3s ease;
        }

        .section-card:hover {
            box-shadow: 0 12px 36px rgba(0, 0, 0, 0.08);
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 900;
            color: #0f172a;
            margin-bottom: 0.5rem;
            letter-spacing: -0.02em;
        }

        .section-subtitle {
            font-size: 0.9rem;
            color: #64748b;
            font-weight: 500;
        }

        .btn-custom {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            color: white;
            border: none;
            padding: 0.85rem 1.85rem;
            border-radius: 10px;
            font-weight: 700;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);
        }

        .btn-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(2, 132, 199, 0.35);
            color: white;
        }

        .form-control,
        .form-select {
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            padding: 0.85rem 1rem;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #0284c7;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.1);
        }

        .form-label {
            font-weight: 700;
            color: #475569;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .table-responsive table {
            margin-bottom: 0;
        }

        .table thead th {
            border: none;
            font-size: 0.75rem;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 1.25rem 1rem;
            border-bottom: 2px solid #e2e8f0;
            background: rgba(248, 250, 252, 0.6);
        }

        .table tbody td {
            border: none;
            padding: 1.25rem 1rem;
            vertical-align: middle;
            font-weight: 500;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }

        .table tbody tr:hover {
            background: rgba(2, 132, 199, 0.03);
            transition: all 0.2s ease;
        }

        .btn-action {
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            padding: 0.5rem 0.85rem;
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }

        .btn-action-edit {
            background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
            color: white;
            box-shadow: 0 2px 6px rgba(6, 182, 212, 0.2);
        }

        .btn-action-edit:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(6, 182, 212, 0.3);
            color: white;
        }

        .btn-action-delete {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            box-shadow: 0 2px 6px rgba(239, 68, 68, 0.2);
        }

        .btn-action-delete:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
            color: white;
        }

        .modal-content {
            border-radius: 16px;
            border: 1px solid rgba(30, 41, 59, 0.08);
            box-shadow: 0 25px 60px -12px rgba(0, 0, 0, 0.15);
        }

        .modal-header {
            padding: 1.75rem 2rem;
            border-bottom: 1px solid #e2e8f0;
            background: linear-gradient(135deg, #f8fafc 0%, #f0f4fa 100%);
        }

        .modal-title {
            font-size: 1.25rem;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.01em;
        }

        .modal-body {
            padding: 1.25rem;
        }

        .btn-close {
            background: transparent;
            border: none;
            font-size: 1.4rem;
            opacity: 0.5;
            transition: all 0.2s ease;
        }

        .btn-close:hover {
            opacity: 1;
        }

        .modal-footer {
            padding: 1.5rem 2rem;
            border-top: 1px solid #e2e8f0;
            background: rgba(248, 250, 252, 0.5);
        }

        .badge-status {
            padding: 0.55rem 1.1rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .badge-active {
            background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
            color: #166534;
        }

        .badge-inactive {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #7f1d1d;
        }
    </style>
    @endpush

    <div class="section-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="section-title">Production Suppliers</h2>
                <p class="section-subtitle mb-0">Manage suppliers used in production purchase orders</p>
            </div>
            <button class="btn-custom" wire:click="openCreateModal">
                <i class="bi bi-plus-lg"></i> Add Supplier
            </button>
        </div>

        <div class="mb-3" style="max-width: 340px;">
            <input type="text" class="form-control" placeholder="🔍 Search suppliers..." wire:model.debounce-300ms="search">
        </div>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Business</th>
                        <th>Contact</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                    <tr wire:key="supplier-{{ $supplier->id }}">
                        <td>
                            <span style="color: #0f172a; font-weight: 800;">{{ $supplier->name }}</span>
                        </td>
                        <td>{{ $supplier->businessname ?: '—' }}</td>
                        <td>{{ $supplier->contact ?: '—' }}</td>
                        <td>{{ $supplier->phone ?: '—' }}</td>
                        <td style="color: #0284c7; font-weight: 700;">{{ $supplier->email ?: '—' }}</td>
                        <td>
                            <span class="badge-status {{ ($supplier->status == 'active' || !$supplier->status) ? 'badge-active' : 'badge-inactive' }}">
                                {{ ucfirst($supplier->status ?: 'active') }}
                            </span>
                        </td>
                        <td class="text-end">
                            <button class="btn btn-action btn-action-edit btn-sm" wire:click="openEditModal({{ $supplier->id }})">
                                <i class="bi bi-pencil-square me-1"></i> Edit
                            </button>
                            <button class="btn btn-action btn-action-delete btn-sm" onclick="confirm('Are you sure you want to delete this supplier?') || event.stopImmediatePropagation()" wire:click="deleteSupplier({{ $supplier->id }})">
                                <i class="bi bi-trash me-1"></i> Delete
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center   py-3">
                            <div style="color: #64748b; font-weight: 600;">No suppliers found</div>
                        </td>
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
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $editingSupplierId ? 'Edit Supplier' : 'Add New Supplier' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Supplier Name</label>
                            <input type="text" class="form-control" wire:model="name" placeholder="Enter supplier name">
                            @error('name') <span class="text-danger small d-block mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Business Name</label>
                            <input type="text" class="form-control" wire:model="businessname" placeholder="Enter business name">
                            @error('businessname') <span class="text-danger small d-block mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contact Person</label>
                            <input type="text" class="form-control" wire:model="contact" placeholder="Contact name">
                            @error('contact') <span class="text-danger small d-block mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" wire:model="phone" placeholder="Phone number">
                            @error('phone') <span class="text-danger small d-block mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" wire:model="email" placeholder="Email address">
                            @error('email') <span class="text-danger small d-block mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-select" wire:model="status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                            @error('status') <span class="text-danger small d-block mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Address</label>
                            <input type="text" class="form-control" wire:model="address" placeholder="Street address">
                            @error('address') <span class="text-danger small d-block mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" rows="2" wire:model="notes" placeholder="Additional notes..."></textarea>
                            @error('notes') <span class="text-danger small d-block mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" wire:click="closeModal">Cancel</button>
                    <button type="button" class="btn-custom" wire:click="saveSupplier">
                        <i class="bi bi-check-lg"></i> Save Supplier
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>