<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MonthlySalary extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'year',
        'month',
        'working_days',
        'attendance_days',
        'paid_leave_days',
        'basic_salary',
        'attendance_bonus',
        'commission',
        'overtime_hours',
        'overtime_amount',
        'gross_salary',
        'epf_employee',
        'epf_employer',
        'etf',
        'other_deductions',
        'net_salary',
        'include_epf_etf',
        'status',
    ];

    protected $casts = [
        'working_days' => 'integer',
        'attendance_days' => 'integer',
        'paid_leave_days' => 'integer',
        'basic_salary' => 'decimal:2',
        'attendance_bonus' => 'decimal:2',
        'commission' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'overtime_amount' => 'decimal:2',
        'gross_salary' => 'decimal:2',
        'epf_employee' => 'decimal:2',
        'epf_employer' => 'decimal:2',
        'etf' => 'decimal:2',
        'other_deductions' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'include_epf_etf' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeByMonth($query, int $month, int $year)
    {
        return $query->where('month', $month)->where('year', $year);
    }

    public function scopeGenerated($query)
    {
        return $query->whereIn('status', ['generated', 'approved', 'paid']);
    }
}
