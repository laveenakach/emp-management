<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Mail\QuotationMail;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf; // <-- for barryvdh/laravel-dompdf v2+
use Mpdf\Mpdf;
use Dompdf\Dompdf;

require_once public_path('dompdf/autoload.inc.php');


class QuotationController extends Controller
{

    public function email($id)
    {
        $quotation = Quotation::with('client')->findOrFail($id);
        Mail::to($quotation->client->email)->send(new QuotationMail($quotation));

        return back()->with('success', 'Quotation emailed successfully.');
    }

    public function index()
    {
        //$Quotations = Quotation::latest()->paginate(10);
        $Quotations = Quotation::with('client')->latest()->paginate(10);

        return view('accounts.quotation.index', compact('Quotations'));
    }

    public function create()
    {
        $clients = Client::all();
        $url = route('quotations.store');
        return view('accounts.quotation.create', compact('clients', 'url'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required',
            'quotation_number' => 'required|string|max:255|unique:quotations,quotation_number',
            'quotation_date' => 'required|date',
            'items.*.service_name' => 'required',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.amount' => 'required|numeric|min:0',
        ]);

        // Calculate subtotal
        $subtotal = 0;
        foreach ($request->items as $item) {
            $subtotal += $item['amount'];
        }

        // Calculate GST @18%
        $gst = round($subtotal * 0.18, 2);

        // Calculate grand total
        $grandTotal = $subtotal + $gst;

        // Generate next quotation number
        // $latest = Quotation::latest()->first();
        // $nextNumber = 'Q-' . str_pad(optional($latest)->id + 1 ?? 1, 5, '0', STR_PAD_LEFT);
        // $validated['quotation_number'] = $nextNumber;

        // Create the quotation with totals
        $quotation = Quotation::create([
            'client_id' => $request->client_id,
            'quotation_number' => $validated['quotation_number'],
            'quotation_date' => $request->quotation_date,
            'notes' => $request->notes,
            'status' => $request->status,
            'subtotal' => $subtotal,
            'gst' => $gst,
            'grand_total' => $grandTotal,
        ]);

        // Create items
        foreach ($request->items as $item) {
            $quotation->items()->create([
                'service_name' => $item['service_name'],
                'description' => $item['description'] ?? '',
                'quantity' => $item['quantity'],
                'rate' => $item['rate'],
                'amount' => $item['amount'],
            ]);
        }

        return redirect()->route('quotations.index')->with('success', 'Quotation created successfully.');
    }

    public function edit($id)
    {
        $quotation = Quotation::findOrFail($id);
        $clients = Client::all();
        // $url = route('quotations.update', $quotation);
        return view('accounts.quotation.create', compact('quotation', 'clients'));
    }

    public function update(Request $request, Quotation $quotation)
    {
        $request->validate([
            'client_id' => 'required',
            'quotation_date' => 'required|date',
            'items.*.service_name' => 'required',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.amount' => 'required|numeric|min:0',
        ]);

        // Calculate subtotal
        $subtotal = 0;
        foreach ($request->items as $item) {
            $subtotal += $item['amount'];
        }

        // Calculate GST @18%
        $gst = round($subtotal * 0.18, 2);

        // Calculate grand total
        $grandTotal = $subtotal + $gst;

        // Update quotation
        $quotation->update([
            'client_id' => $request->client_id,
            'quotation_date' => $request->quotation_date,
            'notes' => $request->notes,
            'status' => $request->status,
            'subtotal' => $subtotal,
            'gst' => $gst,
            'grand_total' => $grandTotal,
        ]);

        // Delete old items
        QuotationItem::where('quotation_id', $quotation->id)->delete();

        // Add new items
        foreach ($request->items as $item) {
            QuotationItem::create([
                'quotation_id' => $quotation->id,
                'service_name' => $item['service_name'],
                'description' => $item['description'] ?? '',
                'quantity' => $item['quantity'],
                'rate' => $item['rate'],
                'amount' => $item['amount'],
            ]);
        }

        return redirect()->route('quotations.index')->with('success', 'Quotation updated successfully.');
    }



    public function downloadQuotationPdf($id)
    {
        $quotation = Quotation::with('items', 'client')->findOrFail($id);

        $pdf = Pdf::loadView('accounts.Quotation.pdf', compact('quotation'));

        return $pdf->download('quotation_' . $quotation->quotation_number . '.pdf');
    }


    public function destroy(string $id)
    {
        $quotation = Quotation::findOrFail($id);
        // Delete file_path file if exists
        if ($quotation->file_path && file_exists(public_path($quotation->file_path))) {
            unlink(public_path($quotation->file_path));
        }

        // Delete the quotation record
        $quotation->delete();

        return redirect()->route('quotations.index')->with('success', 'Quotation deleted successfully.');
    }
}
