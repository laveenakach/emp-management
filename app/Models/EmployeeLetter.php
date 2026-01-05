<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeLetter extends Model
{
    use HasFactory;

     protected $fillable = [
        'employee_id', 'letter_type', 'file_path', 'description', 'uploaded_by',
    ];

    public function employee() {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function uploadedBy() {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
