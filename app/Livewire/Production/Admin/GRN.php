<?php

namespace App\Livewire\Production\Admin;

use App\Models\ProductionMaterial;
use App\Models\ProductionMaterialBatch;
use App\Models\PurchaseOrder as PO;
use App\Models\PurchaseOrderItem;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.production.admin')]
#[Title('Goods Receive Note')]
class GRN extends Component
{
    use WithPagination;

    public $searchPO = '';
    public $selectedPO = null;
    public $batch_no = '';
    public $grnItems = [];

    public function selectPO($poId)
    {
        $this->selectedPO = PO::with(['supplier', 'items.material'])->find($poId);
        $this->batch_no = 'BT' . date('Ymd') . '-' . sprintf('%04d', PO::count() + 1);
        $this->grnItems = [];

        foreach ($this->selectedPO->items as $item) {
            if ($item->status != 'received') {
                $this->grnItems[] = [
                    'id' => $item->id,
                    'material_id' => $item->production_material_id,
                    'name' => ($item->material->name ?? 'Unknown') . ' (' . ($item->material->code ?? 'N/A') . ')',
                    'size' => $item->size,
                    'ordered_qty' => $item->quantity,
                    'received_qty' => $item->quantity, // Default to full receipt
                    'cost_price' => $item->unit_price,
                    'selling_price' => $item->unit_price * 1.2, // Default markup
                    'received' => true
                ];
            }
        }
    }

    public function processGRN()
    {
        $this->validate([
            'batch_no' => 'required',
            'grnItems.*.received_qty' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $allReceived = true;
            foreach ($this->grnItems as $item) {
                if ($item['received'] && $item['received_qty'] > 0) {
                    // 1. Create Production Material Batch
                    ProductionMaterialBatch::create([
                        'production_material_id' => $item['material_id'],
                        'batch_no' => $this->batch_no,
                        'size' => $item['size'],
                        'quantity' => $item['received_qty'],
                        'remaining_quantity' => $item['received_qty'],
                        'cost_price' => $item['cost_price'],
                        'selling_price' => $item['selling_price'],
                        'supplier_id' => $this->selectedPO->supplier_id,
                        'purchase_order_id' => $this->selectedPO->id,
                    ]);

                    // 2. Update PO Item status
                    $poItem = PurchaseOrderItem::find($item['id']);
                    $poItem->received_quantity = ($poItem->received_quantity ?? 0) + $item['received_qty'];
                    if ($poItem->received_quantity >= $poItem->quantity) {
                        $poItem->status = 'received';
                    } else {
                        $poItem->status = 'partial';
                        $allReceived = false;
                    }
                    $poItem->save();
                } else {
                    $allReceived = false;
                }
            }

            // 3. Update PO status
            if ($allReceived) {
                $this->selectedPO->status = 'received';
            } else {
                $this->selectedPO->status = 'partial';
            }
            $this->selectedPO->received_date = now();
            $this->selectedPO->save();

            DB::commit();

            $this->dispatch('alert', ['message' => 'GRN processed and stock updated!', 'type' => 'success']);
            $this->selectedPO = null;
            $this->grnItems = [];

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', ['message' => 'Error: ' . $e->getMessage(), 'type' => 'error']);
        }
    }

    public function render()
    {
        $query = PO::where('order_type', 'production')
            ->whereIn('status', ['complete', 'partial'])
            ->with(['supplier']);

        if ($this->searchPO) {
            $query->where('order_code', 'like', "%{$this->searchPO}%");
        }

        $pendingPOs = $query->latest()->paginate(10);

        return view('livewire.production.admin.g-r-n', compact('pendingPOs'));
    }
}
