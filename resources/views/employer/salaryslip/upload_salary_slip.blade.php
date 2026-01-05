@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <!-- <div class="text-center mb-4">
                <h2 class="fw-bold text-primary">📄 Upload or Generate Salary Slip</h2>
                <p class="text-muted">Upload a PDF salary slip or generate it automatically based on attendance.</p>
            </div> -->

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold text-primary">📄 Upload or Generate Salary Slip</h2>

                <a href="javascript:void(0);" onclick="window.history.back();" class="btn btn-outline-secondary rounded-pill">
                    <i class="bi bi-arrow-left"></i> Back to List
                </a>

            </div>
            <p class="text-muted">Upload a PDF salary slip or generate it automatically based on attendance.</p>


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
                    <form method="POST" action="{{ $url }}" enctype="multipart/form-data">
                        @csrf
                        @if(isset($SalarySlip)) @method('PUT') @endif

                        <div class="mb-3">
                            <label for="employee_id" class="form-label fw-semibold">👤 Select Employee <span class="text-danger">*</span></label>
                            <select name="employee_id" id="employee_id" class="form-select rounded-pill" required>
                                <option value="">-- Choose Employee --</option>
                                @foreach($employees as $employee)
                                <option value="{{ $employee->id }}"
                                    {{ old('employee_id', $SalarySlip->employee_id ?? '') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->name }} ({{ $employee->empuniq_id }})
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="month" class="form-label fw-semibold">🗓 Salary Month <span class="text-danger">*</span></label>
                            <input type="month" name="month" id="month"
                                value="{{ old('month', isset($SalarySlip) ? $SalarySlip->month : '') }}"
                                class="form-control rounded-pill" required>
                        </div>

                        

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="auto_generate" name="auto_generate" value="1">
                            <label class="form-check-label" for="auto_generate">🤖 Auto Generate Salary Slip</label>
                        </div>

                        <div class="mb-3 col-md-12" id="salary_field">
                            <label for="salary" class="form-label fw-semibold">Salary<span class="text-danger">*</span></label>
                            <input type="text" name="salary" id="salary" placeholder="Enter salary"
                                value="{{ old('salary', isset($SalarySlip) ? $SalarySlip->salary : '') }}"
                                class="form-control rounded-pill" >
                        </div>

                        <div id="pdf_upload_section">
                            <div class="mb-3 col-md-12">
                                <label for="pdf_file" class="form-label">📎 Upload PDF (optional, Max 5MB)</label>
                                <input type="file" id="pdf_file" name="pdf_file" class="form-control" accept="application/pdf">
                                @error('pdf_file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                @if(isset($SalarySlip->file_path))
                                <div class="mt-2">
                                    <a href="{{ asset($SalarySlip->file_path) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-paperclip me-1"></i> View Current File
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-success rounded-pill px-5 py-2">
                                <i class="bi bi-upload me-1"></i> Save Slip
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const autoGenerate = document.getElementById('auto_generate');
    const pdfUpload = document.getElementById('pdf_upload_section');
    const salary_field = document.getElementById('salary_field');

    autoGenerate.addEventListener('change', () => {
        if (autoGenerate.checked) {
            pdfUpload.style.display = 'none';
        } else {
            pdfUpload.style.display = 'block';
        }
    });

    // Initialize on page load
    window.addEventListener('load', () => {
        if (autoGenerate.checked) {
            pdfUpload.style.display = 'none';
        } else {
            salary_field.style.display = 'none';
        }
    });

    autoGenerate.addEventListener('change', () => {
        if (autoGenerate.checked) {
            salary_field.style.display = 'block';
        } else {
            salary_field.style.display = 'none';
        }
    });
</script>
@endsection