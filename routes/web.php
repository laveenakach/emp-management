<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployerController;
use App\Http\Controllers\EmployeeAttendanceController;
use App\Http\Controllers\EmployeeLeaveController;
use App\Http\Controllers\SalarySlipController;
use App\Http\Controllers\EmployerAttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Account\QuotationController;
use App\Http\Controllers\Account\BillingController;
use App\Http\Controllers\Account\InvoiceController;
use App\Http\Controllers\Account\ClientController;
use App\Http\Controllers\EmployeeLetterController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\CandidateInvoiceController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/employee-leaves', [EmployeeLeaveController::class, 'index'])->name('employee.leaves.index');
    Route::get('/employee-leaves/create', [EmployeeLeaveController::class, 'create'])->name('employee.leaves.create');
    Route::post('/employee-leaves/store', [EmployeeLeaveController::class, 'store'])->name('employee.leaves.store');
    Route::put('/employee/leaves/update-status/{id}', [EmployeeLeaveController::class, 'updateStatus'])->name('employee.leaves.updateStatus');
    Route::post('/tasks/{task}/comments', [CommentController::class, 'store'])->name('tasks.comments.store');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/submitedtask', [TaskController::class, 'submitedtask'])->name('tasks.submitedtask');
    Route::post('/tasks/{task}/submit', [TaskController::class, 'submit'])->name('tasks.submit');

    Route::get('/team_leader/attendance', [EmployeeAttendanceController::class, 'index'])->name('team_leader.attendance');
    Route::get('/team_leader/salary-slips', [SalarySlipController::class, 'empoloyeeindex'])->name('team_leader.salary_slips.index');

    Route::get('/team_leader/dashboard', [DashboardController::class, 'index'])->name('dashboard.team_leader');

    Route::get('/tasks/{task}/reschedule', [TaskController::class, 'showRescheduleForm'])->name('tasks.reschedule.form');
    Route::post('/tasks/{task}/reschedule', [TaskController::class, 'reschedule'])->name('tasks.reschedule');
    Route::delete('/tasks/{id}/soft-delete', [TaskController::class, 'softDelete'])->name('tasks.softDelete');

    Route::post('/tasks/{task}/request-more-time', [TaskController::class, 'requestMoreTime'])
    ->name('tasks.requestMoreTime');

    Route::get('/trashedtasks', [TaskController::class, 'trashed'])->name('tasks.trashed');
    Route::post('/tasks/{id}/restore', [TaskController::class, 'restore'])->name('tasks.restore');
    Route::delete('/tasks/{id}/force-delete', [TaskController::class, 'forceDelete'])->name('tasks.forceDelete');

    Route::get('/salary-slip/download/{id}', [SalarySlipController::class, 'generatePDF'])->name('salary_slips.download');

    Route::get('/letters', [EmployeeLetterController::class, 'index'])->name('letters.index');
    Route::get('/letters/{letter}/download', [EmployeeLetterController::class, 'download'])->name('letters.download');
});

Route::middleware(['auth'])->group(function () {
    Route::resource('tasks', TaskController::class);
});

