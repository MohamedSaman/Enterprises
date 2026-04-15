<div class="container-fluid py-3">
    @push('styles')
    <style>
        .page-header {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            margin-bottom: 1.5rem;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
        }

        .page-subtitle {
            color: #64748b;
            margin: 0;
            font-weight: 500;
        }

        .collapse-card {
            background: #fff;
            border: 1px solid #e8eef5;
            border-radius: 1rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            overflow: hidden;
        }

        .collapse-header {
            width: 100%;
            border: 0;
            background: #fff;
            padding: 1.15rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-weight: 700;
            color: #0f172a;
            text-align: left;
        }

        .collapse-content {
            border-top: 1px solid #eef2f6;
            padding: 1.5rem;
        }

        .section-card {
            background: #fff;
            border: 1px solid #eef2f6;
            border-radius: 1rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            padding: 1.5rem;
        }

        .hint-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1rem;
        }

        .section-spacer {
            margin-top: 1rem;
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
    </style>
    @endpush

    <div class="page-header">
        <div class="bg-info-subtle rounded-circle d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
            <i class="bi bi-sliders fs-3 text-info"></i>
        </div>
        <div>
            <h1 class="page-title">Production Settings</h1>
            <p class="page-subtitle">Manage size conversion values used for batch target estimation.</p>
        </div>
    </div>

    <div class="collapse-card">
        <button type="button" class="collapse-header" wire:click="$toggle('showSizeSettingsSection')">
            <span>
                <i class="bi bi-rulers fs-5 me-3 text-info"></i>
                Production Size Conversion Settings
            </span>
            <i class="bi {{ $showSizeSettingsSection ? 'bi-chevron-up' : 'bi-chevron-down' }} fs-4 text-secondary"></i>
        </button>

        @if($showSizeSettingsSection)
        <div class="collapse-content">
            <div class="section-card">
                <p class="text-muted mb-4">Define ton usage needed for 1000 cages by size. Batch target estimation uses these values.</p>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Size S (ton per 1000 cages)</label>
                        <input type="number" step="0.001" min="0.01" class="form-control" wire:model="size_s_ton_per_1000">
                        @error('size_s_ton_per_1000') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Size M (ton per 1000 cages)</label>
                        <input type="number" step="0.001" min="0.01" class="form-control" wire:model="size_m_ton_per_1000">
                        @error('size_m_ton_per_1000') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Size L (ton per 1000 cages)</label>
                        <input type="number" step="0.001" min="0.01" class="form-control" wire:model="size_l_ton_per_1000">
                        @error('size_l_ton_per_1000') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="hint-box mt-4">
                    <div class="fw-bold mb-2">How estimation works</div>
                    <div class="small text-muted">Estimated target = (Material ton × 1000) / Selected size setting</div>
                    <div class="small text-muted">Example: 5 ton with Size M setting 0.5 => (5 × 1000) / 0.5 = 10,000</div>
                </div>

                <div class="mt-4">
                    <button class="btn-custom" wire:click="saveSettings">Save Settings</button>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="collapse-card section-spacer">
        <button type="button" class="collapse-header" wire:click="$toggle('showCommissionSettingsSection')">
            <span>
                <i class="bi bi-cash-coin fs-5 me-3 text-success"></i>
                Employee Commission Rules
            </span>
            <i class="bi {{ $showCommissionSettingsSection ? 'bi-chevron-up' : 'bi-chevron-down' }} fs-4 text-secondary"></i>
        </button>

        @if($showCommissionSettingsSection)
        <div class="collapse-content">
            <div class="section-card">
                <p class="text-muted mb-4">Set the commission rules used by production daily logs. The first threshold uses one rate, and items after the threshold use a higher rate.</p>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Threshold Items</label>
                        <input type="number" min="1" class="form-control" wire:model="commission_threshold_items">
                        @error('commission_threshold_items') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Rate Up To Threshold (per item)</label>
                        <input type="number" step="0.01" min="0" class="form-control" wire:model="commission_rate_upto_threshold">
                        @error('commission_rate_upto_threshold') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Rate After Threshold (per item)</label>
                        <input type="number" step="0.01" min="0" class="form-control" wire:model="commission_rate_after_threshold">
                        @error('commission_rate_after_threshold') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="hint-box mt-4">
                    <div class="fw-bold mb-2">Commission Example</div>
                    <div class="small text-muted">If production is 12,000 items:</div>
                    <div class="small text-muted">First 10,000 items = 10 each, remaining 2,000 items = 15 each</div>
                </div>

                <div class="mt-4">
                    <button class="btn-custom" wire:click="saveSettings">Save Settings</button>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>