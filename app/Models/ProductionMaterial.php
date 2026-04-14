<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'code',
        'material_type',
        'unit',
        'reorder_level'
    ];

    /**
     * Get the batches for the material.
     */
    public function batches()
    {
        return $this->hasMany(ProductionMaterialBatch::class, 'production_material_id');
    }

    /**
     * Get total stock across all batches and sizes.
     */
    public function getTotalStockAttribute()
    {
        return $this->batches()->sum('remaining_quantity');
    }

    /**
     * Get stock by size.
     */
    public function getStockBySize($size)
    {
        return $this->batches()->where('size', $size)->sum('remaining_quantity');
    }
}
