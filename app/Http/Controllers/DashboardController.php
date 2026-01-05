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
        // print_r('hhh');
        // die;
        return view('dashboard.employee', [
            'myAttendances' => Attendances::where('employee_id', Auth::user()->id)->count(),
            'Leavesrequest' => EmployeeLeave::where('user_id', Auth::user()->id)->count(),
            'salarySlips' => SalarySlip::where('employee_id', Auth::user()->id)->count(),
            $user = Auth::user(),
            'taskManagement' => Task::whereJsonContains('assigned_to', (string) $user->id)
                ->orderByDesc('id')
                ->count(),

            'taskSubmited' => Task::Where('status', 'Submitted')
                ->whereJsonContains('assigned_to', (string) $user->id)
                ->orderByDesc('id')
                ->count(),

            'letters' => EmployeeLetter::where('employee_id',$user->id)->with('employee')->count(), 

            'notifications' => Notification::orderBy('id', 'desc')->paginate(3), // Fetch all notifications
        ]);
    }

    public function employerindex()
    {
        $myAttendances = Attendances::where('employee_id', Auth::user()->id)->count();
        // print_r($myAttendances);
        // die;
        return view('dashboard.employer', [
            'employeecount' => User::where('role', 'employee')->count(),
            'myAttendances' => Attendances::count(),
            'Leavesrequest' => EmployeeLeave::count(),
            'salarySlips' => SalarySlip::count(),
            'Bill' => Bill::count(),
            'clients' => Client::count(),
            'Quotation' => Quotation::count(),
            'Invoice' => Invoice::count(),

            $user = Auth::user(),

            'taskManagement' => Task::where('created_by', $user->id)
                ->orWhereJsonContains('assigned_to', (string) $user->id)
                ->orderByDesc('id')
                ->count(),
        ]);
    }
}
