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

        .status-badge {
            border-radius: 20px;
            padding: 0.55rem 1.1rem;
            font-size: 0.72rem;
            font-weight: 800;
            text-transform: uppercase;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        }

        .status-active {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #1e40af;
        }

        .status-completed {
            background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
            color: #166534;
        }

        .batch-modal-content {
            border-radius: 16px;
            border: 1px solid rgba(30, 41, 59, 0.08);
            box-shadow: 0 25px 60px -12px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }

        .batch-modal-dialog {
            max-width: min(1180px, 96vw);
        }

        .batch-modal-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            background: linear-gradient(135deg, #f8fafc 0%, #eff6ff 55%, #f0fdf4 100%);
            position: relative;
        }

        .batch-modal-header .modal-title {
            font-size: 1.2rem;
            line-height: 1.3;
        }

        .batch-modal-header p {
            font-size: 0.82rem;
        }

        .batch-modal-header::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 5px;
            height: 100%;
            background: linear-gradient(180deg, #0284c7 0%, #22c55e 100%);
        }

        .batch-modal-body {
            padding: 1rem 1.5rem 1.25rem;
            background:
                radial-gradient(circle at top right, rgba(14, 165, 233, 0.06), transparent 26%),
                radial-gradient(circle at bottom left, rgba(34, 197, 94, 0.06), transparent 30%),
                #ffffff;
        }

        .batch-modal-footer {
            border-top: 1px solid #e2e8f0;
            padding: 0.85rem 1.5rem 1rem;
            background: linear-gradient(180deg, #f8fafc 0%, #f8fbff 100%);
        }

        .batch-modal-shell {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .batch-tab-toggle {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .batch-tab-buttons {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.75rem;
            padding: 0.5rem;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
        }

        .batch-tab-button {
            margin: 0;
            padding: 0.7rem 0.9rem;
            border-radius: 11px;
            background: transparent;
            color: #64748b;
            font-size: 0.8rem;
            font-weight: 800;
            letter-spacing: 0.02em;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            user-select: none;
        }

        .batch-tab-button:hover {
            color: #0f172a;
            background: rgba(255, 255, 255, 0.7);
        }

        .batch-step-actions {
            display: flex;
            justify-content: space-between;
            gap: 0.75rem;
            margin-top: 1rem;
            flex-wrap: wrap;
        }

        .batch-step-actions .btn {
            min-width: 130px;
        }

        #batch-tab-planning:checked~.batch-tab-buttons .batch-tab-button-planning,
        #batch-tab-workers:checked~.batch-tab-buttons .batch-tab-button-workers {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            color: #ffffff;
            box-shadow: 0 10px 20px rgba(2, 132, 199, 0.18);
        }

        .batch-tab-panels {
            display: grid;
        }

        .batch-tab-panel {
            display: none;
            padding: 0.85rem 0 0;
        }

        #batch-tab-planning:checked~.batch-tab-panels .batch-tab-panel-planning {
            display: block;
        }

        #batch-tab-workers:checked~.batch-tab-panels .batch-tab-panel-workers {
            display: block;
        }

        .batch-tab-panel-card {
            background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 1rem;
        }

        .batch-tab-panel-title {
            font-size: 0.78rem;
            font-weight: 900;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 1rem;
        }

        .planner-card {
            background: linear-gradient(135deg, #f8fafc 0%, #f0f4fa 100%);
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1rem;
        }

        .planning-grid .row {
            --bs-gutter-x: 0.85rem;
            --bs-gutter-y: 0.75rem;
        }

        .planning-field {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }

        .planning-field-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 1.2rem;
        }

        .field-link {
            font-size: 0.72rem;
            font-weight: 700;
            color: #0369a1;
            text-decoration: none;
        }

        .field-link:hover {
            color: #075985;
            text-decoration: underline;
        }

        .field-help {
            font-size: 0.74rem;
            color: #64748b;
            line-height: 1.3;
            margin-top: 0.15rem;
        }

        .stock-suggestion {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.4rem 0.7rem;
            border-radius: 999px;
            background: #ecfeff;
            color: #0f766e;
            font-size: 0.72rem;
            font-weight: 700;
        }

        .batch-summary-card {
            background: #ffffff;
            border: 1px solid #dbeafe;
            border-radius: 12px;
            padding: 0.9rem;
        }

        .batch-summary-title {
            font-size: 0.78rem;
            font-weight: 900;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 0.6rem;
        }

        .batch-summary-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 0.5rem;
        }

        .batch-summary-item {
            background: linear-gradient(135deg, #f8fbff 0%, #eefaf6 100%);
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 0.75rem;
        }

        .batch-summary-item-label {
            font-size: 0.7rem;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.25rem;
        }

        .batch-summary-item-value {
            font-size: 0.95rem;
            font-weight: 800;
            color: #0f172a;
        }

        .breakdown-table th,
        .breakdown-table td {
            font-size: 0.82rem;
            padding: 0.55rem 0.7rem;
        }

        .section-label {
            font-size: 0.72rem;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 0.75rem;
        }

        .metric-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            border-radius: 20px;
            padding: 0.45rem 0.85rem;
            font-size: 0.7rem;
            font-weight: 700;
            border: none;
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            color: #0284c7;
            box-shadow: 0 2px 6px rgba(2, 132, 199, 0.15);
        }

        .metric-pill.low {
            border-color: #fecaca;
            background: #fef2f2;
            color: #b91c1c;
        }

        .workers-panel {
            border: 1px solid #e6eef7;
            border-radius: 12px;
            padding: 0.85rem;
            background: #fbfdff;
        }

        .helper-box {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 10px;
            padding: 0.5rem 0.7rem;
            font-size: 0.76rem;
            color: #475569;
        }

        .search-input {
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            padding: 0.8rem 1rem;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .search-input:focus {
            border-color: #0284c7;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.1);
            outline: none;
        }

        .table-batch thead th {
            border: none;
            font-size: 0.72rem;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 1rem 0.9rem;
            background: rgba(248, 250, 252, 0.8);
            border-bottom: 2px solid #e2e8f0;
        }

        .table-batch tbody td {
            border-color: #f1f5f9;
            padding: 1rem 0.9rem;
            font-weight: 500;
            color: #334155;
        }

        .table-batch tbody tr {
            transition: all 0.2s ease;
        }

        .table-batch tbody tr:hover {
            background: rgba(2, 132, 199, 0.04);
        }

        .action-btn {
            width: 34px;
            height: 34px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            transition: all 0.2s ease;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(15, 23, 42, 0.08);
            border-color: #cbd5e1;
        }

        .value-progress-box {
            min-width: 200px;
            max-width: 250px;
            margin-left: auto;
        }

        .value-progress-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 0.6rem;
            font-size: 0.8rem;
            margin-bottom: 0.35rem;
            color: #334155;
            font-weight: 700;
        }

        .value-progress-pct {
            color: #0f172a;
        }

        .value-progress-track {
            height: 8px;
            border-radius: 999px;
            background: #e2e8f0;
            overflow: hidden;
            box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.08);
        }

        .value-progress-fill {
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(90deg, #0ea5e9 0%, #2563eb 100%);
        }

        .modal-body {
            padding: 1.75rem 2rem;
        }

        .modal-footer {
            border-top: 1px solid #e2e8f0;
            padding: 1rem 2rem 1.5rem;
        }

        .form-label {
            font-weight: 700;
            color: #475569;
            font-size: 0.74rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 0.35rem;
        }

        .form-control,
        .form-select {
            border-radius: 8px;
            border: 1px solid #dbe3ee;
            background: #ffffff;
            padding: 0.56rem 0.72rem;
            font-weight: 500;
            font-size: 0.9rem;
            color: #0f172a;
            transition: all 0.2s ease;
        }

        .batch-modal-footer .btn {
            font-size: 0.86rem;
            padding: 0.5rem 0.9rem;
            border-radius: 8px;
        }

        .batch-modal-footer .btn-custom {
            padding: 0.56rem 1rem;
            font-size: 0.86rem;
            box-shadow: 0 2px 8px rgba(2, 132, 199, 0.2);
        }

        .batch-modal-footer .btn-custom:disabled {
            opacity: 0.55;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #0284c7;
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.1);
        }

        .workers-panel .border.rounded {
            border-color: #dbe3ee !important;
            background: #ffffff;
        }

        .workers-panel .btn-outline-primary,
        .workers-panel .btn-outline-danger {
            border-radius: 8px;
            font-weight: 600;
        }

        /* Enhanced Worker Selection UI */
        .worker-item-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 0.8rem 1rem;
            margin-bottom: 0.6rem;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .worker-item-card:hover {
            border-color: #0284c7;
            background: #f0f9ff;
            transform: translateX(4px);
        }

        .worker-avatar {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            color: #475569;
            font-size: 0.85rem;
            flex-shrink: 0;
            box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);
        }

        .worker-info {
            flex-grow: 1;
            min-width: 0;
        }

        .worker-name {
            font-size: 0.9rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 0.15rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .worker-meta {
            font-size: 0.72rem;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .worker-badge {
            padding: 0.1rem 0.4rem;
            background: #f1f5f9;
            border-radius: 4px;
            font-weight: 600;
            font-size: 0.65rem;
            text-transform: uppercase;
        }

        .search-container-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 1.25rem;
            margin-bottom: 1rem;
        }

        .validation-notice {
            background: #fff1f2;
            border: 1px solid #fecade;
            color: #be123c;
            padding: 0.85rem 1.25rem;
            border-radius: 10px;
            font-size: 0.82rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 4px rgba(225, 29, 72, 0.05);
        }

        .nav-back-button {
            background: #ffffff;
            border: 1px solid #dbeafe;
            color: #1e40af;
            padding: 0.56rem 1.25rem;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
            width: 100%;
            cursor: pointer;
        }

        .nav-back-button:hover {
            background: #eff6ff;
            border-color: #bfdbfe;
            color: #1d4ed8;
            transform: translateY(-1px);
        }

        @media (max-width: 991px) {
            .batch-modal-body {
                padding: 1rem 1rem 1.25rem;
            }

            .batch-modal-header {
                padding: 1.2rem 1.2rem;
            }

            .batch-modal-footer {
                padding: 1rem 1.2rem 1.25rem;
            }

            .batch-tab-buttons {
                grid-template-columns: 1fr;
            }

            .planning-grid .row {
                --bs-gutter-x: 0.7rem;
                --bs-gutter-y: 0.65rem;
            }
        }
    </style>
    @endpush

    <div class="section-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="section-title">Production Batches</h2>
                <p class="section-subtitle mb-0">Manage rat net production by size, team, and daily output</p>
            </div>
            <button class="btn-custom" wire:click="openCreateModal">
                <i class="bi bi-plus-lg me-1"></i> New Batch
            </button>
        </div>

        <div class="mb-3" style="max-width: 320px;">
            <input type="text" class="form-control search-input" placeholder="Search batch code, size, supervisor" wire:model.live="search">
        </div>

        <div class="table-responsive">
            <table class="table align-middle table-batch mb-0">
                <thead>
                    <tr class="text-uppercase small fw-bold">
                        <th>Batch</th>

                        <th>Supervisor</th>
                        <th class="text-center">Staff</th>
                        <th class="text-center">Days</th>
                        <th class="text-end">Produced / Target</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($batches as $batch)
                    <tr style="cursor: pointer;" data-href="{{ route('production.admin.batch-details', $batch->id) }}" onclick="window.location.href=this.dataset.href;">
                        <td class="fw-bold">{{ $batch->batch_code }}</td>
                        <td>{{ $batch->supervisor->name ?? '-' }}</td>
                        <td class="text-center">{{ $batch->staffMembers->count() }}</td>
                        <td class="text-center">{{ $batch->days->count() }}</td>
                        <td>
                            @php
                            $targetQty = max(1, (int) $batch->target_qty);
                            $completedQty = (int) $batch->completed_qty;
                            $progressPercent = min(100, round(($completedQty / $targetQty) * 100));
                            @endphp
                            <div class="value-progress-box text-end">
                                <div class="value-progress-meta">
                                    <span>{{ number_format($completedQty) }} / {{ number_format($targetQty) }}</span>
                                    <span class="value-progress-pct">{{ $progressPercent }}%</span>
                                </div>
                                <div class="value-progress-track">
                                    <div class="value-progress-fill" style="width: {{ $progressPercent }}%;"></div>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="status-badge {{ $batch->status === 'completed' ? 'status-completed' : 'status-active' }}">
                                {{ $batch->status }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('production.admin.batch-details', $batch->id) }}" class="btn btn-sm action-btn" onclick="event.stopPropagation();">
                                <i class="bi bi-eye"></i>
                            </a>
                            <button type="button" class="btn btn-sm action-btn ms-1" wire:click.stop="openEditModal({{ $batch->id }})" onclick="event.stopPropagation();">
                                <i class="bi bi-pencil-fill"></i>
                            </button>
                            <button
                                type="button"
                                class="btn btn-sm action-btn ms-1"
                                wire:click.stop="confirmDeleteBatch({{ $batch->id }})"
                                onclick="event.stopPropagation();">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center   py-3   text-muted">No production batches yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">{{ $batches->links() }}</div>
    </div>

    @if($showCreateModal)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered batch-modal-dialog">
            <div class="modal-content batch-modal-content">
                <div class="modal-header batch-modal-header">
                    <div>
                        <h5 class="modal-title fw-bold mb-1">{{ $isEditMode ? 'Edit Production Batch' : 'Create Production Batch' }}</h5>
                        <p class="text-muted small mb-0">Plan size, material, workers, and duration for this production cycle</p>
                    </div>
                    <button type="button" class="btn-close" wire:click="closeCreateModal"></button>
                </div>
                <div class="modal-body batch-modal-body">
                    <div class="batch-modal-shell">
                        <input type="radio" name="batch-modal-tab" id="batch-tab-planning" class="batch-tab-toggle" checked>
                        <input type="radio" name="batch-modal-tab" id="batch-tab-workers" class="batch-tab-toggle">

                        <div class="batch-tab-buttons">
                            <label for="batch-tab-planning" class="batch-tab-button batch-tab-button-planning">1. Batch Planning</label>
                            <label for="batch-tab-workers" class="batch-tab-button batch-tab-button-workers">2. Workers & Notes</label>
                        </div>

                        <div class="batch-tab-panels">
                            <section class="batch-tab-panel batch-tab-panel-planning">
                                <div class="batch-tab-panel-card">
                                    <div class="batch-tab-panel-title">Batch Planning</div>
                                    <div class="planner-card">
                                        <div class="planning-grid">
                                            <div class="row">
                                                <div class="col-md-6 col-lg-4">
                                                    <div class="planning-field">
                                                        <div class="planning-field-head">
                                                            <label class="form-label fw-bold mb-0">Material</label>
                                                            <a href="{{ route('production.admin.settings') }}" class="field-link">Size Settings</a>
                                                        </div>
                                                        <select class="form-select" wire:model.live="production_material_id">
                                                            <option value="">Select Material</option>
                                                            @foreach($materials as $material)
                                                            <option value="{{ $material->id }}">{{ $material->name }} ({{ $material->code }})</option>
                                                            @endforeach
                                                        </select>
                                                        @error('production_material_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-6 col-lg-4">
                                                    <div class="planning-field">
                                                        <div class="planning-field-head">
                                                            <label class="form-label fw-bold mb-0">Purchase Batch</label>
                                                        </div>
                                                        <select class="form-select" wire:model.live="purchase_batch_no" @disabled($isEditMode)>
                                                            <option value="">Select batch</option>
                                                            @foreach(($availableMaterialBatches ?? []) as $availableBatch)
                                                            <option value="{{ $availableBatch['purchase_batch_no'] }}">
                                                                {{ $availableBatch['purchase_batch_no'] }} | Remaining {{ number_format($availableBatch['remaining_quantity'], 3) }} ton
                                                            </option>
                                                            @endforeach
                                                            @if($isEditMode && !collect($availableMaterialBatches ?? [])->contains('purchase_batch_no', $purchase_batch_no))
                                                            <option value="{{ $purchase_batch_no }}">{{ $purchase_batch_no }} (Locked)</option>
                                                            @endif
                                                        </select>
                                                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                                            @if(($availableMaterialBatches ?? collect())->isNotEmpty())
                                                            <span class="stock-suggestion">
                                                                Suggested: {{ $availableMaterialBatches->first()['purchase_batch_no'] }}
                                                            </span>
                                                            @endif
                                                            @if($isEditMode)
                                                            <span class="field-help mb-0">Stock batch is locked in edit mode.</span>
                                                            @endif
                                                        </div>
                                                        @error('purchase_batch_no') <span class="text-danger small">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-6 col-lg-4">
                                                    <div class="planning-field">
                                                        <div class="planning-field-head">
                                                            <label class="form-label fw-bold mb-0">Allocated / Available (Ton)</label>
                                                        </div>
                                                        @php
                                                            $totalAllocated = array_sum(array_column($estimatedTargetBreakdown, 'allocated_ton'));
                                                        @endphp
                                                        <input type="number" class="form-control" value="{{ number_format($totalAllocated, 3, '.', '') }}" readonly>
                                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                                            <span class="metric-pill {{ $availableMaterialTon <= 0 ? 'low' : '' }}">
                                                                {{ $purchase_batch_no ? 'Batch: ' . $purchase_batch_no : 'Select a purchase batch' }}
                                                            </span>
                                                            @if($purchase_batch_no && $totalAllocated < $availableMaterialTon)
                                                            <span class="metric-pill" style="background: linear-gradient(135deg, #fefce8 0%, #fef9c3 100%); color: #a16207;">
                                                                Remaining: {{ number_format($availableMaterialTon - $totalAllocated, 3) }} ton
                                                            </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="batch-summary-card">
                                                        <div class="batch-summary-title">Auto Production Target Breakdown</div>
                                                        @php
                                                            $totalAllocatedSummary = array_sum(array_column($estimatedTargetBreakdown, 'allocated_ton'));
                                                            $totalAvailableSummary = array_sum(array_column($estimatedTargetBreakdown, 'available_ton'));
                                                        @endphp
                                                        <div class="batch-summary-grid mb-3">
                                                            <div class="batch-summary-item">
                                                                <div class="batch-summary-item-label">Purchase Batch</div>
                                                                <div class="batch-summary-item-value">{{ $purchase_batch_no ?: '-' }}</div>
                                                            </div>
                                                            <div class="batch-summary-item">
                                                                <div class="batch-summary-item-label">Allocated / Total</div>
                                                                <div class="batch-summary-item-value">{{ number_format($totalAllocatedSummary, 3) }} / {{ number_format($totalAvailableSummary, 3) }} ton</div>
                                                            </div>
                                                            <div class="batch-summary-item">
                                                                <div class="batch-summary-item-label">Estimated Total</div>
                                                                <div class="batch-summary-item-value">{{ number_format($estimated_target_qty) }} pcs</div>
                                                            </div>
                                                            <div class="batch-summary-item">
                                                                <div class="batch-summary-item-label">Estimated Days</div>
                                                                <div class="batch-summary-item-value">{{ $estimated_days ?: 0 }}</div>
                                                            </div>
                                                        </div>

                                                        <div class="table-responsive">
                                                            <table class="table table-bordered breakdown-table mb-0">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th>Size</th>
                                                                        <th class="text-end">Available (Ton)</th>
                                                                        <th class="text-center" style="min-width: 140px;">Allocate (Ton)</th>
                                                                        <th class="text-end">Remaining</th>
                                                                        <th class="text-end">Estimated Target</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @forelse($estimatedTargetBreakdown as $row)
                                                                    @php
                                                                        $remaining = ($row['available_ton'] ?? 0) - ($row['allocated_ton'] ?? 0);
                                                                    @endphp
                                                                    <tr>
                                                                        <td class="fw-bold">{{ $row['size'] }}</td>
                                                                        <td class="text-end">{{ number_format($row['available_ton'], 3) }}</td>
                                                                        <td class="text-center">
                                                                            <input
                                                                                type="number"
                                                                                class="form-control form-control-sm text-center"
                                                                                style="max-width: 120px; margin: 0 auto;"
                                                                                step="0.001"
                                                                                min="0"
                                                                                max="{{ $row['available_ton'] }}"
                                                                                wire:model.live.debounce.400ms="allocatedTonBreakdown.{{ $row['size'] }}"
                                                                                placeholder="0.000">
                                                                        </td>
                                                                        <td class="text-end {{ $remaining > 0 ? 'text-warning' : '' }}">
                                                                            {{ number_format($remaining, 3) }}
                                                                        </td>
                                                                        <td class="text-end fw-bold" style="background: rgba(220, 252, 231, 0.3);">{{ number_format($row['estimated']) }}</td>
                                                                    </tr>
                                                                    @empty
                                                                    <tr>
                                                                        <td colspan="5" class="text-center text-muted">Select a purchase batch to see the breakdown.</td>
                                                                    </tr>
                                                                    @endforelse
                                                                </tbody>
                                                                <tfoot>
                                                                    <tr>
                                                                        <th>Total</th>
                                                                        <th class="text-end">{{ number_format($totalAvailableSummary, 3) }}</th>
                                                                        <th class="text-center fw-bold" style="color: #0284c7;">{{ number_format($totalAllocatedSummary, 3) }}</th>
                                                                        <th class="text-end">{{ number_format($totalAvailableSummary - $totalAllocatedSummary, 3) }}</th>
                                                                        <th class="text-end">{{ number_format($estimated_target_qty) }}</th>
                                                                    </tr>
                                                                </tfoot>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6 col-lg-4">
                                                    <div class="planning-field">
                                                        <div class="planning-field-head">
                                                            <label class="form-label fw-bold mb-0">Estimated Target</label>
                                                        </div>
                                                        <input type="number" class="form-control" value="{{ $estimated_target_qty }}" readonly>
                                                        <input type="hidden" wire:model="target_qty">
                                                        @error('target_qty') <span class="text-danger small">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-6 col-lg-4">
                                                    <div class="planning-field">
                                                        <div class="planning-field-head">
                                                            <label class="form-label fw-bold mb-0">Estimated Days</label>
                                                        </div>
                                                        <input
                                                            type="number"
                                                            min="1"
                                                            step="1"
                                                            class="form-control"
                                                            wire:model.live="estimated_days"
                                                            placeholder="e.g. 30">
                                                        <div class="field-help">User-entered production duration for this batch.</div>
                                                        @error('estimated_days') <span class="text-danger small">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-6 col-lg-4">
                                                    <div class="planning-field">
                                                        <div class="planning-field-head">
                                                            <label class="form-label fw-bold mb-0">Start Date</label>
                                                        </div>
                                                        <input type="date" class="form-control" wire:model="start_date">
                                                        @error('start_date') <span class="text-danger small">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="helper-box">
                                                        Estimation rule: Target = (Material Ton x 1000) / Size Setting.
                                                        Current settings: S {{ $sizeFactors['S'] ?? 0.3 }}, M {{ $sizeFactors['M'] ?? 0.5 }}, L {{ $sizeFactors['L'] ?? 0.75 }}.
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="batch-step-actions">
                                                        <div></div>
                                                        <label for="batch-tab-workers" class="btn btn-custom">Next: Workers & Notes</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>

                            <section class="batch-tab-panel batch-tab-panel-workers">
                                <div class="batch-tab-panel-card">
                                    <div class="batch-tab-panel-title">Workers & Notes</div>
                                    @php
                                    $selectedWorkers = $eligibleStaff->whereIn('id', array_map('intval', $staff_ids));
                                    @endphp
                                    
                                    <div class="search-container-box">
                                        <div class="row g-3">
                                            <div class="col-md-7">
                                                <div class="planning-field">
                                                    <label class="form-label fw-bold">Supervisor</label>
                                                    <select class="form-select" wire:model="supervisor_id">
                                                        <option value="">Select Supervisor</option>
                                                        @foreach($supervisors as $staff)
                                                        <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('supervisor_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-5 d-flex align-items-end">
                                                <label for="batch-tab-planning" class="nav-back-button">
                                                    <i class="bi bi-arrow-left-circle"></i> Back to Batch Planning
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-4">
                                        <div class="col-lg-6">
                                            <label class="form-label fw-bold">{{ $isEditMode ? 'Add More Workers' : 'Find & Add Workers' }}</label>
                                            <div class="mb-3">
                                                <div class="input-group">
                                                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                                                    <input
                                                        type="text"
                                                        class="form-control border-start-0 ps-0"
                                                        placeholder="Name, Phone, NIC..."
                                                        wire:model.live="workerSearch">
                                                </div>
                                            </div>

                                            <div class="border rounded-4 p-3 bg-light-subtle" style="height: 320px; overflow-y: auto; border-style: dashed !important;">
                                                @if(trim($workerSearch) === '')
                                                <div class="h-100 d-flex flex-column align-items-center justify-content-center text-center p-4">
                                                    <i class="bi bi-person-plus text-muted" style="font-size: 2.5rem; opacity: 0.3;"></i>
                                                    <div class="text-muted small mt-2">Type a worker name to search</div>
                                                </div>
                                                @else
                                                @forelse($filteredEligibleStaff as $staff)
                                                <div class="worker-item-card">
                                                    <div class="worker-avatar">{{ substr($staff->name, 0, 1) }}</div>
                                                    <div class="worker-info">
                                                        <div class="worker-name">{{ $staff->name }}</div>
                                                        <div class="worker-meta">
                                                            <span class="worker-badge">{{ $staff->detail->work_role ?? 'Staff' }}</span>
                                                            <span>{{ $staff->contact ?: ($staff->email ?: 'No contact') }}</span>
                                                        </div>
                                                    </div>
                                                    @if(in_array((int) $staff->id, array_map('intval', $staff_ids), true))
                                                    <button type="button" class="btn btn-sm btn-light text-success fw-bold" disabled><i class="bi bi-check-lg"></i> Added</button>
                                                    @else
                                                    <button type="button" class="btn btn-sm btn-outline-primary" wire:click="addWorker({{ $staff->id }})">Add</button>
                                                    @endif
                                                </div>
                                                @empty
                                                <div class="text-center py-5">
                                                    <div class="text-muted small">No workers found.</div>
                                                </div>
                                                @endforelse
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <label class="form-label fw-bold mb-0">Selected Workers ({{ count($staff_ids) }})</label>
                                            </div>
                                            
                                            <div class="border rounded-4 p-3 bg-white shadow-sm" style="height: 320px; overflow-y: auto;">
                                                @forelse($selectedWorkers as $selectedWorker)
                                                <div class="worker-item-card">
                                                    <div class="worker-avatar" style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); color: #1e40af;">
                                                        {{ substr($selectedWorker->name, 0, 1) }}
                                                    </div>
                                                    <div class="worker-info">
                                                        <div class="worker-name">{{ $selectedWorker->name }}</div>
                                                        <div class="worker-meta">
                                                            <span>{{ $selectedWorker->contact ?: 'No contact' }}</span>
                                                        </div>
                                                    </div>
                                                    <button type="button" class="btn btn-sm btn-outline-danger border-0" wire:click="removeWorker({{ $selectedWorker->id }})">
                                                        <i class="bi bi-x-circle"></i>
                                                    </button>
                                                </div>
                                                @empty
                                                <div class="h-100 d-flex flex-column align-items-center justify-content-center text-center p-4">
                                                    <i class="bi bi-people text-muted" style="font-size: 2.5rem; opacity: 0.1;"></i>
                                                    <div class="text-muted small mt-2">No workers selected yet.</div>
                                                </div>
                                                @endforelse
                                            </div>
                                            @error('staff_ids') <span class="text-danger small d-block mt-1">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <div class="mt-4">
                                        <label class="form-label fw-bold">Additional Notes</label>
                                        <textarea class="form-control" rows="3" wire:model="notes" placeholder="Optional production notes..."></textarea>
                                        @error('notes') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
                <div class="modal-footer batch-modal-footer">
                    @php
                    $missingItems = [];

                    if (blank($production_material_id)) {
                    $missingItems[] = 'Material';
                    }

                    if (blank($purchase_batch_no)) {
                    $missingItems[] = 'Purchase Batch';
                    }

                    if ((float) ($planned_material_ton ?? 0) <= 0) {
                        $missingItems[]='Allocated Stock' ;
                        }

                        if ((int) $estimated_days <=0) {
                        $missingItems[]='Estimated Days' ;
                        }

                        if (blank($start_date)) {
                        $missingItems[]='Start Date' ;
                        }

                        if ((int) $target_qty <=0) {
                        $missingItems[]='Estimated Target' ;
                        }

                        if (blank($supervisor_id)) {
                        $missingItems[]='Supervisor' ;
                        }

                        if (count($staff_ids) <=0) {
                        $missingItems[]='Workers' ;
                        }
                        $canSubmitBatch=count($missingItems)===0;
                        @endphp
                        <div class="w-100">
                        @if(count($missingItems) > 0)
                        <div class="validation-notice">
                            <i class="bi bi-exclamation-triangle-fill mt-1"></i>
                            <div>
                                <div class="mb-1">Required fields missing:</div>
                                <div class="small opacity-75">{{ implode(', ', $missingItems) }}</div>
                            </div>
                        </div>
                        @endif

                        @if($errors->any() && count($missingItems) === 0)
                        <div class="validation-notice" style="background: #fff7ed; border-color: #ffedd5; color: #9a3412;">
                            <i class="bi bi-info-circle-fill mt-1"></i>
                            <div>{{ $errors->first() }}</div>
                        </div>
                        @endif
                </div>
                <button class="btn btn-light" wire:click="closeCreateModal">Cancel</button>
                @if($isEditMode)
                <button class="btn-custom" wire:click="updateBatch" wire:loading.attr="disabled">Update Batch</button>
                @else
                <button class="btn-custom" wire:click="createBatch" wire:loading.attr="disabled">Create Batch</button>
                @endif
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
                <h5 class="modal-title fw-bold text-danger">Delete Production Batch</h5>
                <button type="button" class="btn-close" wire:click="cancelDeleteBatch"></button>
            </div>
            <div class="modal-body">
                <p class="mb-1">Are you sure you want to delete this batch?</p>
                <p class="mb-0"><strong>{{ $deletingBatchCode }}</strong></p>
                <small class="text-muted">This will permanently remove the batch and related day logs.</small>
            </div>
            <div class="modal-footer">
                <button class="btn btn-light" wire:click="cancelDeleteBatch">Cancel</button>
                <button class="btn btn-danger" wire:click="performDeleteBatch">Yes, Delete</button>
            </div>
        </div>
    </div>
</div>
@endif
</div>