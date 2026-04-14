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
        'start_date',
        'end_date',
        'target_qty',
        'completed_qty',
        'supervisor_id',
        'created_by',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

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
