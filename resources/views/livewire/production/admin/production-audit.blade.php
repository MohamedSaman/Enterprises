<div class="dashboard-wrapper">
    @push('styles')
    <style>
        .audit-wrapper {
            background: linear-gradient(135deg, #f5f7fb 0%, #f0f4fa 100%);
            min-height: 100vh;
            padding: 1rem 0;
        }

        .audit-card {
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid rgba(30, 41, 59, 0.08);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
            padding: 1.5rem;
        }

        .audit-title {
            font-size: 1.35rem;
            font-weight: 900;
            color: #0f172a;
            margin-bottom: 0.35rem;
        }

        .audit-subtitle {
            color: #64748b;
            font-size: 0.88rem;
            margin-bottom: 1.25rem;
        }

        .summary-box {
            border: 1px solid #dbeafe;
            border-radius: 12px;
            background: linear-gradient(135deg, #f8fbff 0%, #eef8ff 100%);
            padding: 0.95rem;
            height: 100%;
        }

        .summary-label {
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.06em;
            margin-bottom: 0.3rem;
        }

        .summary-value {
            font-size: 1.1rem;
            font-weight: 900;
            color: #0f172a;
        }

        .transfer-btn {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            color: #ffffff;
            border: 0;
            border-radius: 10px;
            padding: 0.65rem 1.1rem;
            font-weight: 700;
            box-shadow: 0 5px 15px rgba(2, 132, 199, 0.25);
        }

        .transfer-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            box-shadow: none;
        }

        .meta-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            border-radius: 999px;
            border: 1px solid #dbeafe;
            padding: 0.35rem 0.7rem;
            font-size: 0.75rem;
            font-weight: 700;
            color: #0369a1;
            background: #eff6ff;
        }

        .transfer-grid {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
        }

        .transfer-grid table {
            margin-bottom: 0;
        }

        .transfer-grid th,
        .transfer-grid td {
            vertical-align: middle;
            font-size: 0.84rem;
            padding: 0.7rem 0.8rem;
        }

        .transfer-grid thead th {
            background: #f8fafc;
            font-weight: 800;
            color: #334155;
            border-bottom: 1px solid #e2e8f0;
        }

        .qty-input {
            max-width: 140px;
        }
    </style>
    @endpush

    <div class="audit-wrapper">
        <div class="audit-card">
            <h2 class="audit-title">Production Audit Transfer</h2>
            <p class="audit-subtitle mb-3">Select a production batch to view total produced items and transfer them into inventory products by size variant (S, M, L).</p>

            <div class="row g-3 align-items-end mb-3">
                <div class="col-lg-8">
                    <label class="form-label fw-bold">Select Production Batch</label>
                    <select class="form-select" wire:model.live="selectedBatchId">
                        <option value="">Choose batch</option>
                        @foreach($batches as $batch)
                        <option value="{{ $batch->id }}">
                            {{ $batch->batch_code }} | {{ $batch->material->name ?? 'Material N/A' }} | Produced: {{ number_format((int) ($batch->produced_total ?? 0)) }}
                        </option>
                        @endforeach
                    </select>
                    @error('selectedBatchId') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>
                <div class="col-lg-4 text-lg-end">
                    <button
                        type="button"
                        class="transfer-btn"
                        wire:click="transferToAudit"
                        wire:loading.attr="disabled"
                        @disabled(!$selectedBatch || $availableTotals['total'] <=0)>
                        <span wire:loading.remove wire:target="transferToAudit">Transfer Selected Qty</span>
                        <span wire:loading wire:target="transferToAudit">Transferring...</span>
                    </button>
                </div>
            </div>

            @if($selectedBatch)
            <div class="mb-3 d-flex flex-wrap gap-2">
                <span class="meta-pill">Batch: {{ $selectedBatch->batch_code }}</span>
                <span class="meta-pill">Supervisor: {{ $selectedBatch->supervisor->name ?? '-' }}</span>
                <span class="meta-pill">Status: {{ strtoupper($selectedBatch->status) }}</span>
                <span class="meta-pill">Total Available: {{ number_format($availableTotals['total']) }}</span>
                @if($selectedBatch->transferred_to_inventory_at)
                <span class="meta-pill">Last Transfer: {{ $selectedBatch->transferred_to_inventory_at->format('Y-m-d H:i') }}</span>
                @endif
            </div>

            <div class="transfer-grid mb-3">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Size</th>
                                <th class="text-end">Produced</th>
                                <th class="text-end">Transferred</th>
                                <th class="text-end">Available</th>
                                <th class="text-end">Transfer Now</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(['S', 'M', 'L'] as $size)
                            <tr>
                                <td class="fw-bold">{{ $size }}</td>
                                <td class="text-end">{{ number_format($sizeTotals[$size]) }}</td>
                                <td class="text-end">{{ number_format($transferredTotals[$size]) }}</td>
                                <td class="text-end">{{ number_format($availableTotals[$size]) }}</td>
                                <td class="text-end">
                                    <input
                                        type="number"
                                        min="0"
                                        max="{{ $availableTotals[$size] }}"
                                        class="form-control qty-input ms-auto"
                                        wire:model.live="transferQty.{{ $size }}"
                                        @disabled($availableTotals[$size] <=0)>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Total</th>
                                <th class="text-end">{{ number_format($sizeTotals['total']) }}</th>
                                <th class="text-end">{{ number_format($transferredTotals['total']) }}</th>
                                <th class="text-end">{{ number_format($availableTotals['total']) }}</th>
                                <th class="text-end">{{ number_format((int) ($transferQty['S'] ?? 0) + (int) ($transferQty['M'] ?? 0) + (int) ($transferQty['L'] ?? 0)) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            @error('transferQty.S') <span class="text-danger small d-block">{{ $message }}</span> @enderror
            @error('transferQty.M') <span class="text-danger small d-block">{{ $message }}</span> @enderror
            @error('transferQty.L') <span class="text-danger small d-block">{{ $message }}</span> @enderror

            <div class="alert alert-info border-0 mt-3 mb-0">
                You can transfer this batch in parts. Repeated transfers for the same batch and size increase stock in the same inventory product/batch.
            </div>
            @else
            <div class="alert alert-warning border-0 mb-0">No produced batches available for audit transfer yet.</div>
            @endif
        </div>
    </div>
</div>