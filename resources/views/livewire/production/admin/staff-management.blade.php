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
            margin-bottom: 1.5rem;
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

        .role-pill {
            border-radius: 20px;
            padding: 0.55rem 1.1rem;
            font-size: 0.72rem;
            font-weight: 800;
            text-transform: uppercase;
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            color: #0284c7;
            box-shadow: 0 2px 6px rgba(2, 132, 199, 0.15);
        }

        .action-btn {
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem;
        }

        .detail-box {
            background: linear-gradient(135deg, #f8fafc 0%, #f0f4fa 100%);
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.2rem 1.5rem;
        }

        .detail-label {
            font-size: 0.72rem;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }

        .detail-value {
            font-weight: 700;
            color: #0f172a;
            word-break: break-word;
            font-size: 1rem;
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
    </style>
    @endpush

    <div class="section-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="section-title">Production Staff</h2>
                <p class="section-subtitle mb-0">Create and manage production workers, supervisors, cleaners, and oditers</p>
            </div>
            <button class="btn-custom" wire:click="openCreateModal">
                <i class="bi bi-person-plus me-1"></i> Create Staff
            </button>
        </div>

        <div class="mb-3" style="max-width: 320px;">
            <input type="text" class="form-control" placeholder="Search name, NIC, phone, role" wire:model.live="search">
        </div>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr class="text-uppercase small fw-bold">
                        <th>Name</th>
                        <th>Phone</th>
                        <th>NIC</th>
                        <th>Role</th>
                        <th>Joining Date</th>
                        <th class="text-end">Basic Salary</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($staffs as $staff)
                    <tr>
                        <td>
                            <div class="fw-bold">{{ $staff->name }}</div>
                            <small class="text-muted">{{ $staff->email }}</small>
                        </td>
                        <td>{{ $staff->contact }}</td>
                        <td>{{ $staff->detail->nic_num ?? '-' }}</td>
                        <td><span class="role-pill">{{ $staff->detail->work_role ?? '-' }}</span></td>
                        <td>{{ $staff->detail?->join_date?->format('M d, Y') ?? '-' }}</td>
                        <td class="text-end fw-bold">${{ number_format((float) ($staff->detail->basic_salary ?? 0), 2) }}</td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-light action-btn" wire:click="openViewModal({{ $staff->id }})" title="View">
                                <i class="bi bi-eye text-primary"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-light action-btn ms-1" wire:click="openEditModal({{ $staff->id }})" title="Edit">
                                <i class="bi bi-pencil-fill text-muted"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-light action-btn ms-1" wire:click="openDeleteModal({{ $staff->id }})" title="Delete">
                                <i class="bi bi-trash text-danger"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center   py-3   text-muted">No production staff found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">{{ $staffs->links() }}</div>
    </div>

    @if($showCreateModal)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content" style="border-radius: 14px;">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Create Production Staff</h5>
                    <button type="button" class="btn-close" wire:click="closeCreateModal"></button>
                </div>
                <div class="modal-body  p-3">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Name</label>
                            <input type="text" class="form-control" wire:model="name" placeholder="Staff name">
                            @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Phone Number</label>
                            <input type="text" class="form-control" wire:model="phone_number" placeholder="Phone">
                            @error('phone_number') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">NIC</label>
                            <input type="text" class="form-control" wire:model="nic" placeholder="NIC number">
                            @error('nic') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Address</label>
                            <textarea class="form-control" rows="2" wire:model="address" placeholder="Address"></textarea>
                            @error('address') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Joining Date</label>
                            <input type="date" class="form-control" wire:model="joining_date">
                            @error('joining_date') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Basic Salary</label>
                            <input type="number" min="0" step="0.01" class="form-control" wire:model="basic_salary" placeholder="0.00">
                            @error('basic_salary') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Staff Role</label>
                            <select class="form-select" wire:model="staff_role">
                                <option value="worker">Worker</option>
                                <option value="supervisor">Supervisor</option>
                                <option value="cleaner">Cleaner</option>
                                <option value="oditer">Oditer</option>
                            </select>
                            @error('staff_role') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" class="form-control" wire:model="email" placeholder="Email for login">
                            @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Password</label>
                            <input type="password" class="form-control" wire:model="password" placeholder="Password">
                            @error('password') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-12">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" id="epf_eligible_toggle" wire:model="is_epf_eligible">
                                <label class="form-check-label fw-bold" for="epf_eligible_toggle">
                                    Eligible for EPF / ETF Deductions
                                </label>
                            </div>
                            <small class="text-muted">If enabled, EPF/ETF will be calculated automatically for this staff member every month.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" wire:click="closeCreateModal">Cancel</button>
                    <button class="btn-custom" wire:click="saveStaff">Save Staff</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($showViewModal)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="border-radius: 14px;">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Staff Details</h5>
                    <button type="button" class="btn-close" wire:click="closeViewModal"></button>
                </div>
                <div class="modal-body  p-3">
                    <div class="detail-grid">
                        <div class="detail-box">
                            <div class="detail-label">Name</div>
                            <div class="detail-value">{{ $view_name }}</div>
                        </div>
                        <div class="detail-box">
                            <div class="detail-label">Email</div>
                            <div class="detail-value">{{ $view_email }}</div>
                        </div>
                        <div class="detail-box">
                            <div class="detail-label">Phone</div>
                            <div class="detail-value">{{ $view_phone }}</div>
                        </div>
                        <div class="detail-box">
                            <div class="detail-label">NIC</div>
                            <div class="detail-value">{{ $view_nic }}</div>
                        </div>
                        <div class="detail-box">
                            <div class="detail-label">Role</div>
                            <div class="detail-value">{{ $view_staff_role }}</div>
                        </div>
                        <div class="detail-box">
                            <div class="detail-label">Status</div>
                            <div class="detail-value">{{ $view_status }}</div>
                        </div>
                        <div class="detail-box">
                            <div class="detail-label">Joining Date</div>
                            <div class="detail-value">{{ $view_joining_date }}</div>
                        </div>
                        <div class="detail-box">
                            <div class="detail-label">Basic Salary</div>
                            <div class="detail-value">${{ number_format((float) $view_basic_salary, 2) }}</div>
                        </div>
                        <div class="detail-box">
                            <div class="detail-label">EPF / ETF Eligible</div>
                            <div class="detail-value">
                                @if($view_is_epf_eligible)
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-secondary">No</span>
                                @endif
                            </div>
                        </div>
                        <div class="detail-box" style="grid-column: 1 / -1;">
                            <div class="detail-label">Address</div>
                            <div class="detail-value">{{ $view_address }}</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" wire:click="closeViewModal">Close</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($showDeleteModal)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 14px;">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-danger">Delete Staff</h5>
                    <button type="button" class="btn-close" wire:click="closeDeleteModal"></button>
                </div>
                <div class="modal-body  p-3">
                    <p class="mb-2">Are you sure you want to delete this staff member?</p>
                    @if($deleteBlocked)
                    <div class="alert alert-danger mb-0">{{ $deleteBlockMessage }}</div>
                    @else
                    <div class="alert alert-warning mb-0">This action cannot be undone.</div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" wire:click="closeDeleteModal">Cancel</button>
                    <button class="btn btn-danger" wire:click="deleteStaff" @disabled($deleteBlocked)>Delete</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>