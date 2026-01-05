<?php

namespace App\Http\Controllers;

use App\Models\Attendances;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class EmployerAttendanceController extends Controller
{

    public function index()
    {

        //$attendances = Attendances::with('employee')->orderByDesc('id')->get();

        $user = Auth::user();

        if($user->role === 'employer'){
            $attendances = Attendances::orderByDesc('id')
                ->get();
        }else{
            $attendances = Attendances::where('employee_id',$user->id)
                ->orderByDesc('id')
                ->get();
        }
        return view('employer.attendance.index', compact('attendances'));
    }

    public function create()
    {
        $employees = User::whereNotIn('role', ['employer'])->get();
        $url = route('employer.attendance.store');

        return view('employer.attendance.create', compact('employees', 'url'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'employee_id' => ['required', 'exists:users,id'],
            'date' => ['required', 'date'],
            'check_in' => ['required', 'date_format:H:i'],
            'check_out' => ['nullable', 'date_format:H:i', 'after:check_in'],
        ]);
        // print_r($request->all());
        // die;
        Attendances::create($request->all());

        return redirect()->route('employer.attendance')->with('success', 'Attendance recorded successfully.');
    }

    // Show the form to edit an existing employee
    public function edit(User $employee, $id)
    {
        // print_r($id);
        //  echo "<pre>";
        //  print_r($employee);
        //  die;
        $url = route('employer.attendances.update', $id);

        $Attendances = Attendances::where('id', $id)->first();  // Get all departments
        $employees = User::where('role', 'employee')->get();

        return view('employer.attendance.create', compact('Attendances', 'employees', 'url'));
    }

    // Update an existing employee
    public function update(Request $request, $id)
    {
        // print_r($request->all());
        // die;

        $request->validate([
            'employee_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'check_in' => 'required|date_format:H:i',
            'check_out' => 'required|date_format:H:i|after:check_in',
        ]);

        // print_r($request->all());
        // die;

        $attendance = Attendances::findOrFail($id);
        $attendance->employee_id = $request->employee_id;
        $attendance->date = $request->date;
        $attendance->check_in = $request->check_in;
        $attendance->check_out = $request->check_out;
        $attendance->save();

        return redirect()->route('employer.attendance')->with('success', 'Employee updated successfully.');
    }

    public function delete($id)
    {
        $Attendances = Attendances::where('id', $id)->delete();  // Get all 

        return redirect()->route('employer.attendance')->with('success', 'Attendance delete successfully.');
    }
}
