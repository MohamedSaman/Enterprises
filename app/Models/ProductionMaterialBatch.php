<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionMaterialBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_material_id',
        'batch_no',
        'size',
        'quantity',
        'remaining_quantity',
        'cost_price',
        'supplier_id',
        'purchase_order_id'
    ];

    /**
     * Get the material that owns the batch.
     */
    public function material()
    {
        return $this->belongsTo(ProductionMaterial::class, 'production_material_id');
    }

    /**
     * Get the supplier for the batch.
     */
    public function supplier()
    {
        return $this->belongsTo(ProductSupplier::class, 'supplier_id');
    }

    /**
     * Get the purchase order that created this batch.
     */
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    /**
     * Scope a query to only include active batches with stock.
     */
    public function scopeAvailable($query)
    {
        return $query->where('remaining_quantity', '>', 0);
    }
}
