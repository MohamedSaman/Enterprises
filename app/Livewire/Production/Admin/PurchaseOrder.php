<?php

namespace App\Livewire\Production\Admin;

use App\Models\ProductionMaterial;
use App\Models\ProductionMaterialBatch;
use App\Models\ProductSupplier;
use App\Models\PurchaseOrder as PO;
use App\Models\PurchaseOrderItem;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;

#[Layout('components.layouts.production.admin')]
#[Title('Purchase Materials')]
class PurchaseOrder extends Component
{
    use WithPagination;

    public $suppliers = [];
    public $materialResults = [];
    public $activeSearchIndex = null;

    public $showModal = false;
    public $showViewModal = false;
    public $material_id = null; // Added for edit functionality
    public $po_id = null; // Added for edit functionality
    public $supplier_id = '';
    public $order_date;
    public $items = [];

    public $selectedViewPO = null;

    // GRN properties
    public $selectedPO = null;
    public $grnItems = [];
    public $showGRNModal = false;

    protected $rules = [
        'supplier_id' => 'required',
        'order_date' => 'required|date',
        'items.*.material_id' => 'required',
        'items.*.quantity' => 'required|numeric|min:0.01',
        'items.*.unit_price' => 'required|numeric|min:0',
    ];

    public function mount()
    {
        $this->suppliers = ProductSupplier::all();
        $this->order_date = date('Y-m-d');
    }

    public function openModal()
    {
        $this->resetForm();
        $this->addItem();
        $this->showModal = true;
    }

    public function resetForm()
    {
        $this->po_id = null;
        $this->supplier_id = '';
        $this->order_date = date('Y-m-d');
        $this->items = [];
        $this->materialResults = [];
        $this->activeSearchIndex = null;
        $this->showViewModal = false;
        $this->selectedViewPO = null;
        $this->showGRNModal = false;
        $this->selectedPO = null;
        $this->grnItems = [];
    }

    public function addItem()
    {
        $this->items[] = [
            'material_id' => '',
            'name' => '',
            'size' => 'S',
            'quantity' => 1,
            'unit_price' => 0,
            'total' => 0
        ];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->activeSearchIndex = null;
    }

    public function updated($property)
    {
        if (str_contains($property, 'items.')) {
            foreach ($this->items as $index => $item) {
                $this->items[$index]['total'] = (float)($item['quantity'] ?? 0) * (float)($item['unit_price'] ?? 0);
            }
        }
    }

    public function performSearchMaterial($index, $value)
    {
        $this->activeSearchIndex = $index;

        if (strlen($value) < 2) {
            $this->materialResults = [];
            return;
        }

        $this->materialResults = ProductionMaterial::where('name', 'like', "%{$value}%")
            ->orWhere('code', 'like', "%{$value}%")
            ->limit(10)
            ->get()
            ->toArray();
    }

    public function selectMaterial($index, $materialId)
    {
        $material = ProductionMaterial::find($materialId);
        if ($material) {
            $this->items[$index]['material_id'] = $material->id;
            $this->items[$index]['name'] = $material->name . ' (' . $material->code . ')';
            $this->materialResults = [];
            $this->activeSearchIndex = null;
        }
    }

    public function viewOrder($id)
    {
        $this->selectedViewPO = PO::with(['supplier', 'items.material', 'batches.material'])->find($id);

        if (!$this->selectedViewPO) {
            $this->dispatch('alert', ['message' => 'Purchase order not found.', 'type' => 'error']);
            return;
        }

        $this->showViewModal = true;
    }

    public function closeViewModal()
    {
        $this->showViewModal = false;
        $this->selectedViewPO = null;
    }

    public function getLastPurchasePrice($materialId)
    {
        $lastPurchasePrice = PurchaseOrderItem::whereHas('order', function ($query) {
            $query->where('status', '!=', 'pending');
        })
            ->where('production_material_id', $materialId)
            ->where('order_id', '!=', $this->selectedViewPO->id)
            ->orderBy('created_at', 'desc')
            ->first();

        return $lastPurchasePrice ? $lastPurchasePrice->unit_price : null;
    }