Route::middleware(['auth', 'role:employer'])->group(function () {

    Route::get('/employer/dashboard', [DashboardController::class, 'employerindex'])->name('dashboard.employer');
    // Show all employees (for employer)
    Route::get('employer/employees', [EmployerController::class, 'index'])->name('employer.employees.index');
    // Add new employee
    Route::get('employer/employees/create', [EmployerController::class, 'create'])->name('employer.employees.create');
    Route::post('employer/employees/store', [EmployerController::class, 'store'])->name('employer.employees.store');
    // Edit employee
    Route::get('employer/employees/{employee}/edit', [EmployerController::class, 'edit'])->name('employer.employees.edit');
    Route::put('employer/employees/{employee}', [EmployerController::class, 'update'])->name('employer.employees.update');
    Route::get('employer/employees/{id}', [EmployerController::class, 'show'])->name('employer.employees.show');

    Route::delete('/employees/{id}/soft-delete', [EmployerController::class, 'softDelete'])->name('employees.softDelete');
    Route::get('/trashedemployees', [EmployerController::class, 'trashed'])->name('employees.trashed');
    Route::post('/employees/{id}/restore', [EmployerController::class, 'restore'])->name('employees.restore');
    Route::delete('/employees/{id}/force-delete', [EmployerController::class, 'forceDelete'])->name('employees.forceDelete');

    Route::get('/get-designations/{department_id}', [EmployerController::class, 'getDesignations']);

    Route::get('/get-designations/{department_id}', function ($department_id) {
        return \App\Models\Designation::where('department_id', $department_id)->get();
    });


    Route::get('/employee/attendance', [EmployeeAttendanceController::class, 'index'])->name('employee.attendance');
    Route::post('/employee/check-in', [EmployeeAttendanceController::class, 'checkIn'])->name('employee.checkin');
    Route::post('/employee/check-out', [EmployeeAttendanceController::class, 'checkOut'])->name('employee.checkout');

    Route::get('employer/attendance', [EmployerAttendanceController::class, 'index'])->name('employer.attendance');
    Route::get('attendance/create', [EmployerAttendanceController::class, 'create'])->name('employer.attendance.create');
    Route::post('attendance/store', [EmployerAttendanceController::class, 'store'])->name('employer.attendance.store');
    // Edit attendance
    Route::get('employer/attendance/edit/{id}', [EmployerAttendanceController::class, 'edit'])->name('employer.attendance.edit');
    Route::put('employer/attendances/update/{id}', [EmployerAttendanceController::class, 'update'])->name('employer.attendances.update');
    Route::delete('employer/attendance/delete{employee}', [EmployerAttendanceController::class, 'delete'])->name('employer.attendance.delete');

    Route::get('/salary-slips', [SalarySlipController::class, 'index'])->name('employer.salary_slips.index');
    Route::get('/employer/upload-salary-slip', [SalarySlipController::class, 'create'])->name('employer.salary-slip.create');
    Route::post('/employer/upload-salary-slip', [SalarySlipController::class, 'store'])->name('employer.salary-slip.store');
    Route::get('employer/salaryslips/edit/{id}', [SalarySlipController::class, 'edit'])->name('employer.salaryslips.edit');
    Route::put('employer/salaryslips/update/{id}', [SalarySlipController::class, 'update'])->name('employer.salaryslips.update');
    Route::delete('employer/salaryslips/delete{employee}', [SalarySlipController::class, 'delete'])->name('employer.salaryslips.delete');

    Route::get('/letters/create', [EmployeeLetterController::class, 'create'])->name('letters.create');
    Route::post('/letters', [EmployeeLetterController::class, 'store'])->name('letters.store');
    Route::get('/letters/{letter}/edit', [EmployeeLetterController::class, 'edit'])->name('letters.edit');
    Route::put('/letters/{letter}', [EmployeeLetterController::class, 'update'])->name('letters.update');
    Route::delete('/letters/{letter}', [EmployeeLetterController::class, 'destroy'])->name('letters.destroy');

    Route::get('/notifications/create', [NotificationController::class, 'create'])->name('notifications.create');
    Route::post('/notifications', [NotificationController::class, 'store'])->name('notifications.store');

    Route::get('employer/notifications/edit/{id}', [NotificationController::class, 'edit'])->name('employer.notifications.edit');
    Route::put('employer/notifications/update/{id}', [NotificationController::class, 'update'])->name('employer.notifications.update');
    Route::delete('employer/notifications/delete/{id}', [NotificationController::class, 'destroy'])->name('employer.notifications.delete');

    Route::get('/notifications/read/{id}', [NotificationController::class, 'markRead'])
        ->name('notifications.read');

    Route::get('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])
        ->name('notifications.markAllRead');

    Route::post('/tasks/{task}/approve', [TaskController::class, 'approveTask'])
        ->name('tasks.approve');

    Route::post('/tasks/{task}/reject', [TaskController::class, 'rejectTask'])
        ->name('tasks.reject');

    Route::prefix('accounts')->name('accounts.')->group(function () {
        Route::resource('clients', \App\Http\Controllers\Account\ClientController::class);
        // Route::resource('quotations', \App\Http\Controllers\Account\QuotationController::class);
        //Route::resource('billings', \App\Http\Controllers\Account\BillingController::class);
        Route::resource('invoices', \App\Http\Controllers\Account\InvoiceController::class);
        Route::resource('payments', \App\Http\Controllers\Account\PaymentController::class);

        Route::put('/{Invoice}', [InvoiceController::class, 'update'])->name('inv.update');

        Route::get('/get-bills/{client_id}', [InvoiceController::class, 'getClientBills'])->name('get-bills');
        Route::get('/get-bill-details/{bill_id}', [InvoiceController::class, 'getBillDetails'])->name('get-bill-details');

        Route::get('/get-client-details/{id}', [InvoiceController::class, 'getClientDetails'])->name('get-client-details');
    });

    Route::prefix('quotations')->name('quotations.')->group(function () {
        Route::get('/', [QuotationController::class, 'index'])->name('index');
        Route::get('/create', [QuotationController::class, 'create'])->name('create');
        Route::post('/', [QuotationController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [QuotationController::class, 'edit'])->name('edit');
        Route::put('/{quotation}', [QuotationController::class, 'update'])->name('update');
        Route::delete('/{id}', [QuotationController::class, 'destroy'])->name('destroy');
        Route::get('/download/{id}', [QuotationController::class, 'download'])->name('download');
        Route::post('/email/{id}', [QuotationController::class, 'email'])->name('email');
        Route::get('/{id}/download-pdf', [QuotationController::class, 'downloadQuotationPdf'])->name('download');
    });


    Route::prefix('billings')->name('billings.')->group(function () {
        Route::get('/', [BillingController::class, 'index'])->name('index');
        Route::get('/create', [BillingController::class, 'create'])->name('create');
        Route::post('/', [BillingController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [BillingController::class, 'edit'])->name('edit');
        Route::put('/{bill}', [BillingController::class, 'update'])->name('update');
        Route::delete('/{id}', [BillingController::class, 'destroy'])->name('destroy');
        Route::get('/download/{id}', [BillingController::class, 'downloadBillPdf'])->name('download');
    });

    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/', [InvoiceController::class, 'index'])->name('index');
        Route::get('/create', [InvoiceController::class, 'create'])->name('create');
        Route::post('/', [InvoiceController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [InvoiceController::class, 'edit'])->name('edit');
        Route::put('/{bill}', [InvoiceController::class, 'update'])->name('update');
        Route::delete('/{id}', [InvoiceController::class, 'destroy'])->name('destroy');
        Route::get('/download/{id}', [InvoiceController::class, 'download'])->name('download');
    });

    Route::resource('candidates', CandidateController::class);

    Route::prefix('candidates/invoices')->name('candidates.invoices.')->group(function () {
        Route::get('/index', [CandidateInvoiceController::class, 'index'])->name('index');
        Route::get('/create', [CandidateInvoiceController::class, 'create'])->name('create');
        Route::post('/', [CandidateInvoiceController::class, 'store'])->name('store');
        Route::get('/{invoice}', [CandidateInvoiceController::class, 'show'])->name('show');
        Route::get('/{invoice}/download', [CandidateInvoiceController::class, 'downloadPdf'])->name('download');
        Route::get('/edit/{id}', [CandidateInvoiceController::class, 'edit'])->name('edit');
        Route::put('/{bill}', [CandidateInvoiceController::class, 'update'])->name('update');
        Route::delete('/{id}', [CandidateInvoiceController::class, 'destroy'])->name('destroy');
    });
});


Route::middleware(['auth', 'role:employee'])->group(function () {
    // Show all employees (for employer)
    Route::get('/employee/dashboard', [DashboardController::class, 'index'])->name('dashboard.employee');

    Route::get('/employee/attendance', [EmployeeAttendanceController::class, 'index'])->name('employee.attendance');

    Route::post('/employee/check-in', [EmployeeAttendanceController::class, 'checkIn'])->name('employee.checkin');
    Route::post('/employee/check-out', [EmployeeAttendanceController::class, 'checkOut'])->name('employee.checkout');

    Route::get('/employee/salary-slips', [SalarySlipController::class, 'empoloyeeindex'])->name('employee.salary-slips.index');
    Route::get('/employee/salary-slips/download/{id}', [SalarySlipController::class, 'download'])->name('employee.salary-slips.download');
    Route::get('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])
        ->name('notifications.markAllRead');
    Route::get('/notifications/read/{id}', [NotificationController::class, 'markRead'])
        ->name('notifications.read');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
