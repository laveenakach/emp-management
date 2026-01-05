@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<div class="container py-2">
    <div class="col-lg-10 mx-auto">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-primary">Submit a Leave Request</h4>
            <a href="{{ route('employee.leaves.index') }}" class="btn btn-outline-secondary rounded-pill">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>

        <!-- Toast Messages -->
        <div class="toast-container position-fixed top-0 end-0 p-3">
            @if (session('status'))
            <div class="toast show text-white bg-success" role="alert">
                <div class="d-flex">
                    <div class="toast-body">{{ session('status') }}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
            @endif

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

        <div class="card shadow-sm rounded-2">
            <div class="card-body px-4 py-5">
                <form method="POST" action="{{ route('employee.leaves.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-4">
                        <!-- Employee Name -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Employee Name</label>
                            <input type="text" class="form-control" name="employee_name" value="{{ auth()->user()->name }}" readonly>
                        </div>

                        <!-- Department -->
                        <!-- <div class="col-md-6">
                            <label class="form-label fw-semibold">Department <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="department" required>
                        </div> -->

                        <div class="col-md-6">
                            <label class="form-label">Department <span class="text-danger">*</span></label>
                            <select name="department" id="department" class="form-control" required>
                                <option value="">-- Select Department --</option>
                                @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                            @error('department') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Leave Request Date -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Leave Request Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="date" required>
                        </div>

                        <!-- From Date -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">From Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="from_date" required>
                        </div>

                        <!-- To Date -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">To Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="to_date" required>
                        </div>

                        <!-- Leave Duration (Half Day / Full Day) -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Leave Duration <span class="text-danger">*</span></label>
                            <select name="leave_duration" class="form-control" required>
                                <option value="">-- Select --</option>
                                <option value="Full Day">Full Day</option>
                                <option value="Half Day">Half Day</option>
                            </select>
                        </div>

                        <!-- Leave Type -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Leave Type( Select Any One) <span class="text-danger">*</span></label><br>
                            @php
                            $leaveTypes = [
                            'Vacation', 'Personal Reason', 'Illness', 'Family Care', 'Medical Appointment',
                            'Bereavement', 'Maternity Leave', 'Paternity Leave', 'Marriage Leave',
                            'Religious Holiday', 'Work from Home', 'Jury Duty', 'Quarantine', 'Training Leave'
                            ];
                            @endphp

                            @foreach ($leaveTypes as $type)
                            <div class="form-check form-check-inline mb-2">
                                <input class="form-check-input" type="checkbox" name="leave_type[]" value="{{ $type }}" id="{{ Str::slug($type) }}">
                                <label class="form-check-label" for="{{ Str::slug($type) }}">{{ $type }}</label>
                            </div>
                            @endforeach
                        </div>


                        <!-- Other Reason -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Other Reason</label>
                            <textarea name="reason" class="form-control" rows="3" placeholder="Optional"></textarea>
                        </div>

                        <!-- Upload Document -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Supporting Document (PDF/JPG/PNG)</label>
                            <input type="file" class="form-control" name="document" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            <small class="text-muted">Max size: 5MB</small>
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-end">
                        <button type="submit" class="btn btn-success px-4 py-2 rounded-pill">
                            <i class="bi bi-check-circle-fill me-2"></i> Submit Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap Script -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        setTimeout(() => {
            let toasts = document.querySelectorAll('.toast');
            toasts.forEach(toast => new bootstrap.Toast(toast).hide());
        }, 7000);

        document.getElementById('document')?.addEventListener('change', function() {
            const file = this.files[0];
            if (file && file.size > 5 * 1024 * 1024) {
                alert('File size must be under 5MB.');
                this.value = '';
            }
        });
    });
</script>
@endsection