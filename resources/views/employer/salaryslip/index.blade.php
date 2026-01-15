@extends('layouts.app')

@section('content')
<style>
    .btn-outline-warning.custom-hover:hover {
        background-color: #66fdee !important;
        /* Your desired hover color */
        color: #000;
        /* Text color on hover */
        border-color: #4d4b44 !important;
    }

    a.btn.rounded-pill.px-4.shadow-sm.add-btn:hover {
        background-color: #6bf9f0;
        color: black;
    }

    a.btn.rounded-pill.px-4.shadow-sm.add-btn {
        background-color: black;
        color: white;
        border: 2px soild red;
    }
</style>
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<div class="container mt-2">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-primary">Salary Slips</h3>
        <a href="{{ route('employer.salary-slip.create') }}" class="btn rounded-pill px-4 shadow-sm add-btn">
            <i class="bi bi-plus-circle me-2"></i>upload OR auto-generate salary slip
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

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
            <table id="salarySlipTable" class="table table-hover table-bordered nowrap" style="width:100%;">
                <thead class="table-dark">
                    <tr>
                        <th>Sr no</th>
                        <th>Employee ID</th>
                        <th>Employee Name</th>
                        <th>Month</th>
                        <th>Created At</th>
                        <th>Slip</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($salarySlips as $slip)
                    <tr>
                        <td>{{ $loop->iteration }}</td> {{-- Serial Number --}}
                        <td>{{ $slip->empuniq_id}}</td>
                        <td>{{ $slip->employee_name}}</td>
                        <td>{{ $slip->month }}</td>
                        <td>{{ \Carbon\Carbon::parse($slip->created_at)->format('d M, Y') }}</td>
                        <td>
                            @if($slip->status == 'generated')

                            <a href="{{ route('salary_slips.download', $slip->id) }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-download me-1"></i> Download PDF
                            </a>

                            @else

                            <a href="{{ asset($slip->file_path) }}" class="btn btn-sm btn-outline-info" target="_blank">
                                <i class="bi bi-file-earmark-pdf"></i> View uploaded PDF
                            </a>

                            @endif

                        </td>
                        <td>
                            <div class="d-flex gap-2 align-items-center">
                                <a href="{{ route('employer.salaryslips.edit', $slip->id) }}" class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                <form action="{{ route('employer.salaryslips.delete', $slip->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="bi bi-trash"></i> Delete
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script>
    $(document).ready(function() {
        $('#salarySlipTable').DataTable({
            dom: 'Bfrtip',
            buttons: ['excelHtml5'],
            pageLength: 10,
            scrollX: true,
            scrollY: true,
           
        });
    });
</script>

<!-- Bootstrap Icons CDN (Optional) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-dismiss toast after 5 seconds
        setTimeout(() => {
            let toast = new bootstrap.Toast('.toast');
            toast.hide();
        }, 7000); // Toast will disappear after 5 seconds
    });
</script>
@endsection