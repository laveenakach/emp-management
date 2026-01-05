<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmployeeLetter;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EmployeeLetterController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->role === 'employer'){
            $letters = EmployeeLetter::with('employee')->latest()->get();
        }else{
            $letters = EmployeeLetter::where('employee_id',$user->id)->with('employee')->latest()->get();
        }
        return view('employer.letters.index', compact('letters'));
    }

    public function create()
    {
        $employees = User::where('role', 'employee')->get();
        return view('employer.letters.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:users,id',
            'letter_type' => 'required|in:offer,appointment',
            'file' => 'required|mimes:pdf|max:2048',
            'description' => 'nullable|string'
        ]);

        // $filePath = $request->file('file')->store('letters', 'public');

        // Manual Upload
        if ($request->hasFile('file')) {
            $pdf = $request->file('file');
            $pdfName = time() . '_' . $pdf->getClientOriginalName();
            $pdf->move(public_path('uploads/letters'), $pdfName);
            $path = 'uploads/letters/' . $pdfName;
        }

        EmployeeLetter::create([
            'employee_id' => $request->employee_id,
            'letter_type' => $request->letter_type,
            'file_path' => $path,
            'description' => $request->description,
            'uploaded_by' => auth()->id()
        ]);

        return redirect()->route('letters.index')->with('success', 'Letter uploaded successfully.');
    }

    public function download(EmployeeLetter $letter)
    {
        return response()->download(public_path($letter->file_path));
    }

    public function edit(EmployeeLetter $letter)
    {
        $employees = User::where('role', 'employee')->get();
        return view('employer.letters.create', compact('letter', 'employees'));
    }

    public function update(Request $request, EmployeeLetter $letter)
    {
        $request->validate([
            'employee_id' => 'required|exists:users,id',
            'letter_type' => 'required|in:offer,appointment',
            'file' => 'nullable|mimes:pdf|max:2048',
            'description' => 'nullable|string'
        ]);

        $letter->employee_id = $request->employee_id;
        $letter->letter_type = $request->letter_type;
        $letter->description = $request->description;

        // if ($request->hasFile('file')) {
        //     // Delete old file
        //     Storage::disk('public')->delete($letter->file_path);
        //     // Upload new
        //     $letter->file_path = $request->file('file')->store('letters', 'public');
        // }

        if ($request->hasFile('file')) {
            $pdf = $request->file('file');
            $pdfName = time() . '_' . $pdf->getClientOriginalName();
            $pdf->move(public_path('uploads/letters'), $pdfName);
            $path = 'uploads/letters/' . $pdfName;
        }

        $letter->file_path = $path;

        $letter->save();

        return redirect()->route('letters.index')->with('success', 'Letter updated successfully.');
    }

    public function destroy(EmployeeLetter $letter)
    {
        Storage::disk('public')->delete($letter->file_path);
        $letter->delete();

        return redirect()->route('letters.index')->with('success', 'Letter deleted successfully.');
    }
}
