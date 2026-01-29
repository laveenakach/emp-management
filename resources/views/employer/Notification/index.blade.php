@extends('layouts.app')

@section('content')
<style>
    div.dataTables_wrapper .dataTables_filter {
        margin-bottom: 10px; /* space above search */
    }
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
    <div class="d-none d-md-flex gap-2 d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-primary">Notifications</h3>
        @if(auth()->user()->role === 'employer')
        <a href="{{ route('notifications.create') }}" class="btn rounded-pill px-4 shadow-sm add-btn">
            <i class="bi bi-plus-circle me-2"></i>Create Notification
        </a>
        @endif
        </a>
    </div>

    <div class="d-flex d-md-none justify-content-between align-items-center mb-3">
        <h3 class="fw-bold text-primary">Notifications</h3>
        @if(auth()->user()->role === 'employer')
            <a href="{{ route('notifications.create') }}">
                <i class="bi bi-plus-circle fs-5"></i>
            </a>
        @endif
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
            <table id="salarySlipTable" class="table table-hover table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Sr no</th>
                        @if(auth()->user()->role === 'employer')
                            <th>Employee</th>
                        @endif
                        <th>Title</th>
                        <th>Description</th>
                        <th>Notification Date</th>
                        <th>Due Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($notifications as $notification)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        @if(auth()->user()->role === 'employer')
                            <td>{{ $notification->data['employee_name'] ?? '—' }}</td>

                        @endif
                        {{-- Title from JSON --}}
                        <td>{{ $notification->data['title'] ?? '-' }}</td>

                        {{-- Message from JSON --}}
                        <td>{{ $notification->data['message'] ?? '-' }}</td>

                        {{-- Notification Created Date --}}
                        <td>{{ $notification->created_at->format('d M, Y h:i A') }}</td>

                        {{-- Due Date from JSON --}}
                        <td>
                            @if(isset($notification->data['due_date']))
                                {{ \Carbon\Carbon::parse($notification->data['due_date'])->format('d M, Y') }}
                            @else
                                -
                            @endif
                        </td>

                        <td>
                            <span class="badge bg-{{ $notification->read_at ? 'secondary' : 'success' }}">
                                {{ $notification->read_at ? 'Read' : 'Unread' }}
                            </span>
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
            pageLength: 10
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