<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate_invoices extends Model
{
    use HasFactory;
     protected $fillable = [
        'candidate_id', 'invoice_no', 'invoice_date', 'due_date', 'discount', 'convenience_fees',
        'gst_percent', 'cgst_percent', 'sgst_percent',
        'total_tax_percent', 'total_amount', 'status'
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id');
    }
}
