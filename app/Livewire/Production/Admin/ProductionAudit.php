<?php

namespace App\Livewire\Production\Admin;

use App\Models\BrandList;
use App\Models\CategoryList;
use App\Models\ProductBatch;
use App\Models\ProductDetail;
use App\Models\ProductPrice;
use App\Models\ProductStock;
use App\Models\ProductionBatchDay;
use App\Models\ProductionBatch;
use App\Models\ProductSupplier;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.production.admin')]
#[Title('Production Audit')]
class ProductionAudit extends Component
{
    public ?int $selectedBatchId = null;
    public array $transferQty = ['S' => 0, 'M' => 0, 'L' => 0];

    public function mount(): void
    {
        $firstBatch = $this->batches->first();
        $this->selectedBatchId = $firstBatch?->id;
        $this->syncTransferQtyWithAvailable();
    }

    public function updatedSelectedBatchId(): void
    {
        $this->resetErrorBag();
        $this->syncTransferQtyWithAvailable();
    }

    public function getBatchesProperty()
    {
        return ProductionBatch::query()
            ->with(['material', 'supervisor'])
            ->withSum('days as produced_total', 'produced_qty')
            ->withSum('days as produced_s_total', 'produced_s_qty')
            ->withSum('days as produced_m_total', 'produced_m_qty')
            ->withSum('days as produced_l_total', 'produced_l_qty')
            ->whereNotNull('supervisor_id')
            ->orderByDesc('id')
            ->get()
            ->filter(function ($batch) {
                return ((int) ($batch->produced_total ?? 0)) > 0;
            })
            ->values();
    }

    public function getSelectedBatchProperty(): ?ProductionBatch
    {
        if (!$this->selectedBatchId) {
            return null;
        }

        return $this->batches->firstWhere('id', (int) $this->selectedBatchId);
    }

    public function getSizeTotalsProperty(): array
    {
        if (!$this->selectedBatch) {
            return [
                'S' => 0,
                'M' => 0,
                'L' => 0,
                'total' => 0,
            ];
        }

        $s = (int) ($this->selectedBatch->produced_s_total ?? 0);
        $m = (int) ($this->selectedBatch->produced_m_total ?? 0);
        $l = (int) ($this->selectedBatch->produced_l_total ?? 0);
        $total = (int) ($this->selectedBatch->produced_total ?? 0);

        if (($s + $m + $l) === 0 && $total > 0) {
            $size = strtoupper((string) ($this->selectedBatch->size ?? ''));
            if (in_array($size, ['S', 'M', 'L'], true)) {
                ${strtolower($size)} = $total;
            }
        }

        return [
            'S' => $s,
            'M' => $m,
            'L' => $l,
            'total' => $total,
        ];
    }

    public function getTransferredTotalsProperty(): array
    {
        if (!$this->selectedBatch) {
            return ['S' => 0, 'M' => 0, 'L' => 0, 'total' => 0];
        }

        $s = (int) ($this->selectedBatch->transferred_s_qty ?? 0);
        $m = (int) ($this->selectedBatch->transferred_m_qty ?? 0);
        $l = (int) ($this->selectedBatch->transferred_l_qty ?? 0);

        return [
            'S' => $s,
            'M' => $m,
            'L' => $l,
            'total' => $s + $m + $l,
        ];
    }

    public function getAvailableTotalsProperty(): array
    {
        $produced = $this->sizeTotals;
        $transferred = $this->transferredTotals;

        return [
            'S' => max($produced['S'] - $transferred['S'], 0),
            'M' => max($produced['M'] - $transferred['M'], 0),
            'L' => max($produced['L'] - $transferred['L'], 0),
            'total' => max($produced['total'] - $transferred['total'], 0),
        ];
    }

    private function syncTransferQtyWithAvailable(): void
    {
        $this->transferQty = [
            'S' => 0,
            'M' => 0,
            'L' => 0,
        ];
    }

