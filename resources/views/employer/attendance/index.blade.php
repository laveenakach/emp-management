@extends('layouts.app')

@section('content')
<!-- Custom Styles -->
<style>
    div.dataTables_wrapper .dataTables_filter {
        margin-bottom: 10px; /* space above search */
    }
    td {
        vertical-align: middle; /* restore table alignment */
    }

    .action-buttons {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
    }

    .action-buttons form {
        margin: 0;
    }
    .btn-outline-warning.custom-hover:hover {
        background-color: #66fdee !important;
        color: #000;
        border-color: #4d4b44 !important;
    }

    a.btn.rounded-pill.px-4.shadow-sm.add-btn:hover {
        background-color: #6bf9f0;
        color: black;
    }

    a.btn.rounded-pill.px-4.shadow-sm.add-btn {
        background-color: black;
        color: white;
    }
    @media (max-width: 767.98px) {
    td {
        vertical-align: middle; /* restore table alignment */
    }

    .action-buttons {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
    }

    .action-buttons form {
        margin: 0;
    }
}

</style>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

<div class="container mt-2">
    <!-- Header -->
    <div class="d-none d-md-flex gap-2 d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-primary">Employee Attendance Records</h3>
        <a href="{{ route('employer.attendance.create') }}" class="btn rounded-pill px-4 shadow-sm add-btn">
            <i class="bi bi-plus-circle me-2"></i> Add Attendance
        </a>
    </div>

    <div class="d-flex d-md-none justify-content-between align-items-center mb-3">
        <h3 class="fw-bold text-primary">Employee Attendance Records</h3>
        <a href="{{ route('employer.attendance.create') }}"
                class="text-decoration-none text-dark">
                    <i class="bi bi-plus-circle fs-5"></i>
                </a>
    </div>

    <!-- Toast Messages -->
    <div class="toast-container position-fixed top-0 end-0 p-3">
        @if (session('status'))
            <div class="toast show text-white bg-success" role="alert">
                <div class="d-flex">
                    <div class="toast-body">{{ session('status') }}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="toast show text-white bg-danger" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        @endif
    </div>

    <!-- Attendance Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="attendanceTable" class="table table-hover table-bordered nowrap" style="width:100%;">
                    <thead class="table-dark">
                        <tr>
                            <th>Sr No.</th>
                            <th>Employee ID</th>
                            <th>Employee</th>
                            <th>Date</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($attendances as $attendance)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $attendance->employee->empuniq_id ?? 'N/A' }}</td>
                                <td>{{ $attendance->employee->name ?? 'N/A' }}</td>
                                <td>{{ $attendance->date }}</td>
                                <td>{{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('h:i A') : '-' }}</td>
                                <td>{{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('h:i A') : '-' }}</td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('employer.attendance.edit', $attendance->id) }}" class="btn btn-warning btn-sm">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('employer.attendance.delete', $attendance->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script>
    $(document).ready(function () {
        $('#attendanceTable').DataTable({
        dom: 'Bfrtip',
        buttons: ['excelHtml5'],
        responsive: false,
       // scrollX: true,
        pageLength: 10
    });
    });

    // Toast auto-hide
    document.addEventListener('DOMContentLoaded', function () {
        setTimeout(() => {
            let toastEl = document.querySelector('.toast');
            if (toastEl) {
                let toast = new bootstrap.Toast(toastEl);
                toast.hide();
            }
        }, 7000);
    });
</script>

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endsection
