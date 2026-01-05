<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalarySlip;
use App\Models\User;
use App\Models\Attendances;
use App\Models\Quotation;
use App\Models\EmployeeLeave;
use App\Notifications\SalarySlipUploaded;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
//use App\Mail\SalarySlipNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Mpdf\Mpdf;
use Dompdf\Dompdf;

require_once public_path('dompdf/autoload.inc.php');

class SalarySlipController extends Controller
{

    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'employer') {
            $salarySlips = DB::table('salary_slips')
                ->join('users', 'salary_slips.employee_id', '=', 'users.id')
                ->select(
                    'salary_slips.*',
                    'users.name as employee_name',
                    'users.email as employee_email',
                    'users.empuniq_id'
                )
                ->orderByDesc('salary_slips.id')
                ->paginate(10); // ✅ Add pagination

        } else {

            $salarySlips = DB::table('salary_slips')
                ->join('users', 'salary_slips.employee_id', '=', 'users.id')
                ->where('salary_slips.employee_id', $user->id)
                ->select(
                    'salary_slips.*',
                    'users.name as employee_name',
                    'users.email as employee_email',
                    'users.empuniq_id'
                )
                ->orderByDesc('salary_slips.id')
                ->paginate(10); // ✅ Add pagination
        }

        return view('employer.salaryslip.index', compact('salarySlips'));
    }


    // public function downloadPdf($id)
    // {
    //     $slip = SalarySlip::findOrFail($id);
    //     $pdf = Pdf::loadView('employer.salary_slips.salaryslip_pdf', compact('slip'));
    //     return $pdf->download('Salary_Slip_' . $slip->employee->name . '_' . $slip->month . '.pdf');
    // }


    public function downloadQuotationasPdf($id)
    {
        $quotation = Quotation::with('items', 'client')->findOrFail($id);

        $pdf = Pdf::loadView('accounts.Quotation.pdf', compact('quotation'));

        // echo "<pre>";
        // print_r($pdf);
        // die;

        return $pdf->download('quotation_' . $quotation->quotation_number . '.pdf');
    }


    public function generatePDF($id)
    {
        $slip = SalarySlip::findOrFail($id);
        //$user = User::findOrFail($slip->employee_id);
        $user = DB::table('users')
            // ->join('departments', 'users.department_id', '=', 'departments.id'), 'departments.name as department_name', 'designations.name as designation_name'
            // ->join('designations', 'users.designation_id', '=', 'designations.id')
            ->select('users.*')
            ->where('users.id', $slip->employee_id)
            ->first();

        $pdf = Pdf::loadView('employer.salaryslip.salaryslip_pdf', compact('slip', 'user'));
        return $pdf->download('Salary_Slip_' . $user->name . '_' . $slip->month . '.pdf');
    }

    public function create()
    {
        $url = route('employer.salary-slip.store');
        $employees = User::whereNotIn('role', ['employer'])->get();
        return view('employer.salaryslip.upload_salary_slip', compact('employees', 'url'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:users,id',
            'month' => 'required|date_format:Y-m',
            'salary' => $request->has('auto_generate') ? 'required' : 'nullable',
            'pdf_file' => 'nullable|mimes:pdf|max:5120',
        ]);

        $employee = User::findOrFail($request->employee_id);
        $month = $request->input('month');
        $start = Carbon::parse($month)->startOfMonth();
        $end = Carbon::parse($month)->endOfMonth();
        $path = null;

        $grossSalary = $request->input('salary'); // This is Gross Salary

        if ($request->has('auto_generate')) {

            // Attendance & Leave Calculation
            // Attendance Records
            $attendance = Attendances::where('employee_id', $employee->id)
                ->whereBetween('date', [$start, $end])
                ->get();

            $presentDays = 0;
            $halfDays = 0;

            foreach ($attendance as $day) {
                if ($day->check_in && $day->check_out) {
                    // Calculate total worked hours
                    $checkIn  = \Carbon\Carbon::parse($day->check_in);
                    $checkOut = \Carbon\Carbon::parse($day->check_out);

                    $workedHours = $checkOut->diffInHours($checkIn);

                    // Example rule: 
                    // - 4 to 6 hours = Half Day
                    // - > 6 hours = Full Day
                    // - < 4 hours = Absent (or ignored)
                    if ($workedHours >= 6) {
                        $presentDays += 1;
                    } elseif ($workedHours >= 4 && $workedHours < 6) {
                        $halfDays += 1;
                    }
                }
            }

            // Final Equivalent Present
            $totalPresent = $presentDays + ($halfDays * 0.5);


            $leaves = EmployeeLeave::where('user_id', $employee->id)
                ->where('status', 'approved')
                ->where('is_paid_leave', 'Paid')
                ->where(function ($query) use ($start, $end) {
                    $query->whereBetween('from_date', [$start, $end])
                        ->orWhereBetween('to_date', [$start, $end]);
                })->get();

            $total_half_days = $leaves->where('leave_duration', 'Half Day')->count();

           // $totalPaidleaves = $leaves + ($total_half_days * 0.5);

           // $presentDays = $attendance->count();
            $leaveDays = $leaves->sum(fn($leave) => Carbon::parse($leave->to_date)->diffInDays(Carbon::parse($leave->from_date)) + 1);

            //$end->day
            $totalDays = 30;
            $absentDays = max(0, $totalDays - ($totalPresent));
            // print_r($leaveDays);
            // die;
            // Reverse-calculate Basic Salary
            $conveyance = 1600;
            $medical = 1250;
            $basicSalary = 0.40 * $grossSalary;

            // Component breakdown
            $hra = 0.40 * $basicSalary;

            $special =  $grossSalary - ($basicSalary + $hra + $conveyance + $medical);

            // Final calculated gross salary to ensure precision
            $calculatedGross = $basicSalary + $hra + $conveyance + $medical + $special;

            // Deductions
            // $pf = 0.12 * $basicSalary;
            $professionalTax = 200;

            // Per day deduction
            $perDaySalary = ($grossSalary) / $totalDays;

            // $incomeTax = 0.05 * $grossSalary; 
            $absentDeduction = $perDaySalary * $absentDays;

            $totalDeductions =  $professionalTax  + $absentDeduction;
            $netSalary = $grossSalary - $totalDeductions;

            // Save to DB
            SalarySlip::updateOrCreate(
                ['employee_id' => $employee->id, 'month' => $month],
                [
                    'total_present_days' => $totalPresent,
                    'total_leave_days' => $absentDays,
                    'total_absent_days' => $absentDays,
                    'total_half_days' => $halfDays,
                    'basic_salary' => round($basicSalary, 2),
                    'hra' => round($hra, 2),
                    'conveyance' => $conveyance,
                    'medical' => $medical,
                    'special_allowance' => round($special, 2),
                    'gross_salary' => round($grossSalary, 2),
                    'professionalTax' => round($professionalTax, 2),
                    'absentDeduction' => round($absentDeduction, 2),
                    'deductions' => round($totalDeductions, 2),
                    'net_salary' => round($netSalary, 2),
                    'status' => 'generated',
                    'file_path' => null,
                ]
            );

            return redirect()->route('employer.salary_slips.index')
                ->with('success', 'Salary slip auto-generated successfully.');
        } else {
            // Manual Upload
            if ($request->hasFile('pdf_file')) {
                $pdf = $request->file('pdf_file');
                $pdfName = time() . '_' . $pdf->getClientOriginalName();
                $pdf->move(public_path('uploads/salary_slips'), $pdfName);
                $path = 'uploads/salary_slips/' . $pdfName;
            }

            SalarySlip::updateOrCreate(
                ['employee_id' => $employee->id, 'month' => $month],
                [
                    'file_path' => $path,
                    'status' => 'uploaded',
                ]
            );

            return redirect()->route('employer.salary_slips.index')
                ->with('success', 'Salary slip uploaded successfully.');
        }
    }


    // Send notification email
    //  $employee = $slip->employee;
    // Mail::to($employee->email)->send(new SalarySlipNotification($slip));
    // $employee = User::find($request->employee_id);
    // Notification::route('mail', $employee->email)->notify(new SalarySlipUploaded($salarySlip));

    // Show the form to edit an existing employee
    public function edit($id)
    {
        $url = route('employer.salaryslips.update', $id);

        $SalarySlip = SalarySlip::where('id', $id)->first();  // Get all departments
        $employees = User::where('role', 'employee')->get();

        // echo "<pre>";
        // print_r($SalarySlip);
        // die;

        return view('employer.salaryslip.upload_salary_slip', compact('SalarySlip', 'employees', 'url'));
    }

    public function update(Request $request, $id)
    {
        // Validate input
        $request->validate([
            'employee_id' => 'required|exists:users,id',
            'month' => 'required|date_format:Y-m',
            'salary' => $request->has('auto_generate') ? 'required' : 'nullable',
            'pdf_file' => 'nullable|mimes:pdf|max:5120',
        ]);

        $employee = User::findOrFail($request->employee_id);
        $salarySlip = SalarySlip::findOrFail($id); // Use specific ID
        $month = $request->input('month');
        $start = Carbon::parse($month)->startOfMonth();
        $end = Carbon::parse($month)->endOfMonth();

        if ($request->has('auto_generate')) {
            $grossSalary = $request->input('salary');

            // Attendance & Leave Summary
            $attendance = Attendances::where('employee_id', $employee->id)
                ->whereBetween('date', [$start, $end])
                ->get();

            $presentDays = 0;
            $halfDays = 0;

            foreach ($attendance as $day) {
                if ($day->check_in && $day->check_out) {
                    // Calculate total worked hours
                    $checkIn  = \Carbon\Carbon::parse($day->check_in);
                    $checkOut = \Carbon\Carbon::parse($day->check_out);

                    $workedHours = $checkOut->diffInHours($checkIn);

                    // Example rule: 
                    // - 4 to 6 hours = Half Day
                    // - > 6 hours = Full Day
                    // - < 4 hours = Absent (or ignored)
                    if ($workedHours >= 6) {
                        $presentDays += 1;
                    } elseif ($workedHours >= 4 && $workedHours < 6) {
                        $halfDays += 1;
                    }
                }
            }

            // Final Equivalent Present
            $totalPresent = $presentDays + ($halfDays * 0.5);

            $leaves = EmployeeLeave::where('user_id', $employee->id)
                ->where('status', 'approved')
                ->where('is_paid_leave', 'Unpaid')
                ->whereNotIn('leave_duration', ['Half Day'])
                ->where(function ($query) use ($start, $end) {
                    $query->whereBetween('from_date', [$start, $end])
                        ->orWhereBetween('to_date', [$start, $end]);
                })->get();


            $total_half_days = EmployeeLeave::where('user_id', $employee->id)
                ->where('status', 'approved')
                ->where('is_paid_leave', 'Unpaid')
                ->where('leave_duration', 'Half Day')
                ->where(function ($query) use ($start, $end) {
                    $query->whereBetween('from_date', [$start, $end])
                        ->orWhereBetween('to_date', [$start, $end]);
                })->count();

            //$total_half_days = $leaves->where('leave_duration', 'Half Day')->count();

            //$presentDays = $attendance->count();
            $leaveDays = $leaves->sum(
                fn($leave) =>
                Carbon::parse($leave->to_date)->diffInDays(Carbon::parse($leave->from_date)) + 1
            );

            //$end->day
            $totalDays = 30;
            $absentDays = max(0, $totalDays - ($totalPresent));

            // Salary Breakdown
            $conveyance = 1600;
            $medical = 1250;
            $basicSalary = 0.40 * $grossSalary;
            $hra = 0.40 * $basicSalary;
            $special = $grossSalary - ($basicSalary + $hra + $conveyance + $medical);
            $professionalTax = 200;

            $perDaySalary = ($grossSalary) / $totalDays;

            // print_r($perDaySalary);
            $absentDeduction = $perDaySalary * $absentDays;

            $totalDeductions =  $professionalTax  + $absentDeduction;
            $netSalary = $grossSalary - $totalDeductions;

            // Update the specific SalarySlip
            $salarySlip->update([
                'employee_id' => $employee->id,
                'month' => $month,
                'total_present_days' => $totalPresent,
                'total_leave_days' => $absentDays,
                'total_absent_days' => $absentDays,
                'total_half_days' => $halfDays,
                'basic_salary' => round($basicSalary, 2),
                'hra' => round($hra, 2),
                'conveyance' => round($conveyance, 2),
                'medical' => round($medical, 2),
                'special_allowance' => round($special, 2),
                'gross_salary' => round($grossSalary, 2),
                'professionalTax' => round($professionalTax, 2),
                'absentDeduction' => round($absentDeduction, 2),
                'deductions' => round($totalDeductions, 2),
                'net_salary' => round($netSalary, 2),
                'status' => 'generated',
                'file_path' => null,
            ]);

            return redirect()->route('employer.salary_slips.index')
                ->with('success', 'Salary slip auto-generated successfully.');
        } else {
            // Manual Upload
            $path = $salarySlip->file_path; // fallback to existing
            if ($request->hasFile('pdf_file')) {
                $pdf = $request->file('pdf_file');
                $pdfName = time() . '_' . $pdf->getClientOriginalName();
                $pdf->move(public_path('uploads/salary_slips'), $pdfName);
                $path = 'uploads/salary_slips/' . $pdfName;
            }

            $salarySlip->update([
                'employee_id' => $employee->id,
                'month' => $month,
                'file_path' => $path,
                'status' => 'uploaded',
            ]);

            return redirect()->route('employer.salary_slips.index')
                ->with('success', 'Salary slip updated successfully.');
        }
    }



    public function delete($id)
    {
        $SalarySlip = SalarySlip::where('id', $id)->delete();  // Get all 

        return redirect()->route('employer.salary_slips.index')->with('success', 'Salary Slip delete successfully.');
    }


    // Show employee's own salary slips
    public function empoloyeeindex()
    {
        $salarySlips = SalarySlip::join('users', 'salary_slips.employee_id', '=', 'users.id')
            ->where('salary_slips.employee_id', Auth::id())
            ->select(
                'salary_slips.*',
                'users.name as employee_name',
                'users.email as employee_email',
                'users.empuniq_id'
            )
            ->orderByDesc('salary_slips.id')
            ->get(); // DataTables handles pagination/searching

        //  echo '<pre>';
        // print_r($salarySlips);
        // die;

        return view('employee.salaryslip.index', compact('salarySlips'));
    }

    // Download specific slip
    public function download($id)
    {
        $slip = SalarySlip::where('id', $id)
            ->where('employee_id', Auth::id())
            ->firstOrFail();

        $filePath = public_path($slip->file_path);

        if (file_exists($filePath)) {
            return response()->download($filePath);
        }

        return back()->with('error', 'File not found.');
    }
}