    public function save()
    {
        // Filter out empty rows before validation
        $this->items = array_values(array_filter($this->items, function ($item) {
            return !empty($item['material_id']);
        }));

        if (count($this->items) === 0) {
            $this->dispatch('alert', ['message' => 'Please select at least one material item.', 'type' => 'error']);
            return;
        }

        $this->validate();

        try {
            DB::beginTransaction();

            $total_amount = collect($this->items)->sum('total');
            $order_code = $this->po_id ? PO::find($this->po_id)->order_code : 'PO-PROD-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(2)));

            $po = PO::updateOrCreate(
                ['id' => $this->po_id],
                [
                    'order_code' => $order_code,
                    'supplier_id' => $this->supplier_id,
                    'order_type' => 'production',
                    'order_date' => $this->order_date,
                    'status' => 'pending',
                    'total_amount' => $total_amount,
                    'due_amount' => $total_amount,
                    'discount_amount' => 0,
                ]
            );

            // Clear existing items if editing
            if ($this->po_id) {
                PurchaseOrderItem::where('order_id', $po->id)->delete();
            }

            foreach ($this->items as $index => $item) {
                PurchaseOrderItem::create([
                    'order_id' => $po->id,
                    'production_material_id' => $item['material_id'],
                    'size' => $item['size'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount' => 0,
                    'status' => 'pending',
                ]);
            }

            DB::commit();

            $this->dispatch('alert', ['message' => 'Purchase Order ' . ($this->po_id ? 'updated' : 'created') . ' successfully!', 'type' => 'success']);
            $this->showModal = false;
            $this->resetForm();
            $this->resetPage();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', ['message' => 'Error: ' . $e->getMessage(), 'type' => 'error']);
        }
    }

    public function editOrder($id)
    {
        $this->showViewModal = false;
        $this->selectedViewPO = null;
        $this->resetForm();
        $this->po_id = $id;
        $po = PO::with('items.material')->findOrFail($id);
        $this->supplier_id = $po->supplier_id;
        $this->order_date = $po->order_date;

        foreach ($po->items as $item) {
            $this->items[] = [
                'material_id' => $item->production_material_id,
                'name' => ($item->material->name ?? 'Unknown') . ' (' . ($item->material->code ?? 'N/A') . ')',
                'size' => $item->size,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'total' => $item->quantity * $item->unit_price
            ];
        }
        $this->showModal = true;
    }

    public function deleteOrder($id)
    {
        try {
            $this->showViewModal = false;
            $this->selectedViewPO = null;
            DB::beginTransaction();
            $po = PO::findOrFail($id);
            PurchaseOrderItem::where('order_id', $po->id)->delete();
            ProductionMaterialBatch::where('purchase_order_id', $po->id)->delete();
            $po->delete();
            DB::commit();
            $this->dispatch('alert', ['message' => 'Order deleted successfully!', 'type' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', ['message' => 'Error deleting order: ' . $e->getMessage(), 'type' => 'error']);
        }
    }

    public function completeOrder($id)
    {
        $po = PO::findOrFail($id);
        $po->status = 'complete';
        $po->save();
        $this->showViewModal = false;
        $this->selectedViewPO = null;
        $this->dispatch('alert', ['message' => 'Order marked as complete!', 'type' => 'success']);
    }

    public function openGRNModal($id)
    {
        $this->showViewModal = false;
        $this->selectedViewPO = null;
        $this->selectedPO = PO::with(['supplier', 'items.material'])->find($id);
        if (!$this->selectedPO) {
            $this->dispatch('alert', ['message' => 'Purchase order not found.', 'type' => 'error']);
            return;
        }

        $this->grnItems = [];

        foreach ($this->selectedPO->items as $item) {
            $alreadyReceived = (float) ($item->received_quantity ?? 0);
            $remainingQty = max(0, (float) $item->quantity - $alreadyReceived);

            if ($remainingQty > 0) {
                $this->grnItems[] = [
                    'id' => $item->id,
                    'material_id' => $item->production_material_id,
                    'name' => ($item->material->name ?? 'Unknown') . ' (' . ($item->material->code ?? 'N/A') . ')',
                    'size' => $item->size,
                    'ordered_qty' => $item->quantity,
                    'already_received_qty' => $alreadyReceived,
                    'remaining_qty' => $remainingQty,
                    'received_qty' => $remainingQty,
                    'cost_price' => $item->unit_price,
                    'received' => true
                ];
            }
        }

        if (count($this->grnItems) === 0) {
            $this->dispatch('alert', ['message' => 'All items are already fully received for this order.', 'type' => 'info']);
            return;
        }

        $this->showGRNModal = true;
    }

    public function processGRN()
    {
        if (!$this->selectedPO) {
            $this->dispatch('alert', ['message' => 'No purchase order selected for GRN.', 'type' => 'error']);
            return;
        }

        if (count($this->grnItems) === 0) {
            $this->dispatch('alert', ['message' => 'No GRN items to process.', 'type' => 'error']);
            return;
        }

        try {
            DB::beginTransaction();

            $hasAnyReceipt = false;
            $batchCounterBySize = []; // Track batch number counter per size variant

            foreach ($this->grnItems as $item) {
                $poItem = PurchaseOrderItem::find($item['id']);
                if (!$poItem) {
                    continue;
                }

                $remainingToReceive = max(0, (float) $poItem->quantity - (float) ($poItem->received_quantity ?? 0));
                $receivedQty = (float) ($item['received_qty'] ?? 0);
                $costPrice = (float) ($item['cost_price'] ?? 0);

                if ($costPrice < 0) {
                    throw new \Exception('Cost price cannot be negative for ' . ($item['name'] ?? 'an item') . '.');
                }

                if (!($item['received'] ?? false)) {
                    if ($remainingToReceive > 0 && ($poItem->status ?? '') === 'received') {
                        $poItem->status = 'partial';
                        $poItem->save();
                    }
                    continue;
                }

                if ($receivedQty <= 0) {
                    continue;
                }

                if ($receivedQty > $remainingToReceive) {
                    throw new \Exception('Received quantity cannot exceed remaining quantity for ' . ($item['name'] ?? 'an item') . '.');
                }

                $hasAnyReceipt = true;

                // Keep PO item price aligned with GRN cost price changes.
                $poItem->unit_price = $costPrice;

                if ($receivedQty > 0) {
                    // Generate batch number with size variant: BT{date}{variant}-{counter}
                    $size = $item['size'] ?? 'X'; // Fallback to X if size is empty
                    if (!isset($batchCounterBySize[$size])) {
                        // Get next counter for this size variant
                        $lastBatch = ProductionMaterialBatch::where('batch_no', 'like', 'BT' . date('Ymd') . $size . '-%')
                            ->orderBy('id', 'desc')
                            ->first();
                        $batchCounterBySize[$size] = $lastBatch ? (int) substr($lastBatch->batch_no, -4) + 1 : 1;
                    } else {
                        $batchCounterBySize[$size]++;
                    }

                    $batchNo = 'BT' . date('Ymd') . $size . '-' . sprintf('%04d', $batchCounterBySize[$size]);

                    ProductionMaterialBatch::create([
                        'production_material_id' => $item['material_id'],
                        'batch_no' => $batchNo,
                        'size' => $item['size'],
                        'quantity' => $receivedQty,
                        'remaining_quantity' => $receivedQty,
                        'cost_price' => $costPrice,
                        'supplier_id' => $this->selectedPO->supplier_id,
                        'purchase_order_id' => $this->selectedPO->id,
                    ]);
                }

                $poItem->received_quantity = (float) ($poItem->received_quantity ?? 0) + $receivedQty;
                $poItem->status = $poItem->received_quantity >= (float) $poItem->quantity ? 'received' : 'partial';
                $poItem->save();

                // If GRN changed the price, align existing batches for the same PO/material/size.
                ProductionMaterialBatch::where('purchase_order_id', $this->selectedPO->id)
                    ->where('production_material_id', $item['material_id'])
                    ->where('size', $item['size'])
                    ->update(['cost_price' => $costPrice]);
            }

            if (!$hasAnyReceipt) {
                throw new \Exception('Please receive at least one item to process GRN.');
            }

            $poItems = PurchaseOrderItem::where('order_id', $this->selectedPO->id)->get();
            $allReceived = $poItems->every(function ($poItem) {
                return (float) ($poItem->received_quantity ?? 0) >= (float) $poItem->quantity;
            });

            $orderTotal = $poItems->sum(function ($poItem) {
                return (float) $poItem->quantity * (float) ($poItem->unit_price ?? 0);
            });

            $this->selectedPO->status = $allReceived ? 'received' : 'partial';
            $this->selectedPO->received_date = now();
            $this->selectedPO->total_amount = $orderTotal;
            $this->selectedPO->due_amount = $orderTotal;
            $this->selectedPO->save();

            DB::commit();

            $this->dispatch('alert', ['message' => 'GRN processed successfully!', 'type' => 'success']);
            $this->resetForm();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', ['message' => 'Error: ' . $e->getMessage(), 'type' => 'error']);
        }
    }

    public function render()
    {
        $purchaseOrders = PO::where('order_type', 'production')
            ->with(['supplier', 'items.material', 'batches.material'])
            ->latest()
            ->paginate(15);

        return view('livewire.production.admin.purchase-order', compact('purchaseOrders'));
    }
}
