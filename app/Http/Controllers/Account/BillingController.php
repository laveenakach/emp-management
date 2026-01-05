<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Quotation;
use App\Models\Bill;
use App\Models\BillItem;
use App\Models\QuotationItem;
use Barryvdh\DomPDF\Facade\Pdf; // <-- for barryvdh/laravel-dompdf v2+

require_once public_path('dompdf/autoload.inc.php');

class BillingController extends Controller
{

    public function index()
    {
        $bills = \App\Models\Bill::with('client')->latest()->paginate(10); // Adjust pagination as needed
        return view('accounts.billing.index', compact('bills'));
    }

    public function create()
    {
        $clients = \App\Models\Client::all(); // Assuming you have a clients table
        return view('accounts.billing.create', compact('clients'));
    }

    public function store(Request $request)
    {
        //dd($request->all());
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'bill_number' => 'required|unique:bills,bill_number',
            'bill_date' => 'required|date',
            'due_date' => 'nullable|date',
            //'tax_percent' => 'required|numeric|min:0',
            //'discount' => 'nullable|numeric|min:0',
            'status' => 'required|string',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.rate' => 'required|numeric|min:0',
        ]);

         $gst_percent = $request->has('gst') ? $request->input('gst') : 0;
        $cgst_percent = $request->has('cgst') ? $request->input('cgst') : 0;
        $sgst_percent = $request->has('sgst') ? $request->input('sgst') : 0;

        $total_tax_percent = $gst_percent + $cgst_percent + $sgst_percent;

        // Calculate totals
        $subtotal = 0;
        foreach ($request->items as $item) {
            $subtotal += $item['quantity'] * $item['rate'];
        }

        $taxAmount = ($subtotal * $total_tax_percent) / 100;
       // $discountAmount = $request->discount ?? 0;
        $totalAmount = $subtotal + $taxAmount;

        $bill = Bill::create([
            'client_id' => $request->client_id,
            'bill_number' => $request->bill_number,
            'bill_date' => $request->bill_date,
            'due_date' => $request->due_date,
            //'tax_percent' => $request->tax_percent,
            'gst_percent' => $request->has('gst') ? $request->input('gst') : 0,
            'cgst_percent' => $request->has('cgst') ? $request->input('cgst') : 0,
            'sgst_percent' => $request->has('sgst') ? $request->input('sgst') : 0,
            'total_tax_percent' => $total_tax_percent,
            //'discount' => $discountAmount,
            'total_amount' => $totalAmount,
            'status' => $request->status,
            'bill_notes' => $request->bill_notes
        ]);

        foreach ($request->items as $item) {
            BillItem::create([
                'bill_id' => $bill->id,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'rate' => $item['rate'],
                'amount' => $item['quantity'] * $item['rate'],
            ]);
        }

        return redirect()->route('billings.index')->with('success', 'Bill created successfully.');
    }

    public function edit($id)
    {
        $Bill = Bill::findOrFail($id);
        // print_r($Bill->gst_percent);
        // die;
        $clients = Client::all();
        // $url = route('quotations.update', $quotation);
        return view('accounts.billing.create', compact('Bill', 'clients'));
    }

    public function update(Request $request, Bill $bill)
    {

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'bill_number' => 'required|unique:bills,bill_number,' . $bill->id,
            'bill_date' => 'required|date',
            'due_date' => 'nullable|date',
            //'tax_percent' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'status' => 'required|string',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.rate' => 'required|numeric|min:0',
        ]);

        $bill->gst_percent = $request->has('gst') ? $request->input('gst') : 0;
        $bill->cgst_percent = $request->has('cgst') ? $request->input('cgst') : 0;
        $bill->sgst_percent = $request->has('sgst') ? $request->input('sgst') : 0;

        $bill->total_tax_percent = $bill->gst_percent + $bill->cgst_percent + $bill->sgst_percent;

        $subtotal = 0;
        foreach ($request->items as $item) {
            $subtotal += $item['quantity'] * $item['rate'];
        }

        $taxAmount = ($subtotal * $bill->total_tax_percent) / 100;
        $totalAmount = $subtotal + $taxAmount ;

        $bill->update([
            'client_id' => $request->client_id,
            'bill_number' => $request->bill_number,
            'bill_date' => $request->bill_date,
            'due_date' => $request->due_date,
            //'tax_percent' => $request->tax_percent,
            'gst_percent' => $request->has('gst') ? $request->input('gst') : 0,
            'cgst_percent' => $request->has('cgst') ? $request->input('cgst') : 0,
            'sgst_percent' => $request->has('sgst') ? $request->input('sgst') : 0,
            'total_tax_percent' => $bill->total_tax_percent,
            'total_amount' => $totalAmount,
            'status' => $request->status,
            'bill_notes' => $request->bill_notes
        ]);

        $bill->items()->delete();

        foreach ($request->items as $item) {
            $bill->items()->create([
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'rate' => $item['rate'],
                'amount' => $item['quantity'] * $item['rate'],
            ]);
        }

        return redirect()->route('billings.index')->with('success', 'Bill updated successfully.');
    }

    public function downloadBillPdf($id)
    {
        $bill = Bill::with('items', 'client')->findOrFail($id);

        //$Bill = Bill::findOrFail($id);

        $pdf = Pdf::loadView('accounts.billing.pdf', compact('bill'));

        return $pdf->download('bill_' . $bill->bill_number . '.pdf');
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }


    /**
     * Remove the specified resource from storage.
     */

    public function destroy(string $id)
    {
        // Find the Bill by its ID or fail
        $Bill = Bill::findOrFail($id);

        // Delete the associated BillItems
        $BillItems = BillItem::where('bill_id', $id)->get();

        // Delete each BillItem
        foreach ($BillItems as $BillItem) {
            $BillItem->delete();
        }
        // Delete the Bill record itself
        $Bill->delete();

        // Redirect back to the billing index with a success message
        return redirect()->route('billings.index')->with('success', 'Bill and its items deleted successfully.');
    }
}
