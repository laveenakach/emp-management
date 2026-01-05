<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use App\Models\Designation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;


class EmployerController extends Controller
{
    // Show all employees (for employer)
    public function index()
    {
        $employees = User::query()
            ->leftJoin('departments', 'users.department_id', '=', 'departments.id')
            ->where('users.role', 'employee')
            ->select('users.*', 'departments.name as department_name')
            ->orderBy('users.id', 'desc') // Use 'asc' for ascending, 'desc' for descending
            ->get();

        return view('employer.employees.index', compact('employees'));
    }

    public function getDesignations($department_id)
    {
        $designations = Designation::where('department_id', $department_id)->pluck('name', 'id');
        return response()->json($designations);
    }


    // Show the form to create a new employee
    public function create()
    {
        $departments = Department::all();  // Get all departments
        return view('employer.employees.create', compact('departments'));
    }

    // Store the newly created employee
    public function store(Request $request)
    {
        // print_r($request->all());
        // die;
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'mobile_no' => 'required|digits:10',
            'experience' => 'required|integer|min:0',
            'photo' => 'nullable|mimes:jpg,jpeg,png|max:5048', // max 5MB file size
            'city' => 'required|string|max:100',
            'location' => 'required|string|max:100',
            'address' => 'required|string|max:500',
            'aadhar_card' => 'required|digits:12',
            'pan_card' => 'required|string|size:10',
            //'bank_account' => 'required|string|max:20',
           // 'ifsc_code' => 'required|string|max:11',
           // 'designation_id' => 'required|string|max:100',
            'password' => 'nullable|confirmed|min:8', // optional password
            //'department_id' => 'required|exists:departments,id',
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->mobile_no = $request->mobile_no;
        $user->experience = $request->experience;
        $user->city = $request->city;
        $user->location = $request->location;
        $user->address = $request->address;
        $user->aadhar_card = $request->aadhar_card;
        $user->pan_card = $request->pan_card;
        $user->bank_account = $request->bank_account;
        $user->ifsc_code = $request->ifsc_code;
        $user->designation_id = $request->designation_id;
        $user->department = $request->department;
        $user->role = 'employee';

        // Generate and assign unique empuniq_id
        $lastId = User::max('id') ?? 0;
        $user->empuniq_id = 'EMP' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);

        // Photo Upload
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoName = time() . '_' . $photo->getClientOriginalName();
            $photo->move(public_path('uploads/profile_photos'), $photoName);
            $user->photo = $photoName;
        }
        // Password Update (if provided)
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return redirect()->route('employer.employees.index')->with('success', 'Employee created successfully.');
    }

    // Show the form to edit an existing employee
    public function edit(User $employee)
    {
        // echo "<pre>";
        // print_r($employee);
        // die;
        $departments = Department::all();  // Get all departments decryptString
        return view('employer.employees.edit', compact('employee', 'departments'));
    }

    public function softDelete($id)
    {
        $task = User::findOrFail($id);
        $task->delete();  // This will soft delete the task (requires SoftDeletes in model)

        return redirect()->route('employer.employees.index')->with('status', 'Task soft-deleted successfully.');
    }

    // Show trashed employees
    public function trashed()
    {
        $trashedemployees = User::query()
            ->leftJoin('departments', 'users.department_id', '=', 'departments.id')
            ->where('users.role', 'employee')
            ->select('users.*', 'departments.name as department_name')
            ->orderBy('users.id', 'desc') // Use 'asc' for ascending, 'desc' for descending
            ->onlyTrashed()->get();

        return view('employer.employees.trashed', compact('trashedemployees'));
    }

    // Restore
    public function restore($id)
    {
        $task = User::onlyTrashed()->findOrFail($id);
        $task->restore();
        return redirect()->route('employer.employees.index')->with('status', 'Employees restored successfully.');
    }

    // Force delete
    public function forceDelete($id)
    {
        $task = User::onlyTrashed()->findOrFail($id);
        $task->forceDelete();
        return redirect()->route('employer.employees.index')->with('status', 'Employees permanently deleted.');
    }

    // Update an existing employee
    public function update(Request $request, User $employee)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'mobile_no' => 'required|digits:10',
            'experience' => 'required|integer|min:0',
            'photo' => 'nullable|mimes:jpg,jpeg,png|max:5048', // max 5MB file size
            'city' => 'required|string|max:100',
            'location' => 'required|string|max:100',
            'address' => 'required|string|max:500',
            'aadhar_card' => 'required|digits:12',
            'pan_card' => 'required|string|size:10',
            //'bank_account' => 'required|string|max:20',
            //'ifsc_code' => 'required|string|max:11',
            //'designation_id' => 'required|string|max:100',
            'password' => 'nullable|confirmed|min:8', // optional password
            //'department_id' => 'required|exists:departments,id',
        ]);

        // $user = auth()->user();

        $user = User::where('id', $employee->id)->first();
        // print_r($user);
        // die;
        $user->name = $request->name;
        //$user->email = $request->email;
        $user->mobile_no = $request->mobile_no;
        $user->experience = $request->experience;
        $user->city = $request->city;
        $user->location = $request->location;
        $user->address = $request->address;
        $user->aadhar_card = $request->aadhar_card;
        $user->pan_card = $request->pan_card;
        $user->bank_account = $request->bank_account;
        $user->ifsc_code = $request->ifsc_code;
        $user->designation_id = $request->designation_id;
        $user->department = $request->department;
        $user->role = 'employee';

        // Photo Upload
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoName = time() . '_' . $photo->getClientOriginalName();
            $photo->move(public_path('uploads/profile_photos'), $photoName);
            $user->photo = $photoName;
        }
        // Password Update (if provided)
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return redirect()->route('employer.employees.index')->with('success', 'Employee updated successfully.');
    }

    public function show($id)
    {
        //$user = User::findOrFail($id);
        $user = User::with(['department', 'designation'])->findOrFail($id);

        return view('employer.employees.show', compact('user'));
    }
}
