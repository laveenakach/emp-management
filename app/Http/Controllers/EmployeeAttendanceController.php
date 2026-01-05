<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendances;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class EmployeeAttendanceController extends Controller
{

    public function index()
    {
        $today = now()->toDateString();
        $attendance = Attendances::where('employee_id', Auth::id())->whereDate('date', $today)->first();

        $attendancelist = Attendances::Join('users', 'attendances.employee_id', '=', 'users.id')
            ->where('attendances.employee_id', Auth::id())
            ->whereNotIn('users.role', ['employer'])
            ->select('attendances.*', 'users.name as user_name')
            ->get();

        return view('employee.attendance.index', compact('attendance', 'attendancelist'));
    }

    public function checkIn()
    {
        $today = today()->toDateString();
        $nowTime = now(); // Get full current time

        $attendance = Attendances::firstOrCreate(
            [
                'employee_id' => Auth::id(),
                'date' => $today,
            ],
            [
                'check_in' => now()->format('H:i:s'),
            ]
        );

        // Determine Greeting based on current hour
        $hour = $nowTime->format('H');

        if ($hour >= 5 && $hour < 12) {
            $greeting = 'Good Morning!';
        } elseif ($hour >= 12 && $hour < 17) {
            $greeting = 'Good Afternoon!';
        } else {
            $greeting = 'Good Evening!';
        }

        return redirect()->back()->with('success', "Checked in successfully! $greeting");
    }

    public function checkOut()
    {
        $today = now()->toDateString();

        $attendance = Attendances::where('employee_id', Auth::id())->whereDate('date', $today)->first();

        if ($attendance && !$attendance->check_out) {
            $attendance->update([
                'check_out' => now()->format('H:i:s'),
            ]);

            return redirect()->back()->with('success', 'Checked out successfully!');
        }

        return redirect()->back()->with('error', 'Already checked out or not checked in yet.');
    }




}
