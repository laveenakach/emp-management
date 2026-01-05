@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold text-primary">📄 Upload Offer or Appointment Letters</h2>
                <a href="javascript:void(0);" onclick="window.history.back();" class="btn btn-outline-secondary rounded-pill">
                    <i class="bi bi-arrow-left"></i> Back to List
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

            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-4">
                    <form method="POST"
                        action="{{ isset($letter) ? route('letters.update', $letter->id) : route('letters.store') }}"
                        enctype="multipart/form-data">
                        @csrf
                        @if(isset($letter)) @method('PUT') @endif

                        <!-- Select Employee -->
                        <div class="mb-3">
                            <label for="employee_id" class="form-label fw-semibold">👤 Select Employee <span class="text-danger">*</span></label>
                            <select name="employee_id" id="employee_id" class="form-select rounded-pill" required>
                                <option value="">-- Choose Employee --</option>
                                @foreach($employees as $employee)
                                <option value="{{ $employee->id }}"
                                    {{ old('employee_id', $letter->employee_id ?? '') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->name }} ({{ $employee->empuniq_id }})
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Letter Type -->
                        <div class="mb-3">
                            <label for="letter_type" class="form-label fw-semibold">📌 Letter Type <span class="text-danger">*</span></label>
                            <select name="letter_type" id="letter_type" class="form-select rounded-pill" required>
                                <option value="">-- Select Type --</option>
                                <option value="offer" {{ old('letter_type', $letter->letter_type ?? '') == 'offer' ? 'selected' : '' }}>Offer Letter</option>
                                <option value="appointment" {{ old('letter_type', $letter->letter_type ?? '') == 'appointment' ? 'selected' : '' }}>Appointment Letter</option>
                            </select>
                        </div>

                        <!-- Upload File -->
                        <div class="mb-3">
                            <label for="file" class="form-label fw-semibold">📎 Upload PDF <span class="text-danger">*</span></label>
                            <input type="file" name="file" id="file" class="form-control" accept="application/pdf" {{ isset($letter) ? '' : 'required' }}>
                            @if(isset($letter))
                            <small class="text-muted">Current File: <a href="{{ route('letters.download', $letter->id) }}" target="_blank">Download</a></small>
                            @endif
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">📝 Description (optional)</label>
                            <textarea name="description" id="description" class="form-control" rows="3">{{ old('description', $letter->description ?? '') }}</textarea>
                        </div>

                        <!-- Submit -->
                        <div class="text-end">
                            <button type="submit" class="btn btn-success rounded-pill px-5 py-2">
                                <i class="bi bi-upload me-1"></i> Upload Letter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection