<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_id',
        'service_name',
        'description',
        'quantity',
        'rate',
        'amount',
    ];

    // public function quotation()
    // {
    //     return $this->belongsTo(Quotation::class);
    // }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class, 'quotation_id');
    }
}
