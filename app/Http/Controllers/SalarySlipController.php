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

        file_put_contents(
            public_path($filePath),
            $pdf->output()
        );

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
            'pdf_file'      => 'nullable|mimes:pdf|max:5120',
            'auto_generate' => 'nullable|boolean',
        ]);

        $employee = User::findOrFail($request->employee_id);
        $month    = $request->month;

        $start     = Carbon::parse($month)->startOfMonth();
        $end       = Carbon::parse($month)->endOfMonth();
        $totalDays = $start->daysInMonth;

        /*
        ==================================================
        ✅ AUTO GENERATE SALARY SLIP
        ==================================================
        */
        if ($request->boolean('auto_generate')) {

            // 🔹 Fetch last valid gross salary
            $previousSlip = SalarySlip::where('employee_id', $employee->id)
                ->where('gross_salary', '>', 0)
                ->where('month', '<', $month)
                ->orderBy('month', 'desc')
                ->first();

            if (!$previousSlip) {
                return back()->withErrors([
                    'salary' => 'No previous gross salary found for this employee.'
                ]);
            }

            $grossSalary = (float) $previousSlip->gross_salary;

            // ---------------- ATTENDANCE ----------------
            $attendance = Attendances::where('employee_id', $employee->id)
                ->whereBetween('date', [$start, $end])
                ->get();

            $presentDays = 0;
            $halfDays    = 0;

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

            $effectiveDays = $presentDays + ($halfDays * 0.5);
            $absentDays    = max($totalDays - $effectiveDays, 0);

            // ---------------- SALARY CALCULATION ----------------
            // ✅ Gross salary NEVER changes
            $perDaySalary    = $grossSalary / $totalDays;
            $absentDeduction = round($perDaySalary * $absentDays, 2);

            $professionalTax = $grossSalary > 0 ? 200 : 0;
            $netSalary       = round($grossSalary - $absentDeduction - $professionalTax, 2);

            // ---------------- SALARY BREAKUP ----------------
            $basicSalary = round(0.40 * $grossSalary, 2);
            $hra         = round(0.40 * $basicSalary, 2);
            $conveyance  = $presentDays > 0 ? 1600 : 0;
            $medical     = $presentDays > 0 ? 1250 : 0;

            $specialAllowance = round(
                $grossSalary - ($basicSalary + $hra + $conveyance + $medical),
                2
            );

            // ---------------- SAVE SLIP ----------------
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
                    'professionalTax'    => $professionalTax,
                    'absentDeduction'    => $absentDeduction,
                    'deductions'         => $absentDeduction + $professionalTax,
                    'net_salary'         => $netSalary,

                    'status'             => 'generated',
                ]
            );

            // ---------------- GENERATE PDF ----------------
            $pdfPath = $this->generateAndSaveSalaryPdf($salarySlip, $employee);
            $salarySlip->update(['file_path' => $pdfPath]);

            return redirect()
                ->route('employer.salary_slips.index')
                ->with('success', 'Salary slip generated successfully.');
        }

        /*
        ==================================================
        ✅ MANUAL SALARY ENTRY
        ==================================================
        */

        $path = null;
        if ($request->hasFile('pdf_file')) {
            $pdf = $request->file('pdf_file');
            $pdfName = time() . '_' . $pdf->getClientOriginalName();
            $pdf->move(public_path('uploads/salary_slips'), $pdfName);
            $path = 'uploads/salary_slips/' . $pdfName;
        }

        $grossSalary = (float) $request->salary;

        $perDaySalary    = $grossSalary / $totalDays;
        $absentDeduction = 0;
        $professionalTax = $grossSalary >= 15000 ? 200 : 0;

        $netSalary = round($grossSalary - $professionalTax, 2);

        $basicSalary = round(0.40 * $grossSalary, 2);
        $hra         = round(0.40 * $basicSalary, 2);
        $conveyance  = 1600;
        $medical     = 1250;

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
                'basic_salary'      => $basicSalary,
                'hra'               => $hra,
                'conveyance'        => $conveyance,
                'medical'           => $medical,
                'special_allowance' => $specialAllowance,

                'gross_salary'      => $grossSalary,
                'professionalTax'   => $professionalTax,
                'absentDeduction'   => $absentDeduction,
                'deductions'        => $professionalTax,
                'net_salary'        => $netSalary,

                'status'            => 'generated',
            ]
        );

        $pdfPath = $this->generateAndSaveSalaryPdf($salarySlip, $employee);
        $salarySlip->update(['file_path' => $pdfPath]);

        return redirect()
            ->route('employer.salary_slips.index')
            ->with('success', 'Salary slip saved successfully.');
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
        $totalDays = $start->daysInMonth;

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
