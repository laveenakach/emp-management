@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="col-lg-10 mx-auto">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-primary">Employee Attendance</h4>
            <a href="{{ route('employer.attendance') }}" class="btn btn-outline-secondary rounded-pill">
                <i class="bi bi-arrow-left"></i> Back to List
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
                <label for="employee_id" class="form-label">Select Employee</label>
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
                <label for="date" class="form-label">Date</label>
                <input type="date" name="date" id="date"
                    value="{{ old('date', $Attendances->date ?? '') }}"
                    class="form-control @error('date') is-invalid @enderror" required>
                @error('date')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row mb-3">
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