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

                        <div class="row">

                            <div class="mb-3 col-md-6">
                                <label class="form-label">Start Date</label>
                                <input type="date" name="start_date" class="form-control"
                                    value="{{ old('start_date', optional($task->start_date)->format('Y-m-d')) }}"
                                    required>
                                @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3 col-md-6">
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
                            </div>

                        </div>

                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Due Date</label>
                                <input type="date" name="due_date" class="form-control"
                                    value="{{ old('due_date', optional($task->due_date)->format('Y-m-d')) }}"
                                    required>
                                @error('due_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3 col-md-6">
                                <label class="form-label">Due Time</label>

                                @php
                                    $dueTime = old(
                                        'due_time',
                                        isset($task->due_date)
                                            ? \Carbon\Carbon::parse($task->due_date)->format('H:i')
                                            : ''
                                    );
                                @endphp

                                <select name="due_time" class="form-select">
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
                            </div>
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