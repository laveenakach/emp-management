@extends('layouts.app')
@section('title', 'Reschedule Task')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow border-0">
                <div class="card-body">
                    <h4 class="mb-3 text-primary fw-bold">🕒 Reschedule Task</h4>

                    <form method="POST" action="{{ route('tasks.reschedule', $task->id) }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Start Date</label>
                            <input type="datetime-local" name="start_date" class="form-control"
                                value="{{ old('start_date', \Carbon\Carbon::parse($task->start_date)->format('Y-m-d\TH:i')) }}"
                                required>
                            @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Due Date</label>
                            <input type="datetime-local" name="due_date" class="form-control"
                                value="{{ old('due_date', \Carbon\Carbon::parse($task->due_date)->format('Y-m-d\TH:i')) }}"
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