<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZkEmployee extends Model
{
    use HasFactory;

    protected $fillable = [
        'emp_code',
        'first_name',
        'last_name',
        'department_id',
        'department_code',
        'department_name',
        'hire_date',
    ];

    public function attendances()
    {
        return $this->hasMany(ZkAttendance::class, 'emp_code', 'emp_code');
    }
}
