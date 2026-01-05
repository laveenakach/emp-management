<?php

namespace App\Http\Controllers;

use App\Models\EmployeeLeave;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EmployeeLeaveController extends Controller
{

    public function index()
    {
        if(Auth::user()->role === 'employer'){
            $leaves = EmployeeLeave::Join('departments', 'employee_leaves.department', '=', 'departments.id')
            ->with('user')->select('employee_leaves.*', 'departments.name as department_name')
            ->orderBy('id','desc')->get();
        }else{
            $leaves = EmployeeLeave::Join('departments', 'employee_leaves.department', '=', 'departments.id')
                ->with(['user' => function ($query){
                $query->whereNotIn('role', ['employer']);
            }])->where('user_id', Auth::id())->select('employee_leaves.*', 'departments.name as department_name')
            ->orderBy('id','desc')->get();
        }
        return view('employee.leave.index', compact('leaves'));
    }

    public function create()
    {
        $departments = Department::all();  // Get all departments
        return view('employee.leave.add', compact('departments'));
    }

    public function store(Request $request)
    {
        // print_r($request->all());
        // die;
        $validated = $request->validate([
            'employee_name' => 'required|string',
            'department' => 'required|string',
            //'leave_type' => 'required|array',
            'leave_type.*' => 'required|string|in:Vacation,Personal Reason,Illness,Family Care,Medical Appointment,Bereavement,Maternity Leave,Paternity Leave,Marriage Leave,Religious Holiday,Work from Home,Jury Duty,Quarantine,Training Leave',

            'reason' => 'nullable|string',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'leave_duration' => 'required',
            'date' => 'required|date',
            'document' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120', // max 5MB
        ]);

        // Handle document upload
        if ($request->hasFile('document')) {
            $document = $request->file('document');
            $documentName = time() . '_' . Str::random(10) . '.' . $document->getClientOriginalExtension();
            $document->move(public_path('uploads/leave_documents'), $documentName);
            $validated['document'] = $documentName;
        }

        $validated['leave_type'] = implode(', ', $validated['leave_type']);
        $validated['user_id'] = Auth::id();

        // print_r($validated);
        // die;
        EmployeeLeave::create($validated);

        return redirect()->route('employee.leaves.index')->with('status', 'Leave request submitted successfully.');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'approved_by' => 'nullable|string|max:255',
            'is_paid_leave' => 'required|in:Paid,Unpaid',
            'status' => 'required|in:Pending,Approved,Rejected',
        ]);

        $leave = EmployeeLeave::findOrFail($id);
        $leave->approved_by = $request->approved_by;
        $leave->is_paid_leave = $request->is_paid_leave;
        $leave->status = $request->status;
        $leave->save();

        return redirect()->back()->with('status', 'Leave status updated successfully.');
    }
}
