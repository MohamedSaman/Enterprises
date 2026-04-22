<div class="dashboard-wrapper">
    @push('styles')
    <style>
        .dashboard-wrapper {
            background-color: #f8faff;
            min-height: 100vh;
            padding: 1rem 0;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.25rem;
            margin-bottom: 2rem;
        }

        .sample-card {
            background: #fff;
            border-radius: 8px;
            padding: 1.5rem 1.75rem;
            border: 1px solid #eef2f6;
            display: flex;
            flex-direction: column;
            position: relative;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
            height: 140px;
            justify-content: center;
        }

        .card-accent {
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            border-radius: 8px 0 0 8px;
        }

        .card-label {
            font-size: 0.75rem;
            font-weight: 800;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 0.5rem;
        }

        .card-value {
            font-size: 1.85rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 0.15rem;
            letter-spacing: -0.02em;
        }

        .card-sub {
            font-size: 0.8rem;
            color: #64748b;
            font-weight: 700;
        }

        .card-icon-box {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            width: 42px;
            height: 42px;
            background: #f1f6fc;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0088cc;
            font-size: 1.15rem;
        }

        .section-card {
            background: #fff;
            border-radius: 12px;
            padding: 2.25rem;
            border: 1px solid #eef2f6;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            display: flex;
            flex-direction: column;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2.5rem;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 0.35rem;
            letter-spacing: -0.01em;
        }

        .section-subtitle {
            font-size: 0.85rem;
            color: #94a3b8;
            font-weight: 600;
        }

        .search-container {
            max-width: 320px;
            width: 100%;
            position: relative;
        }

        .search-input {
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            border-radius: 10px;
            padding: 0.75rem 1rem 0.75rem 2.75rem;
            font-size: 0.9rem;
            font-weight: 600;
            color: #1e293b;
            transition: all 0.2s;
            width: 100%;
        }

        .search-input:focus {
            background: #fff;
            border-color: #00a3e0;
            box-shadow: 0 0 0 4px rgba(0, 163, 224, 0.1);
            outline: none;
        }

        .search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1.1rem;
        }

        .btn-custom {
            background: #00a3e0;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
        }

        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 163, 224, 0.25);
            color: #fff;
        }

        .custom-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .custom-table thead th {
            font-size: 0.7rem;
            font-weight: 800;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 1.25rem 1rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .custom-table tbody td {
            padding: 1.25rem 1rem;
            font-size: 0.95rem;
            color: #334155;
            font-weight: 600;
            border-bottom: 1px solid #f8fafc;
            vertical-align: middle;
        }

        .material-code {
            font-weight: 800;
            color: #1e293b;
        }

        .badge-status {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .badge-healthy {
            background: #ecfdf5;
            color: #10b981;
        }

        .badge-warning {
            background: #fffbeb;
            color: #f59e0b;
        }

        /* Modal Aesthetics */
        .modal-content-custom {
            border-radius: 12px;
            border: none;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
        }

        .stock-modal-content {
            overflow: hidden;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            border: 1px solid rgba(14, 165, 233, 0.12);
            box-shadow: 0 28px 60px -16px rgba(2, 132, 199, 0.18);
        }

        .modal-header-custom {
            padding: 1.5rem 2rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .stock-modal-header {
            background: linear-gradient(135deg, #f0f9ff 0%, #ecfeff 52%, #f8fafc 100%);
            border-bottom: 1px solid rgba(14, 165, 233, 0.12);
            position: relative;
        }

        .stock-modal-header::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 5px;
            height: 100%;
            background: linear-gradient(180deg, #06b6d4 0%, #22c55e 100%);
        }

        .stock-modal-title {
            color: #0f172a;
            letter-spacing: -0.02em;
        }

        .stock-modal-subtitle {
            color: #0f766e;
            font-weight: 700;
        }

        .modal-body-custom {
            padding: 1.25rem;
        }

        .stock-modal-body {
            background:
                radial-gradient(circle at top right, rgba(34, 197, 94, 0.08), transparent 30%),
                radial-gradient(circle at bottom left, rgba(14, 165, 233, 0.08), transparent 34%),
                #ffffff;
        }

        .modal-footer-custom {
            padding: 1.5rem 2rem;
            border-top: 1px solid #f1f5f9;
        }

        .stock-modal-footer {
            background: linear-gradient(180deg, #f8fafc 0%, #eefaf6 100%);
            border-top: 1px solid rgba(34, 197, 94, 0.12);
        }

        .stock-modal-table {
            background: #fff;
            border: 1px solid #dbeafe;
            border-radius: 14px;
            overflow: hidden;
        }

        .stock-modal-table thead {
            background: linear-gradient(135deg, #0f172a 0%, #0f766e 100%);
        }

        .stock-modal-table thead th {
            background: linear-gradient(135deg, #0f172a 0%, #0f766e 100%) !important;
            color: #fff !important;
            border-color: rgba(255, 255, 255, 0.12);
            padding: 1rem 1rem;
            vertical-align: middle;
            white-space: nowrap;
        }

        .stock-modal-table tbody tr {
            transition: background-color 0.2s ease, transform 0.2s ease;
        }

        .stock-modal-table tbody tr:hover {
            background: #f8fbff;
        }

        .stock-modal-table tbody td,
        .stock-modal-table tfoot th {
            border-color: #e2e8f0;
        }

        .stock-size-pill {
            background: linear-gradient(135deg, #e0f2fe 0%, #d1fae5 100%);
            color: #0f172a;
            font-weight: 800;
            min-width: 2rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.7);
        }

        .stock-modal-table tfoot {
            background: linear-gradient(135deg, #eff6ff 0%, #ecfdf5 100%);
        }

        .form-label-custom {
            font-size: 0.75rem;
            font-weight: 800;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.65rem;
        }

        .form-control-custom {
            border-radius: 10px;
            border: 1px solid #eef2f6;
            background: #f8fafc;
            padding: 0.75rem 1rem;
            font-weight: 600;
            color: #1e293b;
            font-size: 0.95rem;
        }

        .form-control-custom:focus {
            border-color: #00a3e0;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(0, 163, 224, 0.05);
        }

        .stock-modal-dialog {
            max-width: min(1120px, 96vw);
        }
    </style>
    @endpush

    <!-- Stats Grid -->
    <div class="stats-grid">
        @foreach($stats as $stat)
        <div class="sample-card">
            <div class="card-accent" style="background-color: {{ $stat['color'] }};"></div>
            <div class="card-label">{{ $stat['label'] }}</div>
            <div class="card-value">{{ $stat['value'] }}</div>
            <div class="card-sub">{{ $stat['sub'] }}</div>
            <div class="card-icon-box" style="color: {{ $stat['color'] }};">
                <i class="bi {{ $stat['icon'] }}"></i>
            </div>
        </div>
        @endforeach
    </div>

    <div class="section-card">
        <div class="section-header">
            <div>
                <h2 class="section-title">Material Inventory</h2>
                <p class="section-subtitle">Core production raw materials and current stock levels</p>
            </div>

            <div class="d-flex gap-3 align-items-center">
                <div class="search-container">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" class="search-input" placeholder="Search by name or code..." wire:model.live="search">
                </div>
                <button class="btn-custom" wire:click="openModal">
                    <i class="bi bi-plus-lg"></i> Add Material
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Material Code</th>
                        <th>Material Name</th>
                        <th class="text-center">Current Stock</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($materials as $material)
                    @php
                    $stock = $material->total_stock;
                    @endphp
                    <tr>
                        <td><span class="material-code">{{ $material->code }}</span></td>
                        <td>{{ $material->name }}</td>
                        <td class="text-center"><span class="stock-value fw-bold" style="color: #1e293b">{{ number_format($stock) }}</span></td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-light" wire:click="openStockModal({{ $material->id }})" title="View Stock">
                                <i class="bi bi-eye text-primary"></i>
                            </button>
                            <button class="btn btn-sm btn-light" wire:click="editMaterial({{ $material->id }})">
                                <i class="bi bi-pencil-fill text-muted"></i>
                            </button>
                            <button class="btn btn-sm btn-light"
                                wire:click="openDeleteModal({{ $material->id }})"
                                title="Delete Material">
                                <i class="bi bi-trash text-danger"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center   py-3">No materials found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $materials->links() }}
        </div>
    </div>

    <!-- Modal Implementation -->
    @if($showModal)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5)">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-content-custom">
                <div class="modal-header modal-header-custom">
                    <h5 class="modal-title fw-bold">{{ $material_id ? 'Edit Material' : 'Add New Material' }}</h5>
                    <button type="button" class="btn-close" wire:click="$set('showModal', false)"></button>
                </div>
                <div class="modal-body modal-body-custom">
                    <div class="row g-3">
                        <div class="col-md-6 text-start">
                            <label class="form-label-custom">Material Code</label>
                            <input type="text" class="form-control form-control-custom" wire:model="code">
                        </div>
                        <div class="col-md-6 text-start">
                            <label class="form-label-custom">Type</label>
                            <select class="form-select form-control-custom" wire:model="material_type">
                                <option value="Raw Material">Raw Material</option>
                                <option value="Packaging">Packaging</option>
                                <option value="Accessories">Accessories</option>
                            </select>
                        </div>
                        <div class="col-12 text-start">
                            <label class="form-label-custom">Material Name</label>
                            <input type="text" class="form-control form-control-custom" wire:model="name" placeholder="e.g. Premium Cotton">
                            @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-12 text-start">
                            <label class="form-label-custom">Description</label>
                            <textarea class="form-control form-control-custom" wire:model="description" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer modal-footer-custom">
                    <button type="button" class="btn btn-light rounded-pill  px-3" wire:click="$set('showModal', false)">Cancel</button>
                    <button type="button" class="btn-custom" wire:click="saveMaterial">Save Material</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($showStockModal)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5)">
        <div class="modal-dialog modal-dialog-centered stock-modal-dialog">
            <div class="modal-content modal-content-custom stock-modal-content">
                <div class="modal-header modal-header-custom stock-modal-header">
                    <div>
                        <h5 class="modal-title stock-modal-title fw-bold mb-1">Material Batch Stock</h5>
                        <p class="stock-modal-subtitle small mb-0">{{ $view_material_name }} ({{ $view_material_code }})</p>
                    </div>
                    <button type="button" class="btn-close" wire:click="closeStockModal"></button>
                </div>
                <div class="modal-body modal-body-custom stock-modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0 stock-modal-table">
                            <thead>
                                <tr class="text-uppercase small fw-bold">
                                    <th>Material Batch</th>
                                    <th>Size</th>
                                    <th class="text-end">Received Quantity (Ton)</th>
                                    <th class="text-end">Remaining Stock (Ton)</th>
                                    <th class="text-end">Price Per Ton</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($batchStocks as $row)
                                <tr>
                                    <td><span>{{ $row['batch_no'] }}</span></td>
                                    <td>
                                        <span class="badge rounded-pill stock-size-pill">{{ $row['size'] }}</span>
                                    </td>
                                    <td class="text-end">{{ number_format($row['received_qty'], 2) }}</td>
                                    <td class="text-end text-success fw-semibold">{{ number_format($row['remaining_qty'], 2) }}</td>
                                    <td class="text-end">{{ number_format($row['cost_price'], 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center  py-3  text-muted">No batch records available for this material.</td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3">Total Remaining Stock</th>
                                    <th class="text-end">{{ number_format($batchTotalStock, 2) }}</th>
                                    <th class="text-end">-</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="modal-footer modal-footer-custom stock-modal-footer">
                    <button type="button" class="btn btn-light rounded-pill  px-3" wire:click="closeStockModal">Close</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($showDeleteModal)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5)">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-content-custom">
                <div class="modal-header modal-header-custom">
                    <h5 class="modal-title fw-bold mb-0">Confirm Material Delete</h5>
                    <button type="button" class="btn-close" wire:click="closeDeleteModal"></button>
                </div>
                <div class="modal-body modal-body-custom">
                    <p class="mb-2">Are you sure you want to delete <b>{{ $delete_material_name }}</b>?</p>

                    @if($deleteBlocked)
                    <div class="alert alert-danger mb-0" role="alert">
                        {{ $deleteBlockMessage }}
                    </div>
                    @else
                    <div class="alert alert-warning mb-0" role="alert">
                        This action cannot be undone.
                    </div>
                    @endif
                </div>
                <div class="modal-footer modal-footer-custom">
                    <button type="button" class="btn btn-light rounded-pill  px-3" wire:click="closeDeleteModal">Cancel</button>
                    <button type="button" class="btn btn-danger rounded-pill  px-3" wire:click="deleteMaterial" @disabled($deleteBlocked)>
                        Delete Material
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>