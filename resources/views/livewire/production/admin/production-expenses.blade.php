<div class="dashboard-wrapper">
    @push('styles')
    <style>
        .dashboard-wrapper {
            background-color: #f8faff;
            min-height: 100vh;
            padding: 1rem 0;
        }

        .panel-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #eef2f6;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            padding: 1.5rem;
        }

        .stat-chip {
            border-radius: 999px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            padding: 0.45rem 0.8rem;
            font-weight: 700;
            color: #334155;
            font-size: 0.8rem;
        }
    </style>
    @endpush

    <div class="panel-card mb-3">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <h4 class="fw-bold mb-1">Production Batch Expenses</h4>
                <div class="text-muted small">Add fixed-cost values batch wise. These values are used by the production salary statement.</div>
            </div>
            <div style="min-width: 280px;">
                <label class="form-label small text-uppercase fw-bold text-muted mb-1">Select Batch</label>
                <select class="form-select" wire:model.live="selectedBatchId">
                    @foreach($batches as $batch)
                    <option value="{{ $batch->id }}">{{ $batch->batch_code }} - {{ $batch->size }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="panel-card mb-3">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label fw-bold">Date</label>
                <input type="date" class="form-control" wire:model="date">
                @error('date') <span class="text-danger small">{{ $message }}</span> @enderror
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Category</label>
                <select class="form-select" wire:model="category">
                    @foreach($fixedCategories as $cat)
                    <option value="{{ $cat }}">{{ $cat }}</option>
                    @endforeach
                </select>
                @error('category') <span class="text-danger small">{{ $message }}</span> @enderror
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Amount</label>
                <input type="number" step="0.01" min="0" class="form-control" wire:model="amount" placeholder="0.00">
                @error('amount') <span class="text-danger small">{{ $message }}</span> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Note</label>
                <input type="text" class="form-control" wire:model="description" placeholder="Optional details">
                @error('description') <span class="text-danger small">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="mt-3 d-flex gap-2">
            <button class="btn btn-primary" wire:click="saveExpense">{{ $editingExpenseId ? 'Update Expense' : 'Save Expense' }}</button>
            @if($editingExpenseId)
            <button class="btn btn-light" wire:click="resetForm">Cancel Edit</button>
            @endif
        </div>
    </div>

    <div class="panel-card mb-3">
        <h5 class="fw-bold mb-3">Category Totals (Selected Batch)</h5>
        <div class="d-flex flex-wrap gap-2">
            @foreach($categoryTotals as $label => $value)
            <span class="stat-chip">{{ $label }}: Rs. {{ number_format((float) $value, 2) }}</span>
            @endforeach
        </div>
    </div>

    <div class="panel-card">
        <h5 class="fw-bold mb-3">Expense Entries</h5>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr class="small text-uppercase">
                        <th>Date</th>
                        <th>Category</th>
                        <th class="text-end">Amount</th>
                        <th>Note</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $expense)
                    <tr>
                        <td>{{ optional($expense->date)->format('Y-m-d') }}</td>
                        <td class="fw-semibold">{{ $expense->category }}</td>
                        <td class="text-end fw-bold">Rs. {{ number_format((float) $expense->amount, 2) }}</td>
                        <td>{{ $expense->description ?: '-' }}</td>
                        <td>
                            <div class="d-flex justify-content-end gap-2">
                                <button class="btn btn-sm btn-outline-primary" wire:click="editExpense({{ $expense->id }})">Edit</button>
                                <button class="btn btn-sm btn-outline-danger" wire:click="deleteExpense({{ $expense->id }})">Delete</button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No production fixed expenses found for this batch.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>