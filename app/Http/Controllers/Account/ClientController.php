<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use Illuminate\Support\Str;


class ClientController extends Controller
{
    // Show all clients
    public function index()
    {
        $clients = Client::latest()->paginate(10);
        return view('accounts.clients.index', compact('clients'));
    }

    // Show create form
    public function create()
    {
        $url = route('accounts.clients.store');
        return view('accounts.clients.create', compact('url'));
    }


    // Store client
    public function store(Request $request, Client $client)
    {
        // echo "<pre>";
        // print_r($request->all());
        // die;
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email' . ($client->id ?? '' ? ',' . $client->id : ''),
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            // GSTIN: 15 characters - 11 digits + 1 letter + Z + 1 letter (e.g., 22AAAAA0000A1Z5)
            'gst_number' => [
                'nullable',
                'string',
                'max:15',
                'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/',
            ],
            // Bank account: 9 to 18 digits (India)
            'bank_account' => [
                'nullable',
                'string',
                'regex:/^[0-9]{9,18}$/',
            ],
            // IFSC code: 4 letters + 0 + 6 digits (e.g., SBIN0001234)
            'ifsc_code' => [
                'nullable',
                'string',
                'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/',
            ],
            'project_requirement' => 'nullable|mimes:pdf|max:5120',
        ]);

        $data = $request->except('project_requirement'); // Get all fields except the file

        if ($request->hasFile('project_requirement')) {
            $pdf = $request->file('project_requirement');
            $pdfName = time() . '_' . $pdf->getClientOriginalName();
            $pdf->move(public_path('uploads/project_requirement'), $pdfName);
            $data['project_requirement'] = 'uploads/project_requirement/' . $pdfName;
        }

        $data['CLTuniq_id'] = 'CLT-' . strtoupper(Str::random(6));

        Client::create($data);

        return redirect()->route('accounts.clients.index')->with('success', 'Client created successfully.');
    }

    // Show edit form
    public function edit(Client $client)
    {
        // echo "<pre>";
        // print_r($client);
        // die;
        $url = route('accounts.clients.update', $client);
        return view('accounts.clients.create', compact('client', 'url'));
    }

    // Update client
    public function update(Request $request, Client $client)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email,' . $client->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'gstin' => [
                'nullable',
                'string',
                'max:15',
                'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/',
            ],
            'bank_account' => [
                'nullable',
                'string',
                'regex:/^[0-9]{9,18}$/',
            ],
            'ifsc_code' => [
                'nullable',
                'string',
                'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/',
            ],
            'project_requirement' => 'nullable|mimes:pdf|max:5120',
        ]);

        // Get all fields except the file
        $data = $request->except('project_requirement');

        if ($request->hasFile('project_requirement')) {
            // Delete old file if exists
            if ($client->project_requirement && file_exists(public_path($client->project_requirement))) {
                unlink(public_path($client->project_requirement));
            }

            $pdf = $request->file('project_requirement');
            $pdfName = time() . '_' . $pdf->getClientOriginalName();
            $pdf->move(public_path('uploads/project_requirement'), $pdfName);
            $data['project_requirement'] = 'uploads/project_requirement/' . $pdfName;
        }

        $client->update($data);

        return redirect()->route('accounts.clients.index')->with('success', 'Client updated successfully.');
    }

    public function show($id)
    {
        $client = Client::findOrFail($id);
        return view('accounts.clients.show', compact('client'));
    }

    // Delete client
    public function destroy(Client $client)
    {
        // Delete project_requirement file if exists
        if ($client->project_requirement && file_exists(public_path($client->project_requirement))) {
            unlink(public_path($client->project_requirement));
        }

        // Delete the client record
        $client->delete();

        return redirect()->route('accounts.clients.index')->with('success', 'Client deleted successfully.');
    }
}
