<div class="dashboard-wrapper">
    @push('styles')
    <style>
        .dashboard-wrapper {
            background-color: #f8faff;
            min-height: 100vh;
            padding: 1rem 0;
        }

        .panel {
            background: #fff;
            border: 1px solid #e8eef5;
            border-radius: 12px;
            padding: 1.25rem;
        }
    </style>
    @endpush

    <div class="panel">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="fw-bold mb-1">My Production Batches</h4>
                <p class="text-muted mb-0">Open a batch to add daily logs with expenses and produced item count.</p>
            </div>
            <a href="{{ route('production.staff.dashboard') }}" class="btn btn-light">Dashboard</a>
        </div>

        <div class="mb-3" style="max-width: 320px;">
            <input type="text" class="form-control" placeholder="Search batch, size, status" wire:model.live="search">
        </div>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr class="small text-uppercase">
                        <th>Batch</th>
                        <th>Size</th>
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
                        <td>{{ $batch->size }}</td>
                        <td class="text-center">{{ $batch->staffMembers->count() }}</td>
                        <td class="text-center">{{ $batch->days->count() }}</td>
                        <td class="text-end fw-bold">{{ number_format($batch->completed_qty) }} / {{ number_format($batch->target_qty) }}</td>
                        <td class="text-center">
                            <span class="badge {{ $batch->status === 'completed' ? 'bg-success-subtle text-success' : 'bg-primary-subtle text-primary' }}">
                                {{ ucfirst($batch->status) }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('production.staff.batch-details', $batch->id) }}" class="btn btn-sm btn-light">
                                <i class="bi bi-eye me-1"></i> Open
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">No batches assigned.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">{{ $batches->links() }}</div>
    </div>
</div>