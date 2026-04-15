<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZkAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'emp_code',
        'punch_time',
        'punch_state',
        'verify_type',
        'terminal_sn',
        'terminal_alias',
        'area_alias',
        'temperature',
        'is_mask',
        'upload_time',
    ];

    protected $casts = [
        'punch_time'  => 'datetime',
        'upload_time' => 'datetime',
        'temperature' => 'decimal:1',
    ];

    /**
     * The employee who made this punch.
     */
    public function employee()
    {
        return $this->belongsTo(ZkEmployee::class, 'emp_code', 'emp_code');
    }

    /**
     * Human-readable punch state label.
     */
    public function getPunchStateLabelAttribute(): string
    {
        return match ((string) $this->punch_state) {
            '0' => 'Check-In',
            '1' => 'Check-Out',
            '2' => 'Break-Out',
            '3' => 'Break-In',
            '4' => 'OT-In',
            '5' => 'OT-Out',
            default => 'Unknown',
        };
    }

    /**
     * Human-readable verification method.
     */
    public function getVerifyTypeLabelAttribute(): string
    {
        return match ($this->verify_type) {
            1  => 'Fingerprint',
            2  => 'Card',
            3  => 'Password',
            4  => 'Face',
            15 => 'Palm',
            default => 'Other',
        };
    }
}
