@extends('layouts.app')
@section('title', 'Reschedule Task')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow border-0">
                <div class="card-body">
                    <div class="d-none d-md-flex gap-2 d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-3 text-primary fw-bold">🕒 Reschedule Task</h4>
                        <a href="javascript:history.back()" class="btn btn-sm btn-outline-secondary rounded-pill">
                            <i class="bi bi-arrow-left-circle me-1"></i> Back
                        </a>
                    </div>

                    <div class="d-flex d-md-none align-items-center justify-content-between mb-2">
                        <h4 class="mb-3 text-primary fw-bold">🕒 Reschedule Task</h4>
                        <a href="javascript:void(0);" onclick="window.history.back();" class="btn btn-dark btn-sm">
                                <i class="bi bi-arrow-left"></i>
                            </a>
                    </div>

                    <form method="POST" action="{{ route('tasks.reschedule', $task->id) }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" class="form-control"
                                value="{{ old('start_date', optional($task->start_date)->format('Y-m-d')) }}"
                                required>
                            @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Due Date</label>
                            <input type="date" name="due_date" class="form-control"
                                value="{{ old('due_date', optional($task->due_date)->format('Y-m-d')) }}"
                                required>
                            @error('due_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-success rounded-pill px-4">
                                <i class="bi bi-arrow-repeat"></i> Reschedule Task
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection