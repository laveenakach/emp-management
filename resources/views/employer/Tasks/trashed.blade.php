@extends('layouts.app')
@section('title', 'Trashed Tasks')

@section('content')
<!-- Custom Styles -->
<style>
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
</style>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

<div class="container mt-2">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-4 gap-2">
        <h3 class="fw-bold text-primary">🗑️ Trashed Tasks</h3>
        <div>
            <a href="{{ route('tasks.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left-circle"></i> Back
            </a>
        </div>
    </div>

    <!-- Toast Messages -->
    <div class="toast-container position-fixed top-0 end-0 p-3">
        @foreach (['success', 'error', 'info', 'warning'] as $msg)
            @if (session($msg))
                <div class="toast show text-white bg-{{ $msg === 'error' ? 'danger' : $msg }}" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">{{ session($msg) }}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            @endif
        @endforeach

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

    <!-- Trashed Tasks Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="attendanceTable" class="table table-hover table-bordered nowrap w-100">
                    <thead class="table-light">
                        <tr>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Assigned To</th>
                            <th>Deleted At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($trashedTasks as $task)
                            <tr>
                                <td>{{ $task->title }}</td>
                                <td>{{ $task->status }}</td>
                                <td>
                                    @php $assignedUsers = $task->assigned_to; @endphp
                                    @foreach($assignedUsers as $index => $userId)
                                        @php $user = \App\Models\User::find($userId); @endphp
                                        @if($user)
                                            {{ $user->name }}{{ $index !== count($assignedUsers) - 1 ? ',' : '' }}<br>
                                        @endif
                                    @endforeach
                                </td>
                                <td>{{ $task->deleted_at ? $task->deleted_at->format('d M Y h:i A') : '' }}</td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        <form action="{{ route('tasks.restore', $task->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-success">
                                                <i class="bi bi-arrow-clockwise"></i> Restore
                                            </button>
                                        </form>

                                        <form action="{{ route('tasks.forceDelete', $task->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to permanently delete this task?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash3"></i> Delete Permanently
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
            responsive: true,
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
