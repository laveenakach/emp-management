<?php

namespace App\Http\Controllers;

use App\Models\Attendances;
use App\Models\EmployeeLeave;
use App\Models\SalarySlip;
use App\Models\User;
use App\Models\Task;
use App\Models\Notification;
use App\Models\Client;
use App\Models\Bill;
use App\Models\Quotation;
use App\Models\Invoice;
use App\Models\EmployeeLetter;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{

    public function index()
    {
        $user = Auth::user();

        return view('dashboard.employee', [
            'myAttendances' => Attendances::where('employee_id', $user->id)->count(),
            'Leavesrequest' => EmployeeLeave::where('user_id', $user->id)->count(),
            'salarySlips' => SalarySlip::where('employee_id', $user->id)->count(),

            // ✅ TASKS ASSIGNED TO EMPLOYEE
            'taskManagement' => Task::whereHas('users', function ($q) use ($user) {
                    $q->where('users.id', $user->id);
                })
                ->count(),

            // ✅ SUBMITTED TASKS ASSIGNED TO EMPLOYEE
            'taskSubmited' => Task::where('status', 'Submitted')
                ->whereHas('users', function ($q) use ($user) {
                    $q->where('users.id', $user->id);
                })
                ->count(),

            'letters' => EmployeeLetter::where('employee_id', $user->id)->count(),

            // (Optional improvement below 👇)
            'notifications' => Notification::orderBy('id', 'desc')->paginate(3),
        ]);
    }

    public function employerindex()
    {
        $user = Auth::user();

        return view('dashboard.employer', [
            'employeecount' => User::where('role', 'employee')->count(),
            'myAttendances' => Attendances::count(),
            'Leavesrequest' => EmployeeLeave::count(),
            'salarySlips' => SalarySlip::count(),
            'Bill' => Bill::count(),
            'clients' => Client::count(),
            'Quotation' => Quotation::count(),
            'Invoice' => Invoice::count(),

            // ✅ FIXED TASK COUNT
            'taskManagement' => Task::where(function ($query) use ($user) {
                    $query->where('created_by', $user->id)
                        ->orWhereHas('users', function ($q) use ($user) {
                            $q->where('users.id', $user->id);
                        });
                })
                ->count(),
        ]);
    }
}
