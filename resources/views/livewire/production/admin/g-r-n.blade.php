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
            padding: 2.25rem;
            border: 1px solid #eef2f6;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            margin-bottom: 2rem;
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
            margin-bottom: 2rem;
        }

        .btn-custom-primary {
            background: #00a3e0;
            color: white;
            border: none;
            padding: 0.75rem 1.75rem;
            border-radius: 10px;
            font-weight: 800;
            transition: all 0.2s;
        }

        .btn-custom-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 163, 224, 0.3);
        }

        .form-label {
            font-weight: 700;
            color: #64748b;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .form-control,
        .form-select {
            border-radius: 10px;
            border: 1px solid #eef2f6;
            background: #f8fafc;
            padding: 0.75rem 1rem;
            font-weight: 600;
        }

        .table-custom tr th {
            border: none;
            font-size: 0.75rem;
            font-weight: 800;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 1rem;
        }

        .table-custom tr td {
            border: none;
            padding: 1rem;
            vertical-align: middle;
        }

        .badge-status {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 800;
            letter-spacing: 0.05em;
        }

        .badge-status-pending {
            background: rgba(0, 163, 224, 0.1);
            color: #00a3e0;
        }

        .po-item:hover {
            background: #f8fafc;
            cursor: pointer;
            transition: all 0.2s;
            border-radius: 8px;
        }
    </style>
    @endpush

    <div class="row">
        <!-- Main Area: Selected PO Processing -->
        <div class="col-lg-8">
            <div class="section-card">
                <h2 class="section-title">Goods Receive Note (GRN)</h2>
                <p class="section-subtitle">Confirm receipt of production materials</p>

                @if($selectedPO)
                <div class="mb-4 d-flex justify-content-between align-items-end border-bottom pb-4 mb-4">
                    <div>
                        <span class="text-uppercase fw-bold text-muted small d-block">Order Code</span>
                        <h4 class="fw-bold mb-0">{{ $selectedPO->order_code }}</h4>
                        <span class="text-secondary fw-semibold">{{ $selectedPO->supplier->name }}</span>
                    </div>
                    <div>
                        <div class="mb-3 text-end">
                            <label class="form-label d-block text-end text-muted">Batch Numbers will be auto-generated per variant</label>
                            <span class="text-muted small">Format: BT{Date}{Variant}-{Number}</span>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th style="width: 30%">Material Item</th>
                                <th style="width: 10%">Size</th>
                                <th style="width: 15%">Ordered</th>
                                <th style="width: 15%">Received</th>
                                <th style="width: 20%">Unit Cost Price</th>
                                <th style="width: 10%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($grnItems as $index => $item)
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $item['name'] }}</div>
                                    <small class="text-muted fw-bold">ID: #{{ $item['material_id'] }}</small>
                                </td>
                                <td><span class="badge rounded-pill" style="background: #f0f4f8; color: #334155; font-weight: 800;">{{ $item['size'] }}</span></td>
                                <td class="fw-bold">{{ number_format($item['ordered_qty'], 2) }}</td>
                                <td>
                                    <input type="number" class="form-control" wire:model.live="grnItems.{{ $index }}.received_qty" step="0.01" placeholder="0.00">
                                </td>
                                <td>
                                    <input type="number" class="form-control" wire:model.live="grnItems.{{ $index }}.cost_price" step="0.01" placeholder="0.00">
                                </td>
                                <td>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" wire:model="grnItems.{{ $index }}.received" id="check_{{ $index }}">
                                        <label class="form-check-label small text-muted" for="check_{{ $index }}">Receive</label>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="text-end mt-4 pt-4 border-top">
                    <button class="btn btn-light me-2 fw-bold" wire:click="$set('selectedPO', null)">Cancel</button>
                    <button class="btn-custom-primary" wire:click="processGRN">
                        Receive & Create Stock Batches
                    </button>
                </div>
                @else
                <div class="text-center py-5">
                    <img src="" alt="" style="width: 120px; opacity: 0.1">
                    <h5 class="mt-4 text-muted fw-bold">Select a Purchase Order from the right to start receiving goods.</h5>
                </div>
                @endif
            </div>
        </div>

        <!-- Sidebar: Outstanding POs -->
        <div class="col-lg-4">
            <div class="section-card">
                <h2 class="section-title">Pending Orders</h2>
                <p class="section-subtitle">Awaiting material receipt</p>

                <div class="mb-4">
                    <input type="text" class="form-control" placeholder="Search PO numbers..." wire:model.live="searchPO">
                </div>

                <div class="list-group list-group-flush">
                    @forelse($pendingPOs as $po)
                    <div class="list-group-item px-0 py-3 po-item" wire:click="selectPO({{ $po->id }})">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="mb-0 fw-bold">{{ $po->order_code }}</h6>
                                <small class="text-secondary fw-semibold">{{ $po->supplier->name }}</small>
                            </div>
                            <span class="badge-status badge-status-pending">
                                {{ strtoupper($po->status) }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between text-muted mt-1" style="font-size: 0.8rem">
                            <span>{{ count($po->items) }} Items</span>
                            <span class="fw-bold text-dark">{{ date('M d, Y', strtotime($po->order_date)) }}</span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <small class="text-muted fw-bold">No pending orders found.</small>
                    </div>
                    @endforelse
                </div>

                <div class="mt-4">
                    {{ $pendingPOs->links() }}
                </div>
            </div>
        </div>
    </div>
</div>