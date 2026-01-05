<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Candidate_invoices;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

require_once public_path('dompdf/autoload.inc.php');

class CandidateInvoiceController extends Controller
{

    // Show all invoices
    public function index()
    {
        $invoices = Candidate_invoices::with('candidate')->latest()->get();
        return view('candidates.invoices.index', compact('invoices'));
    }

    // Show create form
    public function create()
    {
        $candidates = Candidate::all();
        return view('candidates.invoices.create', compact('candidates'));
    }

    // Store invoice + items
    public function store(Request $request)
    {
        // echo "<pre>";
        // print_r($request->all());
        // die;
        $request->validate([
            'candidate_id'   => 'required|exists:candidates,id',
            'invoice_date'   => 'required|date',
            'due_date'       => 'nullable|date',
            'items.*.description' => 'required|string',
            'items.*.qty'    => 'required|integer|min:1',
            'items.*.rate'   => 'required|numeric|min:0',
        ]);

        // Generate Invoice No
        $lastId = Candidate_invoices::max('id') ?? 0;
        $year = date('Y'); // current year
        $invoiceNo = 'AT-' . $year . '-' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);

        // Save Candidate_invoices
        $Candidate_invoices = Candidate_invoices::create([
            'candidate_id' => $request->candidate_id,
            'invoice_no'   => $invoiceNo,
            'invoice_date' => $request->invoice_date,
            'due_date'     => $request->due_date,
            'discount'     => $request->discount ?? 0,
            'convenience_fees'     => $request->convenience_fees ?? 0,
            'gst_percent'  => $request->gst_percent ?? 0,
            'cgst_percent' => $request->cgst_percent ?? 0,
            'sgst_percent' => $request->sgst_percent ?? 0,
            'total_tax_percent' => $request->gst_percent + $request->cgst_percent + $request->sgst_percent ?? 0,
            'total_amount' => 0, // will update after items
            'status'       => $request->status,
        ]);

        $totalAmount = 0;

        // Save Items
        foreach ($request->items as $item) {
            $amount = $item['qty'] * $item['rate'];
            InvoiceItem::create([
                'invoice_id'  => $Candidate_invoices->id,
                'description' => $item['description'],
                'qty'         => $item['qty'],
                'rate'        => $item['rate'],
                'amount'      => $amount,
            ]);

            $totalAmount += $amount;
        }

        // Apply discount & tax 
        $discount = $totalAmount * ($Candidate_invoices->discount / 100);
        $convenience_fees_amount = $totalAmount * ($Candidate_invoices->convenience_fees / 100);
        $totalAmount = $totalAmount - $discount;

        $tax = $totalAmount * ($Candidate_invoices->total_tax_percent / 100);
        $finalAmount = $totalAmount + $tax + $convenience_fees_amount;

        $Candidate_invoices->update(['total_amount' => $finalAmount]);

        return redirect()->route('candidates.invoices.index')->with('status', 'Invoice Created Successfully!');
    }

    // Show single invoice
    public function show(Candidate_invoices $invoice)
    {
        $invoice->load('candidate', 'items');
        // echo "<pre>";
        // print_r($invoice);
        // die;
        return view('candidates.invoices.show', compact('invoice'));
    }

    // Download invoice as PDF
    public function downloadPdf(Candidate_invoices $invoice)
    {
        $invoice->load('candidate', 'items');

        $pdf = PDF::loadView('candidates.invoices.pdf', compact('invoice'));
        return $pdf->download($invoice->invoice_no . '.pdf');
    }

    public function edit($id)
    {
        $invoice = Candidate_invoices::with('items')->findOrFail($id);
        $candidates = Candidate::all();
        return view('candidates.invoices.create', compact('invoice', 'candidates'));
    }

    public function update(Request $request, $id)
    {

        $Candidate_invoices = Candidate_invoices::findOrFail($id);
        $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
            //'invoice_no' => 'required',
            'invoice_date' => 'required|date',
        ]);

        $Candidate_invoices->update($request->only([
            'candidate_id',
            'invoice_no',
            'invoice_date',
            'due_date',
            'discount',
            'convenience_fees',
            'gst_percent',
            'cgst_percent',
            'sgst_percent',
            'status'
        ]));

        // Delete old items
        $Candidate_invoices->items()->delete();

        $totalAmount = 0;

        // Save new items
        foreach ($request->items as $item) {
            $amount = $item['qty'] * $item['rate'];

            $Candidate_invoices->items()->create($item);

            $totalAmount += $amount;
        }

        // Apply discount & tax
        $discount = $totalAmount * ($Candidate_invoices->discount / 100);
        $convenience_fees_amount = $totalAmount * ($Candidate_invoices->convenience_fees / 100);
        $totalAmount = $totalAmount - $discount;

        $tax = $totalAmount * ($Candidate_invoices->total_tax_percent / 100);
        $finalAmount = $totalAmount + $tax + $convenience_fees_amount;

        $Candidate_invoices->update(['total_amount' => $finalAmount]);

        return redirect()->route('candidates.invoices.index')->with('success', 'Invoice updated successfully');
    }

    public function destroy($id)
    {
        $invoice = Candidate_invoices::with('items')->findOrFail($id);
        $invoice->items()->delete();

        $invoice->delete();

        return redirect()->route('candidates.invoices.index')->with('success', 'Invoice deleted successfully');
    }
}
