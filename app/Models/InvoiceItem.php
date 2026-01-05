<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

     protected $fillable = [
        'invoice_id', 'description', 'qty', 'rate', 'amount'
    ];

    public function Candidate_invoices()
    {
        return $this->belongsTo(Candidate_invoices::class);
    }
}
