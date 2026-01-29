@extends('layouts.app')
@section('title', 'Submited Tasks')

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

    .btn-outline-danger:hover {
        background-color: #dc3545 !important;
        color: #fff !important;
        border-color: #dc3545 !important;
    }
</style>
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<div class="container mt-2">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-primary">Submited Task List</h4>
        <div>
        </div>
    </div>

    <!-- Toast Messages -->
    <div class="toast-container position-fixed top-0 end-0 p-3">
        @foreach (['success', 'error', 'info', 'warning'] as $msg)
        @if (session($msg))
        <div class="toast show text-white bg-{{ $msg == 'error' ? 'danger' : $msg }}" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    {{ session($msg) }}
                </div>
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

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="salarySlipTable" class="table table-hover table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Sr. No</th>
                            <th>Title</th>
                            <th>Status</th>
                            <!-- <th>Progress/Work Details</th> -->
                            @if(auth()->user()->role === 'employer')
                            <th>Employee Name</th>
                            <th>Total Working Time</th>
                            @endif
                            <th>Submitted At</th>
                            <th>Submission file</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tasks as $task)
                        <tr>
                            <td>{{ $loop->iteration }}</td> {{-- Serial Number --}}
                            <td>{{ $task->title }}</td>
                            <td>{{ ucfirst($task->status) }}</td>
                            <!-- <td>{{ ucfirst($task->progress) }}</td> -->
                            @if(auth()->user()->role === 'employer')
                                {{-- Employee Name Column --}}
                                <td>
                                    @foreach($task->users as $user)
                                        <div>{{ $user->name }}</div>
                                    @endforeach
                                </td>

                                {{-- Working Time Column --}}
                                <td>
                                    @foreach($task->users as $user)
                                        <div>
                                            @if($user->pivot->worked_minutes)
                                                {{ intdiv($user->pivot->worked_minutes, 60) }}h
                                                {{ $user->pivot->worked_minutes % 60 }}m
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </td>
                            @endif
                            <td>{{ \Carbon\Carbon::parse($task->submitted_at)->format('d M Y, h:i A') }}</td>
                            <td>
                                <a href="{{ asset($task->submission_file) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-file-earmark-arrow-down"></i> View Attachment
                                </a>
                            </td>
                            <td class="text-nowrap">
                                <div class="d-flex gap-2">
                                    <!-- @if(auth()->user()->role === 'employee')
                                    <button
                                        class="btn btn-sm btn-success"
                                        data-bs-toggle="modal"
                                        data-bs-target="#submitTaskModal"
                                        data-task-id="{{ $task->id }}"
                                        data-task-title="{{ $task->title }}"
                                        data-task-status="{{ $task->status }}"
                                        >
                                        <i class="bi bi-send-check"></i> Submit Task
                                    </button>
                                    @endif -->

                                    <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-primary d-flex align-items-center gap-1">
                                        <i class="bi bi-chat-dots-fill"></i> View
                                    </a>

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

<!-- Submit Task Modal -->



<script>
    const submitTaskModal = document.getElementById('submitTaskModal');
    submitTaskModal.addEventListener('show.bs.modal', function(event) {

        const button = event.relatedTarget;
        const taskId = button.getAttribute('data-task-id');
        const taskTitle = button.getAttribute('data-task-title');

        document.getElementById('modalTaskId').value = taskId;
        document.getElementById('taskTitle').textContent = taskTitle;
    });
</script>


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