<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Candidate;
use Illuminate\Support\Str;

class CandidateController extends Controller
{
    
    /**
     * Display a listing of candidates.
     */
    public function index()
    {
        $candidates = Candidate::latest()->paginate(10);
        return view('candidates.index', compact('candidates'));
    }

    /**
     * Show the form for creating a new candidate.
     */
    public function create()
    {
        $url = route('candidates.store');
        return view('candidates.create', compact('url'));
    }

    /**
     * Store a newly created candidate in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'                => 'required|string|max:255',
           // 'email'               => 'required|email|unique:candidates,email',
            'phone'               => 'required|string|max:20',
            'gst_number'          => 'nullable|string|max:50',
            'bank_account_number' => 'nullable|string|max:50',
            'ifsc_code'           => 'nullable|string|max:20',
            'address'             => 'nullable|string|max:500',
        ]);

        $request['candidate_id'] = 'CDT-' . strtoupper(Str::random(6));

        Candidate::create($request->all());

        return redirect()->route('candidates.index')->with('success', 'Candidate created successfully.');
    }

    /**
     * Show the form for editing the specified .
     */
    public function edit(Candidate $candidate)
    {
        $url = route('candidates.update', $candidate->id);
        return view('candidates.create', compact('candidate', 'url'));
    }

    /**
     * Update the specified candidate in storage.
     */
    public function update(Request $request, Candidate $candidate)
    {
        $request->validate([
            'name'                => 'required|string|max:255',
            //'email'               => 'required|email|unique:candidates,email,' . $candidate->id,
            'phone'               => 'required|string|max:20',
            'gst_number'          => 'nullable|string|max:50',
            'bank_account_number' => 'nullable|string|max:50',
            'ifsc_code'           => 'nullable|string|max:20',
            'address'             => 'nullable|string|max:500',
        ]);

        //  $request['candidate_id'] = 'CDT-' . strtoupper(Str::random(6));

        $candidate->update($request->all());

        return redirect()->route('candidates.index')->with('success', 'Candidate updated successfully.');
    }

    public function show($id)
    {
        $candidate = Candidate::findOrFail($id);
        return view('candidates.show', compact('candidate'));
    }

    /**
     * Remove the specified candidate from storage. 
     */
    public function destroy(Candidate $candidate)
    {
        $candidate->delete();

        return redirect()->route('candidates.index')->with('success', 'Candidate deleted successfully.');
    }

}