    public function transferToAudit(): void
    {
        $this->validate([
            'selectedBatchId' => 'required|exists:production_batches,id',
            'transferQty.S' => 'required|integer|min:0',
            'transferQty.M' => 'required|integer|min:0',
            'transferQty.L' => 'required|integer|min:0',
        ]);

        $createdVariants = DB::transaction(function () {
            $batch = ProductionBatch::query()
                ->with(['material'])
                ->lockForUpdate()
                ->findOrFail((int) $this->selectedBatchId);

            $totals = [
                'S' => (int) ProductionBatchDay::query()->where('production_batch_id', $batch->id)->sum('produced_s_qty'),
                'M' => (int) ProductionBatchDay::query()->where('production_batch_id', $batch->id)->sum('produced_m_qty'),
                'L' => (int) ProductionBatchDay::query()->where('production_batch_id', $batch->id)->sum('produced_l_qty'),
            ];

            $totalProduced = (int) ProductionBatchDay::query()->where('production_batch_id', $batch->id)->sum('produced_qty');
            if ($totalProduced <= 0) {
                $this->addError('selectedBatchId', 'This batch has no produced quantity to transfer.');
                return 0;
            }

            if (array_sum($totals) === 0) {
                $fallbackSize = strtoupper((string) ($batch->size ?? ''));
                if (in_array($fallbackSize, ['S', 'M', 'L'], true)) {
                    $totals[$fallbackSize] = $totalProduced;
                }
            }

            $alreadyTransferred = [
                'S' => (int) ($batch->transferred_s_qty ?? 0),
                'M' => (int) ($batch->transferred_m_qty ?? 0),
                'L' => (int) ($batch->transferred_l_qty ?? 0),
            ];

            $available = [
                'S' => max($totals['S'] - $alreadyTransferred['S'], 0),
                'M' => max($totals['M'] - $alreadyTransferred['M'], 0),
                'L' => max($totals['L'] - $alreadyTransferred['L'], 0),
            ];

            $requested = [
                'S' => (int) ($this->transferQty['S'] ?? 0),
                'M' => (int) ($this->transferQty['M'] ?? 0),
                'L' => (int) ($this->transferQty['L'] ?? 0),
            ];

            if (array_sum($requested) <= 0) {
                $this->addError('selectedBatchId', 'Enter transfer quantity for at least one size.');
                return 0;
            }

            foreach (['S', 'M', 'L'] as $size) {
                if ($requested[$size] > $available[$size]) {
                    $this->addError('selectedBatchId', 'Requested transfer exceeds available quantity for size ' . $size . '.');
                    return 0;
                }
            }

            $brandId = $this->resolveDefaultBrandId();
            $categoryId = $this->resolveDefaultCategoryId();
            $supplierId = $this->resolveDefaultSupplierId();

            $createdCount = 0;
            foreach ($requested as $size => $quantity) {
                $qty = (int) $quantity;
                if ($qty <= 0) {
                    continue;
                }

                $nameBase = trim((string) ($batch->material->name ?? 'Production Item'));
                $modelName = $batch->batch_code . '-' . $size;

                $product = ProductDetail::query()
                    ->where('source_production_batch_id', $batch->id)
                    ->where('production_size', $size)
                    ->first();

                if (!$product) {
                    $productCode = $this->buildUniqueProductCode('PB-' . $batch->id . '-' . $size);

                    $product = ProductDetail::create([
                        'code' => $productCode,
                        'name' => $nameBase . ' ' . $size,
                        'model' => $modelName,
                        'description' => 'Produced from batch ' . $batch->batch_code . ' (size ' . $size . ') via production audit transfer.',
                        'status' => 'active',
                        'brand_id' => $brandId,
                        'category_id' => $categoryId,
                        'supplier_id' => $supplierId,
                        'source_production_batch_id' => $batch->id,
                        'production_size' => $size,
                    ]);
                }

                ProductPrice::firstOrCreate(
                    ['product_id' => $product->id],
                    [
                        'supplier_price' => 0,
                        'selling_price' => 0,
                        'discount_price' => 0,
                    ]
                );

                $stock = ProductStock::firstOrCreate(
                    ['product_id' => $product->id],
                    [
                        'available_stock' => 0,
                        'damage_stock' => 0,
                        'total_stock' => 0,
                        'sold_count' => 0,
                        'restocked_quantity' => 0,
                    ]
                );

                $stock->available_stock = (int) $stock->available_stock + $qty;
                $stock->total_stock = (int) $stock->total_stock + $qty;
                $stock->restocked_quantity = (int) $stock->restocked_quantity + $qty;
                $stock->save();

                $preferredBatchNumber = $this->buildInventoryBatchNumber($batch->batch_code, $size);
                $inventoryBatch = ProductBatch::query()
                    ->where('product_id', $product->id)
                    ->where('batch_number', $preferredBatchNumber)
                    ->first();

                if (!$inventoryBatch) {
                    $inventoryBatch = ProductBatch::create([
                        'product_id' => $product->id,
                        'batch_number' => $this->resolveInventoryBatchNumberForProduct($product->id, $batch->batch_code, $size),
                        'purchase_order_id' => null,
                        'supplier_price' => 0,
                        'selling_price' => 0,
                        'quantity' => 0,
                        'remaining_quantity' => 0,
                        'received_date' => now()->toDateString(),
                        'status' => 'active',
                    ]);
                }

                $inventoryBatch->quantity = (int) $inventoryBatch->quantity + $qty;
                $inventoryBatch->remaining_quantity = (int) $inventoryBatch->remaining_quantity + $qty;
                $inventoryBatch->status = 'active';
                $inventoryBatch->save();

                $createdCount++;
            }

            if ($createdCount <= 0) {
                $this->addError('selectedBatchId', 'No size quantities were available to transfer.');
                return 0;
            }

            $batch->update([
                'transferred_s_qty' => (int) ($batch->transferred_s_qty ?? 0) + $requested['S'],
                'transferred_m_qty' => (int) ($batch->transferred_m_qty ?? 0) + $requested['M'],
                'transferred_l_qty' => (int) ($batch->transferred_l_qty ?? 0) + $requested['L'],
                'transferred_to_inventory_at' => now(),
                'transferred_to_inventory_by' => (int) Auth::id(),
            ]);

            return $createdCount;
        });

        if ($createdVariants <= 0 || $this->getErrorBag()->isNotEmpty()) {
            return;
        }

        $this->dispatch('alert', [
            'message' => 'Audit transfer completed. ' . array_sum($this->transferQty) . ' item(s) transferred across ' . $createdVariants . ' size variant(s).',
            'type' => 'success',
        ]);

        $this->selectedBatchId = $this->selectedBatchId;
        $this->syncTransferQtyWithAvailable();
    }

