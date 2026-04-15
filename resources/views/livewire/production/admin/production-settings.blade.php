<div class="container-fluid py-3" style="background: linear-gradient(135deg, #f5f7fb 0%, #f0f4fa 100%); min-height: 100vh; margin: -1rem; padding: 2rem !important;">
    @push('styles')
    <style>
        .collapse-card {
            background: #ffffff;
            border: 1px solid rgba(30, 41, 59, 0.08);
            border-radius: 14px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }

        .collapse-header {
            width: 100%;
            border: 0;
            background: linear-gradient(135deg, #f8fafc 0%, #f0f4fa 100%);
            padding: 1.5rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-weight: 700;
            color: #0f172a;
            text-align: left;
            transition: all 0.3s ease;
        }

        .collapse-header:hover {
            background: linear-gradient(135deg, #eff6ff 0%, #e0f2fe 100%);
        }

        .collapse-content {
            border-top: 1px solid #e2e8f0;
            padding: 2rem;
            background: #ffffff;
        }

        .section-card {
            background: linear-gradient(135deg, #f8fafc 0%, #f0f4fa 100%);
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.08);
            padding: 1.5rem;
        }

        .hint-box {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border: 1px solid #bfdbfe;
            border-radius: 12px;
            padding: 1rem;
            color: #1e40af;
        }

        .btn-custom {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            padding: 0.85rem 1.85rem;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);
            transition: all 0.3s ease;
        }

        .btn-custom:hover {
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

        .form-control {
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            padding: 0.85rem 1rem;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: #0284c7;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.1);
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 900;
            color: #0f172a;
        }

        .page-subtitle {
            color: #64748b;
            font-weight: 500;
            font-size: 0.9rem;
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