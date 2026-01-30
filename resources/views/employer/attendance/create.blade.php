@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="col-lg-10 mx-auto">
        <div class="d-none d-md-flex gap-2 d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-primary">Employee Attendance</h4>
            <a href="{{ route('employer.attendance') }}" class="btn btn-outline-secondary rounded-pill">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>

        <div class="d-flex d-md-none align-items-center justify-content-between mb-2">
            <h4 class="fw-bold text-primary">Employee Attendance</h4>

            <a href="{{ route('employer.attendance') }}" class="btn btn-dark btn-sm">
                    <i class="bi bi-arrow-left"></i>
                </a>
        </div>

        {{-- Show success message --}}
        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Show validation errors --}}
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ $url }}" class="card shadow p-4 rounded">
            @csrf

            {{-- Use PUT if editing --}}
            @if(isset($Attendances))
            @method('PUT')
            @endif

            <div class="mb-3">
                <label for="employee_id" class="form-label fw-semibold">Select Employee</label>
                <select name="employee_id" id="employee_id"
                    class="form-select @error('employee_id') is-invalid @enderror" required>
                    <option value="">-- Choose Employee --</option>
                    @foreach($employees as $employee)
                    <option value="{{ $employee->id }}"
                        {{ old('employee_id', $Attendances->employee_id ?? '') == $employee->id ? 'selected' : '' }}>
                        {{ $employee->name }} ({{ $employee->empuniq_id }})
                    </option>
                    @endforeach
                </select>
                @error('employee_id')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="date" class="form-label fw-semibold">Date</label>
                <input type="date" name="date" id="date"
                    value="{{ old('date', $Attendances->date ?? '') }}"
                    class="form-control @error('date') is-invalid @enderror" required>
                @error('date')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row g-3 mb-3">

                {{-- ================= CHECK-IN ================= --}}
                    <!-- <div class="col-md-6">
                        <label class="form-label fw-semibold">Check-In Time</label>

                        @php
                            $selectedCheckIn = old(
                                'check_in',
                                isset($Attendances->check_in)
                                    ? \Carbon\Carbon::parse($Attendances->check_in)->format('H:i')
                                    : ''
                            );
                        @endphp

                        <select name="check_in" class="form-select">
                            <option value="">Select Time</option>

                            @for ($h = 0; $h < 24; $h++)
                                @foreach (['00','15','30','45'] as $m)
                                    @php
                                        $time24 = sprintf('%02d:%s', $h, $m);
                                        $time12 = \Carbon\Carbon::createFromFormat('H:i', $time24)->format('g:i A');
                                    @endphp

                                    <option value="{{ $time24 }}"
                                        {{ $selectedCheckIn === $time24 ? 'selected' : '' }}>
                                        {{ $time12 }}
                                    </option>
                                @endforeach
                            @endfor
                        </select>
                    </div> -->

                    {{-- ================= CHECK-OUT ================= --}}
                    <!-- <div class="col-md-6">
                        <label class="form-label fw-semibold">Check-Out Time</label>

                        @php
                            $selectedCheckOut = old(
                                'check_out',
                                isset($Attendances->check_out)
                                    ? \Carbon\Carbon::parse($Attendances->check_out)->format('H:i')
                                    : ''
                            );
                        @endphp

                        <select name="check_out" class="form-select">
                            <option value="">Select Time</option>

                            @for ($h = 0; $h < 24; $h++)
                                @foreach (['00','15','30','45'] as $m)
                                    @php
                                        $time24 = sprintf('%02d:%s', $h, $m);
                                        $time12 = \Carbon\Carbon::createFromFormat('H:i', $time24)->format('g:i A');
                                    @endphp

                                    <option value="{{ $time24 }}"
                                        {{ $selectedCheckOut === $time24 ? 'selected' : '' }}>
                                        {{ $time12 }}
                                    </option>
                                @endforeach
                            @endfor
                        </select>
                    </div> -->

                    <div class="col">
                    <label for="check_in" class="form-label">Check-In Time</label>
                    <input type="time" name="check_in" id="check_in" value="{{ old('check_in', isset($Attendances) ? \Carbon\Carbon::parse($Attendances->check_in)->format('H:i') : '') }}" class="form-control @error('check_in') is-invalid @enderror" required>
                    @error('check_in')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col">
                    <label for="check_out" class="form-label">Check-Out Time</label>
                    <input type="time" name="check_out" id="check_out" value="{{ old('check_out', isset($Attendances) ? \Carbon\Carbon::parse($Attendances->check_out)->format('H:i') : '') }}" class="form-control @error('check_out') is-invalid @enderror" >
                    @error('check_out')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <button type="submit" class="btn btn-dark px-4 rounded-pill">
                <i class="bi bi-check2-circle me-1"></i> Save Attendance
            </button>
        </form>
    </div>
</div>
@endsection