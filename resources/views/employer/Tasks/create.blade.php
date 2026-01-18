@extends('layouts.app')
@section('title', 'Create Tasks')

@section('title', isset($task) ? 'Update Task' : 'Create Task')

@section('content')
<div class="container mt-3">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <div class="d-none d-md-flex gap-2 d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold text-primary"> 📄 {{ isset($task) ? 'Update Task' : 'Create New Task' }}</h2>
                <a href="javascript:void(0);" onclick="window.history.back();" class="btn btn-outline-secondary rounded-pill">
                    <i class="bi bi-arrow-left"></i> Back to List
                </a>
        </div>

        <div class="d-flex d-md-none align-items-center justify-content-between mb-2">
            <h2 class="fw-bold text-primary"> 📄 {{ isset($task) ? 'Update Task' : 'Create New Task' }}</h2>

            <a href="javascript:void(0);" onclick="window.history.back();" class="btn btn-dark btn-sm">
                    <i class="bi bi-arrow-left"></i>
                </a>
        </div>

            @if (session('success'))
            <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
            <div class="alert alert-danger shadow-sm">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="card shadow border-0 rounded-2">
                <div class="card-body p-4">
                    <form method="POST"
                        action="{{ isset($task) ? route('tasks.update', $task->id) : route('tasks.store') }}"
                        enctype="multipart/form-data">
                        @csrf
                        @if(isset($task))
                        @method('PUT')
                        @endif

                        <div class="row">
                            <!-- Title -->
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control"
                                    value="{{ old('title', $task->title ?? '') }}" required>
                                @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- Priority -->
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Priority <span class="text-danger">*</span></label>
                                <select name="priority" class="form-select" required>
                                    <option value="">Select Priority</option>
                                    @foreach (['Low', 'Medium', 'High'] as $priority)
                                    <option value="{{ $priority }}" {{ old('priority', $task->priority ?? '') == $priority ? 'selected' : '' }}>{{ $priority }}</option>
                                    @endforeach
                                </select>
                                @error('priority') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- Status -->
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="">Select Status</option>
                                    @foreach (['Not Started', 'In Progress', 'Completed', 'Blocked', 'Submitted'] as $status)
                                    <option value="{{ $status }}" {{ old('status', $task->status ?? '') == $status ? 'selected' : '' }}>{{ $status }}</option>
                                    @endforeach
                                </select>
                                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- Role -->
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Role <span class="text-danger">*</span></label>
                                <select name="role" class="form-select" required>
                                    <option value="">Select Role</option>
                                    @foreach (['Owner', 'Reviewer', 'Collaborator'] as $role)
                                    <option value="{{ $role }}" {{ old('role', $task->role ?? '') == $role ? 'selected' : '' }}>{{ $role }}</option>
                                    @endforeach
                                </select>
                                @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- Start Date -->
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" class="form-control"
                                    value="{{ old('start_date', optional($task->start_date)->format('Y-m-d')) }}"

                                    required>
                                 @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- <div class="mb-3 col-md-6">
                                <label class="form-label">Start Time</label>

                                @php
                                    $startTime = old(
                                        'start_time',
                                        isset($task->start_date)
                                            ? \Carbon\Carbon::parse($task->start_date)->format('H:i')
                                            : ''
                                    );
                                @endphp

                                <select name="start_time" class="form-select">
                                    <option value="">Select Time</option>

                                    @for ($h = 0; $h < 24; $h++)
                                        @foreach (['00','15','30','45'] as $m)
                                            @php
                                                $time24 = sprintf('%02d:%s', $h, $m);
                                                $time12 = \Carbon\Carbon::createFromFormat('H:i', $time24)->format('g:i A');
                                            @endphp
                                            <option value="{{ $time24 }}" {{ $startTime === $time24 ? 'selected' : '' }}>
                                                {{ $time12 }}
                                            </option>
                                        @endforeach
                                    @endfor
                                </select>
                            </div> -->

                            <!-- Due Date -->
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Due Date <span class="text-danger">*</span></label>
                                <input type="date" name="due_date" class="form-control"
                                    value="{{ old('due_date', optional($task->due_date ?? null)->format('Y-m-d')) }}"
                                    required>
                                @error('due_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- <div class="mb-3 col-md-6">
                                <label class="form-label">Due Time</label>

                                @php
                                    $dueTime = old(
                                        'due_time',
                                        isset($task->due_date)
                                            ? \Carbon\Carbon::parse($task->due_date)->format('H:i')
                                            : ''
                                    );
                                @endphp

                                <select name="start_time" class="form-select">
                                    <option value="">Select Time</option>

                                    @for ($h = 0; $h < 24; $h++)
                                        @foreach (['00','15','30','45'] as $m)
                                            @php
                                                $time24 = sprintf('%02d:%s', $h, $m);
                                                $time12 = \Carbon\Carbon::createFromFormat('H:i', $time24)->format('g:i A');
                                            @endphp
                                            <option value="{{ $time24 }}" {{ $dueTime === $time24 ? 'selected' : '' }}>
                                                {{ $time12 }}
                                            </option>
                                        @endforeach
                                    @endfor
                                </select>
                            </div> -->

                            <!-- Assign User -->
                           @php
                            $assignedTo = old(
                                'assigned_to',
                                isset($task) ? $task->users->pluck('id')->toArray() : []
                            );
                            @endphp

                            <div class="mb-3 col-md-6">
                                <label class="form-label">Assign Users <span class="text-danger">*</span></label>
                                <select name="assigned_to[]" class="form-select" multiple required>
                                    @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}"
                                        {{ in_array($employee->id, $assignedTo) ? 'selected' : '' }}>
                                        {{ $employee->name }} ({{ $employee->empuniq_id }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('assigned_to') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <!-- Description -->
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Description <span class="text-danger">*</span></label>
                                <textarea name="description" class="form-control" rows="4" required>{{ old('description', $task->description ?? '') }}</textarea>
                                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- Attachments -->
                            <div class="mb-3 col-md-6">
                                <label for="attachments" class="form-label">Attachment <span class="text-muted">(optional)</span></label>
                                <input type="file" id="attachments" name="attachments" class="form-control" accept="image/*,application/pdf">
                                @error('attachments') <div class="invalid-feedback">{{ $message }}</div> @enderror

                                @if(isset($task->file_path))
                                <div class="mt-2">
                                    <a href="{{ asset($task->file_path) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-paperclip me-1"></i> View Current File
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="text-end mt-3">
                            <button type="submit" class="btn btn-success rounded-pill px-5 py-2">
                                <i class="bi bi-upload me-1"></i> {{ isset($task) ? 'Update Task' : 'Create Task' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Include Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Initialize Select2 -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        $('select[name="assigned_to[]"]').select2({
            placeholder: "Select employees",
            width: '100%'
        });
    });
</script>


@endsection