<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionBatchDay extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_batch_id',
        'day_no',
        'work_date',
        'produced_qty',
        'produced_s_qty',
        'produced_m_qty',
        'produced_l_qty',
        'expense_amount',
        'expense_note',
        'expense_items',
        'material_usages',
        'staff_commissions',
        'recorded_by',
    ];

    protected $casts = [
        'work_date' => 'date',
        'produced_s_qty' => 'integer',
        'produced_m_qty' => 'integer',
        'produced_l_qty' => 'integer',
        'expense_items' => 'array',
        'material_usages' => 'array',
        'staff_commissions' => 'array',
        'expense_amount' => 'decimal:2',
    ];

    public function batch()
    {
        return $this->belongsTo(ProductionBatch::class, 'production_batch_id');
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
