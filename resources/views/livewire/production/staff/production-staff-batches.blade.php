<div class="dashboard-wrapper">
    @push('styles')
    <style>
        .dashboard-wrapper {
            min-height: 100vh;
            padding: 1.2rem 0 2rem;
            background:
                radial-gradient(circle at top left, rgba(14, 165, 233, 0.12), transparent 30%),
                linear-gradient(180deg, #f8fbff 0%, #eef4fb 100%);
        }

        .panel {
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(148, 163, 184, 0.22);
            border-radius: 18px;
            padding: 1.25rem;
            box-shadow: 0 14px 36px rgba(15, 23, 42, 0.06);
            backdrop-filter: blur(10px);
        }

        .page-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.35rem 0.75rem;
            border-radius: 999px;
            background: #e0f2fe;
            color: #0369a1;
            font-size: 0.72rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .page-title {
            font-size: 1.35rem;
            font-weight: 900;
            color: #0f172a;
            margin-bottom: 0.2rem;
        }

        .search-wrap {
            position: relative;
            max-width: 380px;
        }

        .search-wrap input {
            padding-left: 2.5rem;
            border-radius: 999px;
            border: 1px solid #dbe7f3;
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.04);
        }

        .search-wrap .bi-search {
            position: absolute;
            left: 0.95rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            pointer-events: none;
        }

        .batch-table {
            border-collapse: separate;
            border-spacing: 0 0.7rem;
        }

        .batch-table thead th {
            border-bottom: 0;
            color: #64748b;
            font-size: 0.74rem;
            letter-spacing: 0.08em;
        }

        .batch-table tbody tr {
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.04);
        }

        .batch-table tbody tr td {
            padding-top: 1rem;
            padding-bottom: 1rem;
            border-top: 1px solid #dbe7f3;
            border-bottom: 1px solid #dbe7f3;
        }

        .batch-table tbody tr td:first-child {
            border-left: 1px solid #dbe7f3;
            border-top-left-radius: 14px;
            border-bottom-left-radius: 14px;
        }

        .batch-table tbody tr td:last-child {
            border-right: 1px solid #dbe7f3;
            border-top-right-radius: 14px;
            border-bottom-right-radius: 14px;
        }

        .size-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 38px;
            padding: 0.25rem 0.55rem;
            border-radius: 999px;
            background: #e2e8f0;
            color: #0f172a;
            font-weight: 800;
            font-size: 0.75rem;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            padding: 0.3rem 0.7rem;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 800;
            text-transform: uppercase;
        }

        .status-pill.active {
            background: #e0f2fe;
            color: #0369a1;
        }

        .status-pill.completed {
            background: #dcfce7;
            color: #15803d;
        }

        .empty-state {
            padding: 1.5rem 1rem;
            color: #64748b;
        }

        .empty-state .bi {
            font-size: 2rem;
            color: #94a3b8;
        }

        .table-action {
            border-radius: 999px;
        }

        .table-action:hover {
            transform: translateY(-1px);
        }

        .progress-box {
            min-width: 180px;
        }

        .progress-track {
            height: 10px;
            border-radius: 999px;
            background: #e2e8f0;
            overflow: hidden;
            box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.08);
        }

        .progress-fill {
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(90deg, #0ea5e9 0%, #2563eb 100%);
        }

        .progress-meta {
            display: flex;
            justify-content: space-between;
            gap: 0.5rem;
            font-size: 0.78rem;
            margin-bottom: 0.35rem;
            color: #475569;
            font-weight: 700;
        }

        .progress-meta .pct {
            color: #0f172a;
        }
    </style>
    @endpush

    <div class="panel">
        <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-3">
            <div>
                <span class="page-badge mb-2">Supervisor Workspace</span>
                <div class="page-title">My Production Batches</div>
                <p class="text-muted mb-0">Open a batch to add daily logs with expenses and produced item count.</p>
            </div>
            <a href="{{ route('production.staff.dashboard') }}" class="btn btn-light rounded-pill">Dashboard</a>
        </div>

        <div class="search-wrap mb-3">
            <i class="bi bi-search"></i>
            <input type="text" class="form-control" placeholder="Search batch, size, status" wire:model.live="search">
        </div>

        <div class="table-responsive">
            <table class="table align-middle batch-table mb-0">
                <thead class="table-light">
                    <tr class="small text-uppercase">
                        <th>Batch</th>

                        <th class="text-center">Workers</th>
                        <th class="text-center">Days</th>
                        <th class="text-end">Produced / Target</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($batches as $batch)
                    <tr>
                        <td class="fw-bold">{{ $batch->batch_code }}</td>

                        <td class="text-center">{{ $batch->staffMembers->count() }}</td>
                        <td class="text-center">{{ $batch->days->count() }}</td>
                        <td>
                            @php
                            $targetQty = max(1, (int) $batch->target_qty);
                            $completedQty = (int) $batch->completed_qty;
                            $estimatedDays = max(1, (int) ($batch->estimated_days ?? 1));
                            $estimatedDailyTarget = (int) round($targetQty / $estimatedDays);
                            $progressPercent = min(100, round(($completedQty / $targetQty) * 100));
                            @endphp
                            <div class="progress-box ms-auto">
                                <div class="progress-meta">
                                    <span>{{ number_format($completedQty) }} / {{ number_format($targetQty) }} pcs</span>
                                    <span class="pct">{{ $progressPercent }}%</span>
                                </div>
                                <div class="progress-track">
                                    <div class="progress-fill" style="width: {{ $progressPercent }}%;"></div>
                                </div>
                                <div class="small text-muted mt-1" style="font-size: 0.72rem;">Daily Target: <strong style="color: #0f172a;">{{ number_format($estimatedDailyTarget) }} pcs/day</strong></div>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="status-pill {{ $batch->status }}">
                                {{ ucfirst($batch->status) }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('production.staff.batch-details', $batch->id) }}" class="btn btn-sm btn-light table-action">
                                <i class="bi bi-eye me-1"></i> Open
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center empty-state">
                            <i class="bi bi-box-seam d-block mb-2"></i>
                            No batches assigned.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">{{ $batches->links() }}</div>
    </div>
</div>