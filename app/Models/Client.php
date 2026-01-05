<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    protected $fillable = ['CLTuniq_id','name', 'email', 'phone', 'address', 'gstin','bank_account','ifsc_code','project_requirement'];

}
