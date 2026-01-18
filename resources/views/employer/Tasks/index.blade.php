@extends('layouts.app')
@section('title', 'All Tasks')

@section('content')
<style>
    /* Mobile-friendly DataTable buttons */
    @media (max-width: 768px) {
        div.dt-buttons {
            width: 100%;
        }

        div.dt-buttons .btn {
            width: 100%;
            margin-bottom: 6px;
        }
    }

    div.dataTables_wrapper {
        width: 100%;
    }

    .dataTables_paginate {
        margin-right: 10px;
    }

    .dataTables_info {
        margin-left: 10px;
    }

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
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">

<div class="container mt-2">

    <div class="d-none d-md-flex gap-2 d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-primary">Task List</h4>
        <div>
            @if(in_array(auth()->user()->role, ['team_leader', 'manager','employer']))
            <a href="{{ route('tasks.create') }}" class="btn rounded-pill px-4 shadow-sm add-btn">
                <i class="bi bi-plus-circle me-2"></i>Create Tasks
            </a>
            <a href="{{ route('tasks.trashed') }}" class="btn btn-outline-danger btn-sm rounded-pill">
                <i class="bi bi-trash3-fill"></i>Trashed Tasks
            </a>
            @endif

        </div>
    </div>

    <div class="d-flex d-md-none justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-primary">Task List</h4>
        <div class="d-flex align-items-center gap-3">
            @if(in_array(auth()->user()->role, ['team_leader', 'manager','employer']))
            <a href="{{ route('tasks.create') }}">
                <i class="bi bi-plus-circle fs-5"></i>
            </a>
            <a href="{{ route('tasks.trashed') }}" class="text-danger">
                <i class="bi bi-trash3-fill"></i>
            </a>
            @endif

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
                <table id="salarySlipTable" class="table table-hover table-bordered nowrap" style="width:100%;">
                    <thead class="table-dark">
                        <tr>
                            <th>Sr. No</th>
                            <th>Title</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Start Date</th>
                            <th>Due Date</th>
                            @if(auth()->user()->role === 'employer')
                            <th>Assigned To</th>
                            @endif
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tasks as $task)
                        @php
                        $assignedUsers = $task->users; // Collection of User models
                        @endphp

                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $task->title }}</td>
                            <td>{{ ucfirst($task->priority) }}</td>
                            <td>{{ ucfirst($task->status) }}</td>
                            <td>{{ \Carbon\Carbon::parse($task->start_date)->format('d M Y, h:i A') }}</td>
                            <td>{{ \Carbon\Carbon::parse($task->due_date)->format('d M Y, h:i A') }}</td>

                            {{-- Assigned Users --}}
                            @if(auth()->user()->role === 'employer')
                            <td>
                                 @forelse($task->users as $user)
                                    {{ $user->name }}<br>
                                @empty
                                    <span class="text-muted">Not Assigned</span>
                                @endforelse
                            </td>
                            @endif

                            <td class="text-nowrap">
                                <div class="d-flex gap-2">

                                    @if(
                                        auth()->user()->role === 'employee' &&
                                        $task->users->contains('id', auth()->id())
                                    )
                                    <button class="btn btn-sm btn-success">
                                        <i class="bi bi-send-check"></i> Submit Task
                                    </button>
                                    @endif

                                    <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    @if(auth()->id() === $task->created_by)
                                        <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-warning d-flex align-items-center gap-1">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>

                                    <a href="{{ route('tasks.reschedule.form', $task->id) }}" class="btn btn-sm btn-success d-flex align-items-center gap-1">
                                        <i class="bi bi-clock-history"></i> Reschedule
                                    </a>

                                    <form action="{{ route('tasks.softDelete', $task->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this task?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                    @endif
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
<div class="modal fade" id="submitTaskModal" tabindex="-1" aria-labelledby="submitTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="submitTaskForm" method="POST" action="{{ route('tasks.submit') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="task_id" id="modalTaskId">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="submitTaskModalLabel">Submit Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to submit the task: <strong id="taskTitle"></strong>?</p>

                    <!-- Progress Update -->
                    <div class="mb-3">
                        <label for="progress" class="form-label">Progress / Work Details</label>
                        <textarea name="progress" id="progress" class="form-control" rows="4" required>{{ old('progress') }}</textarea>
                    </div>

                    <!-- Upload Completed Work -->
                    <div class="mb-3">
                        <label for="submission_file" class="form-label">Upload File (Optional)</label>
                        <input type="file" name="submission_file" id="submission_file" class="form-control">
                    </div>

                    <!-- Status -->
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="">-- Select Status --</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Submitted">Submitted</option>
                            <!-- <option value="Completed">Completed</option> -->
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Submit Task</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>


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

<!-- jQuery (MUST be first) -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<!-- DataTables core -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<!-- Buttons -->
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>

<!-- Export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>


<script>
    $(document).ready(function() {
        $('#salarySlipTable').DataTable({
                dom:
                    "<'row mb-2'<'col-md-6 d-flex align-items-center'B><'col-md-6 d-flex justify-content-end'f>>" +
                    "<'row'<'col-12'tr>>" +
                    "<'row mt-3'<'col-md-5'i><'col-md-7 d-flex justify-content-end'p>>",

                buttons: [{
                    extend: 'excelHtml5',
                    text: '<i class="bi bi-file-earmark-excel"></i> Excel',
                    className: 'btn btn-success btn-sm rounded-pill'
                }],

                pageLength: 10,
                responsive: true
            });

    });
</script>

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