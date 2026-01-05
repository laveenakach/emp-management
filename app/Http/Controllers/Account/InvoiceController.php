<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Quotation;
use App\Models\Bill;
use App\Models\Invoice;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

require_once public_path('dompdf/autoload.inc.php');


class InvoiceController extends Controller
{
    // public function index()
    // {
    //     $invoices = \App\Models\Invoice::with('client')->latest()->paginate(10); // Adjust pagination as needed
    //     return view('accounts.Invoices.index', compact('invoices'));
    // }

    // public function create()
    // {
    //     $clients = \App\Models\Client::all(); // Assuming you have a clients table
    //     $url = route('accounts.invoices.store');
    //     return view('accounts.Invoices.create', compact('url','clients'));
    // }


    public function index()
    {
        $invoices = Invoice::with('client')->latest()->paginate(10);
        return view('accounts.Invoices.index', compact('invoices'));
    }

    public function create()
    {
        $clients = Client::all();
        $quotations = Quotation::all();
        $url = route('accounts.invoices.store');
        return view('accounts.Invoices.create', compact('url', 'clients', 'quotations'));
    }

    public function getClientBills($client_id)
    {
        $bills = Bill::where('client_id', $client_id)->pluck('bill_number', 'id');
        return response()->json($bills);
    }

    public function getBillDetails($bill_id)
    {
        $bill = Bill::findOrFail($bill_id);
        return response()->json([
            'total_amount' => $bill->total_amount,
            'tax_percent' => $bill->total_tax_percent,
        ]);
    }

    public function getClientDetails($id)
    {
        $client = Client::findOrFail($id);
        $latestBill = Bill::where('client_id', $id)->latest('created_at')->first();

        return response()->json([
            'name' => $client->name,
            'email' => $client->email,
            'phone' => $client->phone,
            'address' => $client->address,
            'total_amount' => $latestBill->total_amount ?? null,
            'tax_percent' => $latestBill->total_tax_percent ?? null,
        ]);
    }


    public function store(Request $request)
    {
       
        $request->validate([
            'client_id' => 'required',
            'invoice_date' => 'required|date',
            'total_amount' => 'required|numeric',
        ]);
        $bill = Bill::where('client_id', $request->client_id)
            ->where('bill_number', $request->bill_number)
            ->first();

        $invoice = Invoice::create([
            'client_id' => $request->client_id,
            'bill_id' => $bill->id,
            'quotation_id' => $request->quotation_id,
            'bill_number' => $request->bill_number,
            'invoice_number' => 'INV-' . strtoupper(Str::random(6)),
            'invoice_date' => $request->invoice_date,
            'total_amount' => $request->total_amount,
            //'tax_percent' => $request->tax_percent ?? 0,
            'discount' => $request->discount ?? 0,
            'status' => $request->status,
        ]);

        return redirect()->route('invoices.index')->with('success', 'Invoice created successfully.');
    }

    public function download($id)
    {
        $invoice = Invoice::with(['client','quotation','bill','bill.items'])->findOrFail($id);

        $pdf = PDF::loadView('accounts.invoices.pdf', compact('invoice'));
        return $pdf->download($invoice->invoice_number . '.pdf');
    }

    public function edit($id)
    {
        $Invoice = Invoice::findOrFail($id);
        $clients = Client::all();
        $quotations = Quotation::all();
        $url = route('accounts.invoices.store');
        // $url = route('quotations.update', $quotation);
        return view('accounts.Invoices.create', compact('Invoice', 'clients', 'quotations'));
    }

    public function update(Request $request, Invoice $Invoice)
    {

        $request->validate([
            'client_id' => 'required',
            'invoice_date' => 'required|date',
            'total_amount' => 'required|numeric',
        ]);

        $bill = Bill::where('client_id', $request->client_id)
            ->where('bill_number', $request->bill_number)
            ->first();

        $Invoice->update([
            'client_id' => $request->client_id,
            'bill_id' => $bill->id,
            'quotation_id' => $request->quotation_id,
            'bill_number' => $request->bill_number,
            //'invoice_number' => 'INV-' . strtoupper(Str::random(6)),
            'invoice_date' => $request->invoice_date,
            'total_amount' => $request->total_amount,
            //'tax_percent' => $request->tax_percent ?? 0,
            'discount' => $request->discount ?? 0,
            'status' => $request->status,
        ]);

        return redirect()->route('invoices.index')->with('success', 'Invoice created successfully.');
    }

    public function destroy(string $id)
    {
        $Invoice = Invoice::findOrFail($id);
        $Invoice->delete();

        return redirect()->route('invoices.index')->with('success', 'Invoice deleted successfully.');
    }
}
