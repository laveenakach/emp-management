<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeLeave extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'department',
        'leave_type',
        'reason',
        'date',
        'from_date',
        'to_date',
        'leave_duration',
        'approved_by',
        'is_paid_leave', // new field
        'status',
        'document'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
