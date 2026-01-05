<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalarySlip extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'file_path',
        'month',
        'total_present_days',
        'total_leave_days',
        'total_absent_days',
        'total_half_days',
        'basic_salary',
        'hra', 
        'conveyance', 
        'medical', 
        'special_allowance',
        'professionalTax', 
        'absentDeduction',
        'gross_salary',
        'deductions',
        'net_salary',
        'status'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
