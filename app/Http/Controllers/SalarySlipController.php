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

        $totalDays = 30; // company policy

        /* ======================================================
        ATTENDANCE (COMMON FOR MANUAL & AUTO)
        ====================================================== */
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

        /* ======================================================
        MANUAL SALARY
        ====================================================== */
        if (!$request->boolean('auto_generate')) {

            $grossSalary = (float) $request->salary;

            $perDaySalary     = $grossSalary / $totalDays;
            $absentDeduction  = round($perDaySalary * $absentDays, 2);
            $halfDayDeduction = round($perDaySalary * 0.5 * $halfDays, 2);

            $totalAbsentDeduction = $absentDeduction + $halfDayDeduction;

            $basicSalary = round(0.40 * $grossSalary, 2);
            $hra         = round(0.40 * $basicSalary, 2);
            $conveyance  = $presentDays > 0 ? 1600 : 0;
            $medical     = $presentDays > 0 ? 1250 : 0;

            $specialAllowance = round(
                $grossSalary - ($basicSalary + $hra + $conveyance + $medical),
                2
            );

            $salarySlip = SalarySlip::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'month'       => $month,
                ],
                [
                    'total_present_days' => $presentDays,
                    'total_half_days'    => $halfDays,
                    'total_absent_days'  => $absentDays,

                    'basic_salary'       => $basicSalary,
                    'hra'                => $hra,
                    'conveyance'         => $conveyance,
                    'medical'            => $medical,
                    'special_allowance'  => $specialAllowance,

                    'gross_salary'       => $grossSalary,
                    'professionalTax'    => 200,

                    'absentDeduction' => $totalAbsentDeduction,
                    'deductions'      => 200 + $totalAbsentDeduction,
                    'net_salary'      => round($grossSalary - (200 + $totalAbsentDeduction), 2),

                    'status'             => 'generated',
                ]
            );

            $pdfPath = $this->generateAndSaveSalaryPdf($salarySlip, $employee);
            $salarySlip->update(['file_path' => $pdfPath]);

            return redirect()
                ->route('employer.salary_slips.index')
                ->with('success', 'Salary slip generated successfully.');
        }

        /* ======================================================
        AUTO-GENERATE SALARY
        ====================================================== */
        $previousSlip = SalarySlip::where('employee_id', $employee->id)
            ->where('gross_salary', '>', 0)
            ->where('month', '<', $month)
            ->orderBy('month', 'desc')
            ->first();

        if (!$previousSlip) {
            return back()->withErrors(['salary' => 'No previous gross salary found for this employee.']);
        }

        $grossSalary = (float) $previousSlip->gross_salary;

        $perDaySalary     = $grossSalary / $totalDays;
        $absentDeduction  = round($perDaySalary * $absentDays, 2);
        $halfDayDeduction = round($perDaySalary * 0.5 * $halfDays, 2);

        $totalDeduction = 200 + $absentDeduction + $halfDayDeduction;
        $netSalary      = round($grossSalary - $totalDeduction, 2);

        $basicSalary = round(0.40 * $grossSalary, 2);
        $hra         = round(0.40 * $basicSalary, 2);
        $conveyance  = $presentDays > 0 ? 1600 : 0;
        $medical     = $presentDays > 0 ? 1250 : 0;

        $specialAllowance = round(
            $grossSalary - ($basicSalary + $hra + $conveyance + $medical),
            2
        );

        $salarySlip = SalarySlip::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'month'       => $month,
            ],
            [
                'total_present_days' => $presentDays,
                'total_half_days'    => $halfDays,
                'total_absent_days'  => $absentDays,

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

        $start = Carbon::parse($month)->startOfMonth();
        $end   = $start->copy()->endOfMonth();

        $totalDays   = 30;
        $grossSalary = $request->salary ?? $salarySlip->gross_salary;

        // =========================
        // ✅ ATTENDANCE (FIXED)
        // =========================
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

        // =========================
        // ✅ SALARY CALCULATION
        // =========================
        $perDaySalary = $grossSalary / $totalDays;

        // half day = 0.5 deduction
        $absentDeduction = round(
            ($absentDays * $perDaySalary) +
            ($halfDays * 0.5 * $perDaySalary),
            2
        );

        $professionalTax = $grossSalary > 0 ? 200 : 0;

        $totalDeductions = round(
            $professionalTax + $absentDeduction,
            2
        );

        $netSalary = round(
            $grossSalary - $totalDeductions,
            2
        );

        // =========================
        // ✅ EARNINGS BREAKUP
        // =========================
        $basicSalary = round(0.40 * $grossSalary, 2);
        $hra         = round(0.40 * $basicSalary, 2);
        $conveyance  = ($presentDays + $halfDays) > 0 ? 1600 : 0;
        $medical     = ($presentDays + $halfDays) > 0 ? 1250 : 0;

        $specialAllowance = round(
            $grossSalary - ($basicSalary + $hra + $conveyance + $medical),
            2
        );

        // =========================
        // ✅ UPDATE SLIP
        // =========================
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
            'deductions'         => $totalDeductions,
            'net_salary'         => $netSalary,

            'status'             => 'generated',
        ]);

        // =========================
        // ✅ REGENERATE PDF
        // =========================
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
