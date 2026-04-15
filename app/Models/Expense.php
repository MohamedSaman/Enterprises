<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'expense_type',
        'module',
        'production_batch_id',
        'amount',
        'date',
        'status',
        'description'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',

    ];

    public function productionBatch(): BelongsTo
    {
        return $this->belongsTo(ProductionBatch::class, 'production_batch_id');
    }
}
