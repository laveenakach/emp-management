<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;
    protected $fillable = ['client_id', 'quotation_number', 'quotation_date', 'status','subtotal','gst','grand_total', 'notes', 'file_path'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function items()
    {
        return $this->hasMany(QuotationItem::class, 'quotation_id');
    }
    
}