    private function resolveDefaultBrandId(): int
    {
        return (int) BrandList::firstOrCreate(
            ['brand_name' => 'Default Brand'],
            ['status' => 'active']
        )->id;
    }

    private function resolveDefaultCategoryId(): int
    {
        return (int) CategoryList::firstOrCreate(
            ['category_name' => 'Default Category'],
            ['status' => 'active']
        )->id;
    }

    private function resolveDefaultSupplierId(): int
    {
        return (int) ProductSupplier::firstOrCreate(
            ['name' => 'Default Supplier'],
            [
                'phone' => '0000000000',
                'email' => 'default@supplier.com',
                'address' => 'Default Address',
                'status' => 'active',
            ]
        )->id;
    }

    private function buildUniqueProductCode(string $base): string
    {
        $candidate = strtoupper(Str::slug($base, '-'));
        if ($candidate === '') {
            $candidate = 'PB-' . now()->format('YmdHis');
        }

        $counter = 1;
        $unique = $candidate;

        while (ProductDetail::where('code', $unique)->exists()) {
            $counter++;
            $unique = $candidate . '-' . $counter;
        }

        return $unique;
    }

    private function buildInventoryBatchNumber(string $batchCode, string $size): string
    {
        return 'PROD-' . strtoupper(Str::slug($batchCode, '')) . '-' . strtoupper($size);
    }

    private function resolveInventoryBatchNumberForProduct(int $productId, string $batchCode, string $size): string
    {
        $base = $this->buildInventoryBatchNumber($batchCode, $size);

        if (!ProductBatch::where('batch_number', $base)->where('product_id', '!=', $productId)->exists()) {
            return $base;
        }

        $unique = $base;
        $counter = 1;

        while (ProductBatch::where('batch_number', $unique)->exists()) {
            $counter++;
            $unique = $base . '-' . str_pad((string) $counter, 2, '0', STR_PAD_LEFT);
        }

        return $unique;
    }

    public function render()
    {
        return view('livewire.production.admin.production-audit', [
            'batches' => $this->batches,
            'selectedBatch' => $this->selectedBatch,
            'sizeTotals' => $this->sizeTotals,
            'transferredTotals' => $this->transferredTotals,
            'availableTotals' => $this->availableTotals,
        ]);
    }
}
