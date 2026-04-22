<?php

namespace App\Livewire\Production\Admin;

use App\Models\Setting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.production.admin')]
#[Title('Production Settings')]
class ProductionSettings extends Component
{
    public float $size_s_ton_per_1000 = 0.3;
    public float $size_m_ton_per_1000 = 0.5;
    public float $size_l_ton_per_1000 = 0.75;
    public bool $showSizeSettingsSection = false;

    public int $commission_threshold_items = 10000;
    public float $commission_rate_upto_threshold = 10;
    public float $commission_rate_after_threshold = 15;
    public bool $showCommissionSettingsSection = false;

    public float $rmb_to_lkr_rate = 92;
    public bool $showCurrencySettingsSection = false;

    public function mount(): void
    {
        $this->loadSettings();
    }

    public function loadSettings(): void
    {
        $settings = Setting::query()
            ->whereIn('key', [
                'production_size_factor_s',
                'production_size_factor_m',
                'production_size_factor_l',
                'production_commission_threshold_items',
                'production_commission_rate_upto_threshold',
                'production_commission_rate_after_threshold',
                'production_rmb_to_lkr_rate',
            ])
            ->pluck('value', 'key');

        $this->size_s_ton_per_1000 = (float) ($settings['production_size_factor_s'] ?? 0.3);
        $this->size_m_ton_per_1000 = (float) ($settings['production_size_factor_m'] ?? 0.5);
        $this->size_l_ton_per_1000 = (float) ($settings['production_size_factor_l'] ?? 0.75);

        $this->commission_threshold_items = (int) ($settings['production_commission_threshold_items'] ?? 10000);
        $this->commission_rate_upto_threshold = (float) ($settings['production_commission_rate_upto_threshold'] ?? 10);
        $this->commission_rate_after_threshold = (float) ($settings['production_commission_rate_after_threshold'] ?? 15);

        $this->rmb_to_lkr_rate = (float) ($settings['production_rmb_to_lkr_rate'] ?? 92);
    }

    public function saveSettings(): void
    {
        $this->validate([
            'size_s_ton_per_1000' => 'required|numeric|min:0.01',
            'size_m_ton_per_1000' => 'required|numeric|min:0.01',
            'size_l_ton_per_1000' => 'required|numeric|min:0.01',
            'commission_threshold_items' => 'required|integer|min:1',
            'commission_rate_upto_threshold' => 'required|numeric|min:0',
            'commission_rate_after_threshold' => 'required|numeric|min:0',
            'rmb_to_lkr_rate' => 'required|numeric|min:0.01',
        ]);

        $this->saveSettingKey('production_size_factor_s', $this->size_s_ton_per_1000, 'Ton consumed for 1000 cages (Size S)');
        $this->saveSettingKey('production_size_factor_m', $this->size_m_ton_per_1000, 'Ton consumed for 1000 cages (Size M)');
        $this->saveSettingKey('production_size_factor_l', $this->size_l_ton_per_1000, 'Ton consumed for 1000 cages (Size L)');
        $this->saveSettingKey('production_commission_threshold_items', (float) $this->commission_threshold_items, 'Commission threshold items');
        $this->saveSettingKey('production_commission_rate_upto_threshold', $this->commission_rate_upto_threshold, 'Commission rate per item up to threshold');
        $this->saveSettingKey('production_commission_rate_after_threshold', $this->commission_rate_after_threshold, 'Commission rate per item after threshold');
        $this->saveSettingKey('production_rmb_to_lkr_rate', $this->rmb_to_lkr_rate, 'Exchange rate for 1 RMB to LKR');

        $this->dispatch('alert', ['message' => 'Production settings saved successfully.', 'type' => 'success']);
    }

    private function saveSettingKey(string $key, float $value, string $description): void
    {
        Setting::updateOrCreate(
            ['key' => $key],
            [
                'value' => (string) $value,
                'status' => 'active',
                'description' => $description,
                'date' => now(),
            ]
        );
    }

    public function render()
    {
        return view('livewire.production.admin.production-settings');
    }
}
