<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_code',
        'size',
        'production_material_id',
        'production_material_batch_id',
        'purchase_batch_no',
        'start_date',
        'end_date',
        'estimated_days',
        'target_qty',
        'planned_material_ton',
        'completed_qty',
        'transferred_s_qty',
        'transferred_m_qty',
        'transferred_l_qty',
        'supervisor_id',
        'created_by',
        'status',
        'transferred_to_inventory_at',
        'transferred_to_inventory_by',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'estimated_days' => 'integer',
        'planned_material_ton' => 'float',
        'transferred_s_qty' => 'integer',
        'transferred_m_qty' => 'integer',
        'transferred_l_qty' => 'integer',
        'transferred_to_inventory_at' => 'datetime',
    ];

    public function material()
    {
        return $this->belongsTo(ProductionMaterial::class, 'production_material_id');
    }

    public function materialBatch()
    {
        return $this->belongsTo(ProductionMaterialBatch::class, 'production_material_batch_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function staffMembers()
    {
        return $this->belongsToMany(User::class, 'production_batch_staff', 'production_batch_id', 'user_id')->withTimestamps();
    }

    public function days()
    {
        return $this->hasMany(ProductionBatchDay::class);
    }
}
