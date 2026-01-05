<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;
    protected $fillable = ['client_id', 'bill_number', 'bill_date', 'gst_percent', 'cgst_percent', 'sgst_percent', 'total_tax_percent', 'discount', 'total_amount', 'due_date', 'status', 'bill_notes'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function items()
    {
        return $this->hasMany(BillItem::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'bill_id');
    }
}
