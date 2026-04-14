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

        .section-title {
            font-size: 1.25rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 0.35rem;
        }

        .section-subtitle {
            font-size: 0.85rem;
            color: #94a3b8;
            font-weight: 600;
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

        .role-pill {
            border-radius: 999px;
            padding: 0.35rem 0.7rem;
            font-size: 0.72rem;
            font-weight: 800;
            text-transform: uppercase;
            background: #eff6ff;
            color: #1d4ed8;
        }
    </style>
    @endpush

    <div class="section-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="section-title">Production Staff</h2>
                <p class="section-subtitle mb-0">Create and manage production workers, supervisors, cleaners, and oditers</p>
            </div>
            <button class="btn-custom" wire:click="openCreateModal">
                <i class="bi bi-person-plus me-1"></i> Create Staff
            </button>
        </div>

        <div class="mb-4" style="max-width: 320px;">
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
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">No production staff found.</td>
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
                <div class="modal-body p-4">
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
</div>