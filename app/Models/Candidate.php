<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_id',
        'name',
        'email',
        'phone',
        'gst_number',
        'bank_account_number',
        'ifsc_code',
        'address'
    ];
}
