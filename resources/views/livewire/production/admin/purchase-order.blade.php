<div class="dashboard-wrapper">
    @push('styles')
    <style>
        .dashboard-wrapper {
            background: linear-gradient(135deg, #f5f7fb 0%, #f0f4fa 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }

        .section-card {
            background: #ffffff;
            border-radius: 14px;
            padding: 2rem;
            border: 1px solid rgba(30, 41, 59, 0.08);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
            margin-bottom: 2rem;
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
            margin-bottom: 1.5rem;
        }

        .btn-custom-primary {
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

        .btn-custom-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(2, 132, 199, 0.35);
            color: white;
        }

        .form-label {
            font-weight: 700;
            color: #475569;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
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

        .table-custom tr th {
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

        .table-custom tr td {
            border: none;
            padding: 1.25rem 1rem;
            vertical-align: middle;
            font-weight: 500;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }

        .table-custom tbody tr:hover {
            background: rgba(2, 132, 199, 0.03);
            transition: all 0.2s ease;
        }

        /* Modal Aesthetics */
        .modal-content-custom {
            border-radius: 16px;
            border: 1px solid rgba(30, 41, 59, 0.08);
            box-shadow: 0 25px 60px -12px rgba(0, 0, 0, 0.15);
        }

        .modal-header-custom {
            padding: 1.75rem 2rem;
            border-bottom: 1px solid #e2e8f0;
            background: linear-gradient(135deg, #f8fafc 0%, #f0f4fa 100%);
        }

        .search-results {
            position: absolute;
            z-index: 100000;
            background: #ffffff;
            width: 100%;
            min-width: 300px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.12);
            top: 100%;
            left: 0;
            margin-top: 8px;
            overflow: hidden;
            max-height: 240px;
            overflow-y: auto;
        }

        .result-item {
            padding: 0.95rem 1.25rem;
            cursor: pointer;
            border-bottom: 1px solid #f1f5f9;
            transition: all 0.2s ease;
            display: flex;
            flex-direction: column;
            text-align: left;
        }

        .result-item:hover {
            background: linear-gradient(90deg, #eff6ff 0%, #f0f4fa 100%);
        }

        .result-item b {
            color: #0f172a;
            font-size: 0.95rem;
            font-weight: 700;
        }

        .result-item span {
            color: #0284c7;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            margin-top: 0.25rem;
        }

        .badge-status {
            padding: 0.55rem 1.1rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .badge-pending {
            background: linear-gradient(135deg, #fef3c7 0%, #fcd34d 100%);
            color: #92400e;
        }

        .badge-complete {
            background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
            color: #166534;
        }

        .badge-received {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #1e40af;
        }

        .badge-partial {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #7f1d1d;
        }

        .dropdown-menu {
            border: 1px solid #e2e8f0;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.12);
            border-radius: 12px;
            padding: 0.6rem;
        }

        .dropdown-item {
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            padding: 0.7rem 1.1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #475569;
            transition: all 0.2s ease;
        }

        .dropdown-item:hover {
            background: linear-gradient(90deg, #eff6ff 0%, #f0f4fa 100%);
            color: #0284c7;
        }

        .dropdown-item i {
            font-size: 1rem;
        }

        [x-cloak] {
            display: none !important;
        }

        .view-modal-actions .dropdown-menu {
            z-index: 1065;
        }
    </style>
    @endpush

    <div class="section-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="section-title">Production Material Purchases</h2>
                <p class="section-subtitle mb-0">Track and manage all purchase orders issued to suppliers</p>
            </div>
            <button class="btn-custom-primary" wire:click="openModal">
                <i class="bi bi-plus-lg"></i> Add Purchase Order
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th>Order Code</th>
                        <th>Supplier</th>
                        <th>Order Date</th>
                        <th class="text-center">Items</th>
                        <th class="text-center">Batches</th>
                        <th class="text-end">Total Amount</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                @forelse($purchaseOrders as $po)
                <tbody wire:key="po-{{ $po->id }}">
                    <tr>
                        <td><span style="color: #1e293b; font-weight: 800">#{{ $po->order_code }}</span></td>
                        <td>{{ $po->supplier->name }}</td>
                        <td>{{ date('M d, Y', strtotime($po->order_date)) }}</td>
                        <td class="text-center">{{ count($po->items) }}</td>
                        <td class="text-center">
                            <span class="badge rounded-pill" style="background: #eff6ff; color: #3b82f6; font-weight: 800; padding: 0.4rem 0.85rem;">{{ count($po->batches) }}</span>
                        </td>
                        <td class="text-end fw-bold" style="color: #1e293b">${{ number_format($po->total_amount, 2) }}</td>
                        <td class="text-center">
                            @php
                            $badgeClass = match($po->status) {
                            'pending' => 'badge-pending',
                            'complete' => 'badge-complete',
                            'received' => 'badge-received',
                            'partial' => 'badge-partial',
                            default => 'badge-secondary'
                            };
                            @endphp
                            <span class="badge-status {{ $badgeClass }}">
                                {{ strtoupper($po->status) }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">
                                {{-- Alpine.js dropdown — survives Livewire re-renders --}}
                                <div x-data="{ open: false, openUp: false, top: 0, left: 0 }" @click.outside="open = false" class="position-relative">
                                    <button x-ref="trigger" class="btn btn-sm btn-light" type="button" @click="
                                        const r = $refs.trigger.getBoundingClientRect();
                                        openUp = (window.innerHeight - r.bottom) < 220;
                                        top = openUp ? (r.top - 6) : (r.bottom + 6);
                                        left = r.right;
                                        open = !open;
                                    ">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul
                                        x-show="open"
                                        x-cloak
                                        x-transition
                                        class="dropdown-menu show position-fixed"
                                        :style="`z-index: 2000; top: ${top}px; left: ${left}px; min-width: 12rem; transform: translateX(-100%) ${openUp ? 'translateY(-100%)' : ''};`">
                                        <li>
                                            <button class="dropdown-item" @click="open = false" wire:click="viewOrder({{ $po->id }})">
                                                <i class="bi bi-eye text-primary"></i> View
                                            </button>
                                        </li>

                                        @if($po->status !== 'complete')
                                        @if($po->status == 'pending' || $po->status == 'partial')
                                        <li>
                                            <button class="dropdown-item" @click="open = false" wire:click="openGRNModal({{ $po->id }})">
                                                <i class="bi bi-box-seam text-primary"></i> Process GRN
                                            </button>
                                        </li>
                                        @endif

                                        <li>
                                            <button class="dropdown-item" @click="open = false" wire:click="editOrder({{ $po->id }})">
                                                <i class="bi bi-pencil text-info"></i> Edit Order
                                            </button>
                                        </li>
                                        @if($po->status == 'pending')
                                        <li>
                                            <button class="dropdown-item" @click="open = false" wire:click="completeOrder({{ $po->id }})">
                                                <i class="bi bi-check-circle text-success"></i> Mark Complete
                                            </button>
                                        </li>
                                        @endif
                                        @endif

                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <button class="dropdown-item text-danger"
                                                @click="open = false"
                                                onclick="confirm('Are you sure?') || event.stopImmediatePropagation()"
                                                wire:click="deleteOrder({{ $po->id }})">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </li>
                                    </ul>
                                </div>

                            </div>
                        </td>
                    </tr>
                </tbody>
                @empty
                <tbody>
                    <tr>
                        <td colspan="8" class="text-center py-5">No purchase orders found.</td>
                    </tr>
                </tbody>
                @endforelse
            </table>
        </div>
        <div class="mt-4">
            {{ $purchaseOrders->links() }}
        </div>
    </div>

    {{-- ============================================================
         View Purchase Order Modal
    ============================================================ --}}
    @if($showViewModal && $selectedViewPO)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content modal-content-custom" style="overflow: visible;">
                <div class="modal-header modal-header-custom d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="modal-title fw-bold mb-1">Purchase Order Details</h5>
                        <p class="text-muted small mb-0">#{{ $selectedViewPO->order_code }}</p>
                    </div>
                    <div class="d-flex align-items-center gap-2 view-modal-actions">
                        <div x-data="{ open: false }" @click.outside="open = false" class="position-relative">
                            <button class="btn btn-sm btn-light" type="button" @click="open = !open" aria-label="View order actions">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <div
                                x-show="open"
                                x-cloak
                                x-transition.origin.top.right
                                class="dropdown-menu dropdown-menu-end show position-absolute"
                                style="z-index: 1065; right: 0; top: 100%; margin-top: 0.5rem; min-width: 12rem;">
                                @if($selectedViewPO->status !== 'complete')
                                @if($selectedViewPO->status == 'pending' || $selectedViewPO->status == 'partial')
                                <button type="button" class="dropdown-item" @click="open = false" wire:click="openGRNModal({{ $selectedViewPO->id }})">
                                    <i class="bi bi-box-seam text-primary"></i> Process GRN
                                </button>
                                @endif
                                <button type="button" class="dropdown-item" @click="open = false" wire:click="editOrder({{ $selectedViewPO->id }})">
                                    <i class="bi bi-pencil text-info"></i> Edit Order
                                </button>
                                @if($selectedViewPO->status == 'pending')
                                <button type="button" class="dropdown-item" @click="open = false" wire:click="completeOrder({{ $selectedViewPO->id }})">
                                    <i class="bi bi-check-circle text-success"></i> Mark Complete
                                </button>
                                @endif

                                <div class="dropdown-divider"></div>
                                @endif

                                <button type="button" class="dropdown-item text-danger"
                                    @click="open = false"
                                    onclick="confirm('Are you sure?') || event.stopImmediatePropagation()"
                                    wire:click="deleteOrder({{ $selectedViewPO->id }})">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                        <button type="button" class="btn-close" wire:click="closeViewModal"></button>
                    </div>
                </div>

                <div class="modal-body p-4 p-md-5">
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <div class="p-3 rounded-3 border bg-light h-100">
                                <div class="text-muted small text-uppercase fw-bold mb-1">Supplier</div>
                                <div class="fw-bold text-dark">{{ $selectedViewPO->supplier->name ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 rounded-3 border bg-light h-100">
                                <div class="text-muted small text-uppercase fw-bold mb-1">Order Date</div>
                                <div class="fw-bold text-dark">{{ date('M d, Y', strtotime($selectedViewPO->order_date)) }}</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 rounded-3 border bg-light h-100">
                                <div class="text-muted small text-uppercase fw-bold mb-1">Status</div>
                                @php
                                $viewBadgeClass = match($selectedViewPO->status) {
                                'pending' => 'badge-pending',
                                'complete' => 'badge-complete',
                                'received' => 'badge-received',
                                'partial' => 'badge-partial',
                                default => 'badge-secondary'
                                };
                                @endphp
                                <span class="badge-status {{ $viewBadgeClass }}">{{ strtoupper($selectedViewPO->status) }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 rounded-3 border bg-light h-100">
                                <div class="text-muted small text-uppercase fw-bold mb-1">Total Amount</div>
                                <div class="fw-bold text-dark fs-5">${{ number_format($selectedViewPO->total_amount, 2) }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="fw-bold mb-0">Ordered Items</h6>
                            <span class="text-muted small text-uppercase fw-bold">{{ count($selectedViewPO->items) }} items</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0">
                                <thead class="bg-light">
                                    <tr class="text-uppercase small fw-bold">
                                        <th>Material</th>
                                        <th>Size</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-end">Unit Price</th>
                                        <th class="text-end">Last Purchase Price</th>
                                        <th class="text-end">Line Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($selectedViewPO->items as $item)
                                    <tr>
                                        <td class="fw-bold text-dark">{{ $item->material->name ?? '-' }}</td>
                                        <td>{{ $item->size ?? '-' }}</td>
                                        <td class="text-center fw-bold">{{ number_format($item->quantity, 2) }}</td>
                                        <td class="text-end fw-bold">${{ number_format($item->unit_price, 2) }}</td>
                                        <td class="text-end fw-bold">
                                            @php
                                            $lastPrice = $this->getLastPurchasePrice($item->production_material_id);
                                            @endphp
                                            @if($lastPrice)
                                            @if($lastPrice != $item->unit_price)
                                            <span class="text-warning">${{ number_format($lastPrice, 2) }}</span>
                                            @else
                                            <span>${{ number_format($lastPrice, 2) }}</span>
                                            @endif
                                            @else
                                            <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-end fw-bold">${{ number_format(($item->quantity * $item->unit_price), 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="fw-bold mb-0">Batch Records</h6>
                            <span class="text-muted small text-uppercase fw-bold">{{ count($selectedViewPO->batches) }} batches</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0">
                                <thead class="bg-light">
                                    <tr class="text-uppercase small fw-bold">
                                        <th>Batch No</th>
                                        <th>Material</th>
                                        <th>Size</th>
                                        <th class="text-center">Qty Purchased</th>
                                        <th class="text-center">Remaining</th>
                                        <th class="text-end">Cost Price</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($selectedViewPO->batches as $batch)
                                    <tr>
                                        <td class="fw-bold text-dark">{{ $batch->batch_no }}</td>
                                        <td>{{ $batch->material->name ?? '-' }}</td>
                                        <td>{{ $batch->size ?? '-' }}</td>
                                        <td class="text-center fw-bold">{{ number_format($batch->quantity, 2) }}</td>
                                        <td class="text-center fw-bold">
                                            <span class="{{ $batch->remaining_quantity > 0 ? 'text-success' : 'text-danger' }}">
                                                {{ number_format($batch->remaining_quantity, 2) }}
                                            </span>
                                        </td>
                                        <td class="text-end fw-bold">${{ number_format($batch->cost_price, 2) }}</td>
                                        <td class="text-center">
                                            @if($batch->remaining_quantity >= $batch->quantity)
                                            <span class="badge rounded-pill" style="background: #ecfdf5; color: #10b981; font-weight: 800; padding: 0.35rem 0.75rem; font-size: 0.7rem;">FULL</span>
                                            @elseif($batch->remaining_quantity > 0)
                                            <span class="badge rounded-pill" style="background: #fffbeb; color: #f59e0b; font-weight: 800; padding: 0.35rem 0.75rem; font-size: 0.7rem;">PARTIAL</span>
                                            @else
                                            <span class="badge rounded-pill" style="background: #fef2f2; color: #f43f5e; font-weight: 800; padding: 0.35rem 0.75rem; font-size: 0.7rem;">DEPLETED</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">No batch records found.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="modal-footer p-4 border-top">
                    <button type="button" class="btn btn-light fw-bold px-4 rounded-pill" wire:click="closeViewModal">Close</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ============================================================
         Create / Edit Purchase Order Modal
    ============================================================ --}}
    @if($showModal)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5)">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content modal-content-custom">
                <div class="modal-header modal-header-custom">
                    <h5 class="modal-title fw-bold">
                        {{ $po_id ? 'Edit Production Purchase Order' : 'Create New Production Purchase Order' }}
                    </h5>
                    <button type="button" class="btn-close" wire:click="$set('showModal', false)"></button>
                </div>
                <div class="modal-body p-5">
                    <div class="row g-4 mb-5">
                        <div class="col-md-6 text-start">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <label class="form-label d-block mb-0">Select Supplier</label>
                                <button type="button" class="btn btn-sm btn-outline-primary fw-bold" wire:click="openSupplierCreateModal">
                                    <i class="bi bi-plus-lg me-1"></i>Supplier
                                </button>
                            </div>
                            <select class="form-select" wire:model="supplier_id">
                                <option value="">Choose a supplier...</option>
                                @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                            @error('supplier_id') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6 text-start">
                            <label class="form-label d-block mb-2">Order Date</label>
                            <input type="date" class="form-control" wire:model="order_date">
                            @error('order_date') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div style="overflow: visible !important;">
                        <table class="table" style="overflow: visible !important;">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0 px-4 py-3" style="font-size: 0.75rem; text-transform: uppercase;">Material Item</th>
                                    <th class="border-0 px-4 py-3" style="font-size: 0.75rem; text-transform: uppercase; width: 15%">Size</th>
                                    <th class="border-0 px-4 py-3" style="font-size: 0.75rem; text-transform: uppercase; width: 15%">Quantity (Ton)</th>
                                    <th class="border-0 px-4 py-3" style="font-size: 0.75rem; text-transform: uppercase; width: 15%">Price/Ton ($)</th>
                                    <th class="border-0 px-4 py-3 text-end" style="font-size: 0.75rem; text-transform: uppercase; width: 15%">Sub-Total</th>
                                    <th class="border-0 text-center" style="width: 50px"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $index => $item)
                                <tr class="align-middle">
                                    <td class="px-0 py-3 position-relative" style="overflow: visible !important;">
                                        <div class="position-relative">
                                            <input type="text" class="form-control mb-0" placeholder="Search material..."
                                                wire:model.live="items.{{ $index }}.name"
                                                wire:keyup="performSearchMaterial({{ $index }}, $event.target.value)"
                                                style="height: 48px;">

                                            @if($activeSearchIndex === $index && count($materialResults) > 0)
                                            <div class="search-results">
                                                @foreach($materialResults as $result)
                                                <div class="result-item" wire:click="selectMaterial({{ $index }}, {{ $result['id'] }})">
                                                    <b>{{ $result['name'] }}</b>
                                                    <span>{{ $result['code'] }}</span>
                                                </div>
                                                @endforeach
                                            </div>
                                            @endif
                                        </div>
                                        @error("items.$index.material_id")
                                        <div class="text-danger mt-1" style="font-size: 0.65rem; font-weight: 800; text-transform: uppercase;">Required</div>
                                        @enderror
                                    </td>
                                    <td class="px-3 py-3">
                                        <select class="form-select" wire:model="items.{{ $index }}.size" style="height: 48px;">
                                            <option value="S">S</option>
                                            <option value="M">M</option>
                                            <option value="L">L</option>
                                        </select>
                                    </td>
                                    <td class="px-3 py-3">
                                        <div class="input-group" style="height: 48px;">
                                            <input type="number" class="form-control" wire:model.blur="items.{{ $index }}.quantity" step="0.01">
                                            <span class="input-group-text bg-white border-start-0 text-muted small px-2">Ton</span>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3">
                                        <div class="input-group" style="height: 48px;">
                                            <input type="number" class="form-control" wire:model.blur="items.{{ $index }}.unit_price" step="0.01">
                                        </div>
                                    </td>
                                    <td class="px-3 py-3 text-end fw-bold" style="color: #1e293b; font-size: 1rem;">
                                        {{ number_format($item['total'] ?? 0, 2) }}
                                    </td>
                                    <td class="py-3 text-center">
                                        <button class="btn btn-link text-danger p-0" wire:click="removeItem({{ $index }})" style="font-size: 1.2rem;">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <button class="btn btn-outline-primary fw-bold" wire:click="addItem">
                            <i class="bi bi-plus-circle"></i> Add Material
                        </button>
                        <div class="text-end">
                            <span class="text-uppercase text-muted fw-bold small">Total Amount Due</span>
                            <h3 class="fw-bold mb-0" style="color: #1e293b">{{ number_format(collect($items)->sum('total'), 2) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-4 border-top">
                    <button type="button" class="btn btn-light fw-bold px-4 rounded-pill" wire:click="$set('showModal', false)">Discard</button>
                    <button type="button" class="btn-custom-primary px-5 rounded-pill" wire:click="save">
                        {{ $po_id ? 'Update Order' : 'Publish Order' }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if($showSupplierCreateModal)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.55); z-index: 2000;">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="border-radius: 14px;">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add Supplier</h5>
                    <button type="button" class="btn-close" wire:click="closeSupplierCreateModal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6 text-start">
                            <label class="form-label fw-bold">Supplier Name</label>
                            <input type="text" class="form-control" wire:model="new_supplier_name" placeholder="Enter supplier name">
                            @error('new_supplier_name') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6 text-start">
                            <label class="form-label fw-bold">Business Name</label>
                            <input type="text" class="form-control" wire:model="new_supplier_businessname" placeholder="Enter business name">
                            @error('new_supplier_businessname') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-6 text-start">
                            <label class="form-label fw-bold">Phone</label>
                            <input type="text" class="form-control" wire:model="new_supplier_phone" placeholder="Enter phone">
                            @error('new_supplier_phone') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6 text-start">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" class="form-control" wire:model="new_supplier_email" placeholder="Enter email">
                            @error('new_supplier_email') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6 text-start">
                            <label class="form-label fw-bold">Status</label>
                            <select class="form-select" wire:model="new_supplier_status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                            @error('new_supplier_status') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-12 text-start">
                            <label class="form-label fw-bold">Address</label>
                            <input type="text" class="form-control" wire:model="new_supplier_address" placeholder="Enter address">
                            @error('new_supplier_address') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-12 text-start">
                            <label class="form-label fw-bold">Notes</label>
                            <textarea class="form-control" rows="2" wire:model="new_supplier_notes" placeholder="Optional notes"></textarea>
                            @error('new_supplier_notes') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" wire:click="closeSupplierCreateModal">Cancel</button>
                    <button type="button" class="btn btn-primary" wire:click="saveSupplierFromModal">Save Supplier</button>
                </div>
            </div>
        </div>
    </div>
    @endif
    @endif

    {{-- ============================================================
         GRN Modal
    ============================================================ --}}
    @if($showGRNModal)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5)">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content modal-content-custom">
                <div class="modal-header modal-header-custom bg-light">
                    <h5 class="modal-title fw-bold">Process Goods Receive Note (GRN)</h5>
                    <button type="button" class="btn-close" wire:click="$set('showGRNModal', false)"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <p class="text-muted small text-uppercase fw-bold mb-1">Purchase Order</p>
                            <h5 class="fw-bold">#{{ $selectedPO->order_code }}</h5>
                        </div>
                        <div class="col-md-4">
                            <p class="text-muted small text-uppercase fw-bold mb-1">Supplier</p>
                            <h5 class="fw-bold">{{ $selectedPO->supplier->name }}</h5>
                        </div>
                        <div class="col-md-4">
                            <p class="text-muted small text-uppercase fw-bold mb-1">Batch Number</p>
                            <input type="text" class="form-control" wire:model.defer="batch_no">
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="bg-light">
                                <tr class="text-uppercase small fw-bold">
                                    <th>Material</th>
                                    <th class="text-center">Size</th>
                                    <th class="text-center">Ordered</th>

                                    <th class="text-center" style="width: 150px">Receive Now</th>
                                    <th class="text-end" style="width: 170px">Cost Price</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($grnItems as $index => $item)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $item['name'] }}</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark fw-bold border">{{ $item['size'] }}</span>
                                    </td>
                                    <td class="text-center fw-bold">{{ number_format($item['ordered_qty'], 2) }}</td>

                                    <td class="text-center">
                                        @if($item['received'])
                                        <input type="number" class="form-control text-center fw-bold"
                                            wire:model.live.debounce-1000ms="grnItems.{{ $index }}.received_qty" step="0.01"
                                            min="0">
                                        @else
                                        <span class="text-muted fst-italic">Skipped</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white border-end-0 text-muted px-2">Rs.</span>
                                            <input type="number" class="form-control border-start-0 text-end fw-bold"
                                                wire:model.live.debounce-1000ms="grnItems.{{ $index }}.cost_price" step="0.01">
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check form-switch d-flex justify-content-center">
                                            <input class="form-check-input" type="checkbox"
                                                wire:model.live="grnItems.{{ $index }}.received">
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer p-4 border-top">
                    <button type="button" class="btn btn-light fw-bold px-4 rounded-pill" wire:click="$set('showGRNModal', false)">Cancel</button>
                    <button type="button" class="btn-custom-primary px-5 rounded-pill" wire:click="processGRN">
                        <i class="bi bi-check2-circle me-2"></i> Confirm Stock Arrival
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>