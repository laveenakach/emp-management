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

    private function generateAndSaveSalaryPdf($slip, $user)
    {
        $pdf = Pdf::loadView('employer.salaryslip.salaryslip_pdf', compact('slip', 'user'));

        $fileName = 'Salary_Slip_' . $user->name . '_' . $slip->month . '.pdf';
        $filePath = 'uploads/salary_slips/' . $fileName;

        if (!file_exists(public_path('uploads/salary_slips'))) {
            mkdir(public_path('uploads/salary_slips'), 0755, true);
        }

        file_put_contents(public_path($filePath), $pdf->output());

        return $filePath;
    }

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
            'employee_id'   => 'required|exists:users,id',
            'month'         => 'required|date_format:Y-m',
            'salary'        => 'nullable|required_if:auto_generate,0|numeric|min:0',
            'auto_generate' => 'nullable|boolean',
        ]);

        $employee = User::findOrFail($request->employee_id);
        $month    = $request->month;

        $start = Carbon::parse($month)->startOfMonth();
        $end   = Carbon::parse($month)->endOfMonth();

        $totalDays = 30; // COMPANY POLICY: always 30 days

        // ---------- MANUAL ENTRY ----------
        if (!$request->boolean('auto_generate')) {
            $grossSalary = (float) $request->salary;

            $salarySlip = SalarySlip::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'month'       => $month,
                ],
                [
                    'total_present_days' => 0,
                    'total_half_days'    => 0,
                    'total_absent_days'  => 0,

                    'basic_salary'       => round(0.40 * $grossSalary, 2),
                    'hra'                => round(0.40 * (0.40 * $grossSalary), 2),
                    'conveyance'         => 1600,
                    'medical'            => 1250,
                    'special_allowance'  => round($grossSalary - (0.40 * $grossSalary + 0.40 * (0.40 * $grossSalary) + 1600 + 1250), 2),

                    'gross_salary'       => $grossSalary,
                    'professionalTax'    => 200,
                    'absentDeduction'    => 0,
                    'deductions'         => 200,
                    'net_salary'         => round($grossSalary - 200, 2),
                    'status'             => 'generated',
                ]
            );

            // Generate PDF
            $pdfPath = $this->generateAndSaveSalaryPdf($salarySlip, $employee);
            $salarySlip->update(['file_path' => $pdfPath]);

            return redirect()
                ->route('employer.salary_slips.index')
                ->with('success', 'Salary slip generated successfully.');
        }

        // ---------- AUTO-GENERATE ----------
        $previousSlip = SalarySlip::where('employee_id', $employee->id)
            ->where('gross_salary', '>', 0)
            ->where('month', '<', $month)
            ->orderBy('month', 'desc')
            ->first();

        if (!$previousSlip) {
            return back()->withErrors(['salary' => 'No previous gross salary found for this employee.']);
        }

        $grossSalary = (float) $previousSlip->gross_salary;

        // FETCH ATTENDANCE
        $attendanceMap = Attendances::where('employee_id', $employee->id)
            ->whereBetween('date', [$start, $end])
            ->get()
            ->keyBy(fn ($a) => Carbon::parse($a->date)->toDateString());

        $presentDays = 0;
        $halfDays    = 0;
        $absentDays  = 0;

        $workedDaysCounter = 0;

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            if ($workedDaysCounter >= 30) break;
            $workedDaysCounter++;

            $dayStr = $date->toDateString();

            if (!isset($attendanceMap[$dayStr])) {
                $absentDays++;
                continue;
            }

            $day = $attendanceMap[$dayStr];

            if (!$day->check_in || !$day->check_out) {
                $absentDays++;
                continue;
            }

            $minutes = Carbon::parse($day->check_out)
                ->diffInMinutes(Carbon::parse($day->check_in));

            if ($minutes >= 360) {
                $presentDays++;
            } elseif ($minutes >= 240) {
                $halfDays++;
            } else {
                $absentDays++;
            }
        }

        // ---------- SALARY CALCULATION ----------
        $perDaySalary    = $grossSalary / $totalDays;
        $absentDeduction = round($perDaySalary * $absentDays, 2);

        // Deduction for half days
        $halfDayDeduction = round($perDaySalary * 0.5 * $halfDays, 2);

        $totalDeduction  = 200 + $absentDeduction + $halfDayDeduction; // + professional tax
        $netSalary       = round($grossSalary - $totalDeduction, 2);

        $basicSalary = round(0.40 * $grossSalary, 2);
        $hra         = round(0.40 * $basicSalary, 2);
        $conveyance  = $presentDays > 0 ? 1600 : 0;
        $medical     = $presentDays > 0 ? 1250 : 0;
        $specialAllowance = round($grossSalary - ($basicSalary + $hra + $conveyance + $medical), 2);

        // ---------- SAVE SALARY SLIP ----------
        $salarySlip = SalarySlip::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'month'       => $month,
            ],
            [
                'total_present_days' => $presentDays, // full days only
                'total_half_days'    => $halfDays,
                'total_absent_days'  => $absentDays,  // only full absents

                'basic_salary'       => $basicSalary,
                'hra'                => $hra,
                'conveyance'         => $conveyance,
                'medical'            => $medical,
                'special_allowance'  => $specialAllowance,

                'gross_salary'       => $grossSalary,
                'professionalTax'    => 200,
                'absentDeduction'    => $absentDeduction + $halfDayDeduction,
                'deductions'         => $totalDeduction,
                'net_salary'         => $netSalary,

                'status'             => 'generated',
            ]
        );

        // ---------- GENERATE PDF ----------
        $pdfPath = $this->generateAndSaveSalaryPdf($salarySlip, $employee);
        $salarySlip->update(['file_path' => $pdfPath]);

        return redirect()
            ->route('employer.salary_slips.index')
            ->with('success', 'Salary slip generated successfully.');
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
        $request->validate([
            'employee_id' => 'required|exists:users,id',
            'month'       => 'required|date_format:Y-m',
            'salary'      => 'nullable|numeric',
        ]);

        $salarySlip = SalarySlip::findOrFail($id);
        $employee   = User::findOrFail($request->employee_id);
        $month      = $request->month;

        $start     = Carbon::parse($month)->startOfMonth();
        $totalDays = 30;

        $grossSalary = $request->salary ?? $salarySlip->gross_salary;

        // Attendance
        $attendance = Attendances::where('employee_id', $employee->id)
            ->whereBetween('date', [$start, $start->copy()->endOfMonth()])
            ->get();

        $presentDays = 0;
        $halfDays = 0;

        foreach ($attendance as $day) {
            if ($day->check_in && $day->check_out) {
                $hours = Carbon::parse($day->check_out)
                    ->diffInHours(Carbon::parse($day->check_in));

                if ($hours >= 6) {
                    $presentDays++;
                } elseif ($hours >= 4) {
                    $halfDays++;
                }
            }
        }

        $effectiveDays  = $presentDays + ($halfDays * 0.5);
        $absentDays     = max($totalDays - $effectiveDays, 0);
        $perDaySalary   = $grossSalary / $totalDays;
        $absentDeduction = round($perDaySalary * $absentDays, 2);

        $professionalTax = $grossSalary > 0 ? 200 : 0;
        $netSalary       = round($grossSalary - $absentDeduction - $professionalTax, 2);

        $basicSalary = round(0.40 * $grossSalary, 2);
        $hra         = round(0.40 * $basicSalary, 2);
        $conveyance  = $presentDays > 0 ? 1600 : 0;
        $medical     = $presentDays > 0 ? 1250 : 0;

        $specialAllowance = round(
            $grossSalary - ($basicSalary + $hra + $conveyance + $medical),
            2
        );

        $salarySlip->update([
            'employee_id'        => $employee->id,
            'month'              => $month,
            'total_present_days' => $presentDays,
            'total_half_days'    => $halfDays,
            'total_absent_days'  => $absentDays,

            'basic_salary'       => $basicSalary,
            'hra'                => $hra,
            'conveyance'         => $conveyance,
            'medical'            => $medical,
            'special_allowance'  => $specialAllowance,

            'gross_salary'       => $grossSalary,
            'professionalTax'    => $professionalTax,
            'absentDeduction'    => $absentDeduction,
            'deductions'         => $professionalTax + $absentDeduction,
            'net_salary'         => $netSalary,

            'status'             => 'generated',
        ]);

        // ✅ REGENERATE PDF AFTER EDIT
        $pdfPath = $this->generateAndSaveSalaryPdf($salarySlip, $employee);
        $salarySlip->update(['file_path' => $pdfPath]);

        return redirect()
            ->route('employer.salary_slips.index')
            ->with('success', 'Salary slip updated & PDF regenerated successfully.');
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
