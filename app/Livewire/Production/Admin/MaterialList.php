<?php

namespace App\Livewire\Production\Admin;

use App\Models\ProductionMaterial as Material;
use App\Models\PurchaseOrderItem;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.production.admin')]
#[Title('Material List')]
class MaterialList extends Component
{
    use WithPagination;

    public string $search = '';

    // Modal & Form properties
    public $showModal = false;
    public $showStockModal = false;
    public $showDeleteModal = false;
    public $material_id = null;
    public $view_material_id = null;
    public $view_material_name = '';
    public $view_material_code = '';
    public $variantStocks = [];
    public $variantTotalStock = 0;
    public $delete_material_id = null;
    public $delete_material_name = '';
    public $deleteBlocked = false;
    public $deleteBlockMessage = '';
    public $name = '';
    public $code = '';
    public $description = '';
    public $material_type = 'Raw Material';

    public function mount()
    {
        $this->generateCode();
    }

    public function generateCode()
    {
        $lastMaterial = Material::latest()->first();
        if ($lastMaterial) {
            $num = (int) filter_var($lastMaterial->code, FILTER_SANITIZE_NUMBER_INT);
            $this->code = 'MAT-' . sprintf('%03d', $num + 1);
        } else {
            $this->code = 'MAT-001';
        }
    }

    public function openModal()
    {
        $this->resetForm();
        $this->generateCode();
        $this->showModal = true;
    }

    public function resetForm()
    {
        $this->name = '';
        $this->description = '';
        $this->material_type = 'Raw Material';
        $this->material_id = null;
    }

    public function openStockModal($id)
    {
        $material = Material::findOrFail($id);

        $this->view_material_id = $material->id;
        $this->view_material_name = $material->name;
        $this->view_material_code = $material->code;

        // Show variant-wise available stock from active batches.
        $grouped = $material->batches()
            ->where('remaining_quantity', '>', 0)
            ->select('size', DB::raw('SUM(remaining_quantity) as stock_qty'))
            ->groupBy('size')
            ->orderBy('size')
            ->get();

        $stockMap = [];
        foreach ($grouped as $row) {
            $size = strtoupper((string) ($row->size ?: 'N/A'));
            $stockMap[$size] = (float) $row->stock_qty;
        }

        // Keep S/M/L visible first, then append any extra variants.
        $orderedStocks = [];
        foreach (['S', 'M', 'L'] as $size) {
            $orderedStocks[] = [
                'size' => $size,
                'qty' => $stockMap[$size] ?? 0,
            ];
            unset($stockMap[$size]);
        }

        foreach ($stockMap as $size => $qty) {
            $orderedStocks[] = [
                'size' => $size,
                'qty' => $qty,
            ];
        }

        $this->variantStocks = $orderedStocks;
        $this->variantTotalStock = array_sum(array_column($orderedStocks, 'qty'));
        $this->showStockModal = true;
    }

    public function closeStockModal()
    {
        $this->showStockModal = false;
        $this->view_material_id = null;
        $this->view_material_name = '';
        $this->view_material_code = '';
        $this->variantStocks = [];
        $this->variantTotalStock = 0;
    }

    public function openDeleteModal($id)
    {
        $material = Material::find($id);
        if (!$material) {
            $this->dispatch('alert', ['message' => 'Material not found.', 'type' => 'error']);
            return;
        }

        $this->delete_material_id = $material->id;
        $this->delete_material_name = $material->name;

        $isUsedInPurchaseItems = PurchaseOrderItem::where('production_material_id', $id)->exists();
        $this->deleteBlocked = $isUsedInPurchaseItems;
        $this->deleteBlockMessage = $isUsedInPurchaseItems
            ? 'This material is used in purchase orders and cannot be deleted.'
            : '';

        $this->showDeleteModal = true;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->delete_material_id = null;
        $this->delete_material_name = '';
        $this->deleteBlocked = false;
        $this->deleteBlockMessage = '';
    }

    public function saveMaterial()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:production_materials,code,' . $this->material_id,
            'material_type' => 'required',
        ]);

        Material::updateOrCreate(
            ['id' => $this->material_id],
            [
                'name' => $this->name,
                'code' => $this->code,
                'description' => $this->description,
                'material_type' => $this->material_type,
            ]
        );

        $this->showModal = false;
        $this->dispatch('alert', ['message' => 'Material saved successfully!', 'type' => 'success']);
        $this->resetPage();
    }

    public function editMaterial($id)
    {
        $material = Material::findOrFail($id);
        $this->material_id = $material->id;
        $this->name = $material->name;
        $this->code = $material->code;
        $this->description = $material->description;
        $this->material_type = $material->material_type;

        $this->showModal = true;
    }

    public function deleteMaterial()
    {
        if (!$this->delete_material_id) {
            $this->dispatch('alert', ['message' => 'No material selected for deletion.', 'type' => 'error']);
            return;
        }

        $material = Material::find($this->delete_material_id);

        if (!$material) {
            $this->dispatch('alert', ['message' => 'Material not found.', 'type' => 'error']);
            $this->closeDeleteModal();
            return;
        }

        $isUsedInPurchaseItems = PurchaseOrderItem::where('production_material_id', $material->id)->exists();
        if ($isUsedInPurchaseItems) {
            $this->deleteBlocked = true;
            $this->deleteBlockMessage = 'This material is used in purchase orders and cannot be deleted.';
            return;
        }

        $material->delete();
        $this->closeDeleteModal();
        $this->dispatch('alert', ['message' => 'Material deleted successfully!', 'type' => 'success']);
        $this->resetPage();
    }

    public function render()
    {
        $query = Material::with(['batches' => function ($q) {
            $q->where('remaining_quantity', '>', 0);
        }]);

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('code', 'like', '%' . $this->search . '%');
        }

        $materials = $query->paginate(10);

        // Calculate summary stats values
        $totalMaterialsCount = Material::count();
        $totalStockSum = DB::table('production_material_batches')->sum('remaining_quantity');

        // Low stock count based on reorder_level
        $lowStockCount = Material::all()->filter(function ($m) {
            return $m->total_stock <= $m->reorder_level;
        })->count();

        $inventoryValueSum = DB::table('production_material_batches')
            ->selectRaw('SUM(remaining_quantity * cost_price) as total_value')
            ->first()->total_value ?? 0;

        // Structure stats for the view's grid
        $statsData = [
            ['label' => 'Total Materials', 'value' => $totalMaterialsCount, 'sub' => 'Types registered', 'color' => '#3b82f6', 'icon' => 'bi-layers'],
            ['label' => 'Total Stock', 'value' => number_format($totalStockSum), 'sub' => 'Units available', 'color' => '#10b981', 'icon' => 'bi-box-seam'],
            ['label' => 'Low Stock Alert', 'value' => $lowStockCount, 'sub' => 'Items below reorder level', 'color' => '#f43f5e', 'icon' => 'bi-exclamation-triangle'],
            ['label' => 'Inventory Value', 'value' => '$' . number_format($inventoryValueSum, 2), 'sub' => 'Total material worth', 'color' => '#f59e0b', 'icon' => 'bi-currency-dollar'],
        ];

        return view('livewire.production.admin.material-list', [
            'materials' => $materials,
            'stats' => $statsData
        ]);
    }
}
