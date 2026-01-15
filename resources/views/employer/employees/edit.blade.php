@extends('layouts.app')

@section('content')
<style>
input.form-control,
textarea.form-control,
select.form-control {
    border: 1px solid #333 !important; /* dark gray / black */
    border-radius: 0.5rem; /* optional rounded corners */
    background-color: #fff; /* optional */
}

</style>
<div class="container d-flex justify-content-center mt-2">
    <div class="col-md-8">

        <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold text-primary">Edit Employee</h2>
                <a href="javascript:void(0);" onclick="window.history.back();" class="btn btn-outline-secondary rounded-pill">
                    <i class="bi bi-arrow-left"></i> Back to List
                </a>
        </div>

        <div class="card shadow rounded-2">
            <div class="card-body">
                <form action="{{ route('employer.employees.update', $employee->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <!-- <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" name="name" id="name" value="{{ $employee->name }}" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" name="email" id="email" value="{{ $employee->email }}" required>
            </div> -->

                    <!-- <div class="mb-3">
                <label for="department_id" class="form-label">Department</label>
                <select name="department_id" id="department_id" class="form-control" required>
                    <option value="">-Select-
                    </option>
                    @foreach ($departments as $department)
                    <option value="{{ $department->id }}" {{ $employee->department_id == $department->id ? 'selected' : '' }}>
                        {{ $department->name }}
                    </option>
                    @endforeach
                </select>
            </div> -->

                    <div class="row">
                        <!-- Employee Full Name -->
                        <div class="mb-3 col-md-6">
                            <label for="name" class="form-label fw-semibold">
                                Employee Full Name <span class="text-danger">*</span>
                            </label>
                            <input id="name" name="name" type="text" value="{{ old('name', $employee->name) }}"
                                class="form-control @error('name') is-invalid @enderror" required autofocus>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-3 col-md-6">
                            <label for="email" class="form-label fw-semibold">
                                Email Address <span class="text-danger">*</span>
                            </label>
                            <input id="email" name="email" type="email" value="{{ old('email', $employee->email) }}"
                                class="form-control @error('email') is-invalid @enderror" required>
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Mobile Number -->
                        <div class="mb-3 col-md-6">
                            <label for="mobile_no" class="form-label fw-semibold">
                                Mobile Number (Bank Linked No.) <span class="text-danger">*</span>
                            </label>
                            <input id="mobile_no" name="mobile_no" type="text" value="{{ old('mobile_no', $employee->mobile_no) }}"
                                class="form-control @error('mobile_no') is-invalid @enderror" required>
                            @error('mobile_no')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Year of Experience -->
                        <div class="mb-3 col-md-6">
                            <label for="experience" class="form-label fw-semibold">
                                Year of Experience <span class="text-danger">*</span>
                            </label>
                            <input id="experience" name="experience" type="text" value="{{ old('experience', $employee->experience) }}"
                                class="form-control @error('experience') is-invalid @enderror" required>
                            @error('experience')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Upload Image -->
                        <div class="mb-3 col-md-6">
                            <label for="photo" class="form-label fw-semibold">
                                Upload Photo <span class="text-muted">(Max upload 5MB)</span>
                            </label>

                            @if($employee->photo)
                            <!-- Show existing uploaded photo -->
                            <div class="mb-2">
                                <img src="{{ asset('uploads/profile_photos/' . $employee->photo) }}"
                                    alt="Profile Photo"
                                    class="img-thumbnail"
                                    style="width: 100px; height: 100px; object-fit: cover; cursor: pointer;"
                                    data-bs-toggle="modal"
                                    data-bs-target="#photoModal">
                            </div>

                            <!-- Bootstrap Modal for showing big image -->
                            <div class="modal fade" id="photoModal" tabindex="-1" aria-labelledby="photoModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="photoModalLabel">Profile Photo</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-center">
                                            <img src="{{ asset('uploads/profile_photos/' . $employee->photo) }}"
                                                alt="Profile Photo"
                                                class="img-fluid">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Upload new photo -->
                            <input id="photo" name="photo" type="file"
                                class="form-control @error('photo') is-invalid @enderror" accept="image/*">
                            @error('photo')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- City -->
                        <div class="mb-3 col-md-6">
                            <label for="city" class="form-label fw-semibold">
                                City <span class="text-danger">*</span>
                            </label>
                            <input id="city" name="city" type="text" value="{{ old('city', $employee->city) }}"
                                class="form-control @error('city') is-invalid @enderror" required>
                            @error('city')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Location -->
                        <div class="mb-3 col-md-6">
                            <label for="location" class="form-label fw-semibold">
                                Location <span class="text-danger">*</span>
                            </label>
                            <input id="location" name="location" type="text" value="{{ old('location', $employee->location) }}"
                                class="form-control @error('location') is-invalid @enderror" required>
                            @error('location')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div class="mb-3 col-md-6">
                            <label for="address" class="form-label fw-semibold">
                                Address <span class="text-danger">*</span>
                            </label>
                            <textarea id="address" name="address" class="form-control @error('address') is-invalid @enderror" rows="3" required>{{ old('address', $employee->address) }}</textarea>
                            @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Aadhar Card -->
                        <div class="mb-3 col-md-6">
                            <label for="aadhar_card" class="form-label fw-semibold">
                                Aadhar Card Number <span class="text-danger">*</span>
                            </label>
                            <input id="aadhar_card" name="aadhar_card" type="text" value="{{ old('aadhar_card', $employee->aadhar_card) }}"
                                class="form-control @error('aadhar_card') is-invalid @enderror" required>
                            @error('aadhar_card')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- PAN Card -->
                        <div class="mb-3 col-md-6">
                            <label for="pan_card" class="form-label fw-semibold">
                                PAN Card Number <span class="text-danger">*</span>
                            </label>
                            <input id="pan_card" name="pan_card" type="text" value="{{ old('pan_card', $employee->pan_card) }}"
                                class="form-control @error('pan_card') is-invalid @enderror" required>
                            @error('pan_card')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Bank Account Number -->
                        <div class="mb-3 col-md-6">
                            <label for="bank_account" class="form-label fw-semibold">
                                Bank Account Number <span class="text-danger">*</span>
                            </label>
                            <input id="bank_account" name="bank_account" type="text" value="{{ old('bank_account', $employee->bank_account) }}"
                                class="form-control @error('bank_account') is-invalid @enderror">
                            @error('bank_account')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- IFSC Code -->
                        <div class="mb-3 col-md-6">
                            <label for="ifsc_code" class="form-label fw-semibold">
                                IFSC Code <span class="text-danger">*</span>
                            </label>
                            <input id="ifsc_code" name="ifsc_code" type="text" value="{{ old('ifsc_code', $employee->ifsc_code) }}"
                                class="form-control @error('ifsc_code') is-invalid @enderror">
                            @error('ifsc_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Designation -->
                        <!-- <div class="mb-3 col-md-6">
                            <label for="designation" class="form-label">
                                Designation <span class="text-danger">*</span>
                            </label>
                            <input id="designation" name="designation" type="text" value="{{ old('designation', $employee->designation) }}"
                                class="form-control @error('designation') is-invalid @enderror" required>
                            @error('designation')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div> -->

                        <!-- <div class="mb-3 col-md-6">
                            <label class="form-label">Department <span class="text-danger">*</span></label>
                            <select name="department_id" id="department" class="form-control" required>
                                <option value="">-- Select Department --</option>
                                @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ $employee->department_id == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('department_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3 col-md-6">
                            <label class="form-label">Designation <span class="text-danger">*</span></label>
                            <select name="designation_id" id="designation" class="form-control" required>
                                <option value="">-- Select Designation --</option>
                                {{-- Designations will be loaded by JavaScript --}}
                            </select>
                            @error('designation_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div> -->

                          <!-- Department -->
                        <div class="col-md-6">
                            <label for="Department" class="form-label fw-semibold">Department <span class="text-danger">*</span></label>
                            <input type="text" id="department" name="department" class="form-control @error('department') is-invalid @enderror"
                                value="{{ old('ifsc_code', $employee->department) }}" required>
                            @error('department') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Designation -->
                        <div class="col-md-6">
                            <label for="designation" class="form-label fw-semibold">Designation <span class="text-danger">*</span></label>
                            <input type="text" id="designation_id" name="designation_id" class="form-control @error('designation_id') is-invalid @enderror"
                                value="{{ old('ifsc_code', $employee->designation_id) }}" required>
                            @error('designation_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>


                        <!-- Set New Password -->
                        <div class="col-md-6 position-relative">
                            <label for="password" class="form-label fw-semibold">Set New Password</label>
                            <div class="input-group">
                                <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror">
                                <span class="input-group-text">
                                    <i class="fas fa-eye toggle-password" toggle="#password" style="cursor: pointer;"></i>
                                </span>
                            </div>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 position-relative">
                            <label for="password_confirmation" class="form-label fw-semibold">Confirm Password</label>
                            <div class="input-group">
                                <input type="password" id="password_confirmation" name="password_confirmation" value=""  class="form-control">
                               
                                <span class="input-group-text">
                                    <i class="fas fa-eye toggle-password" toggle="#password_confirmation" style="cursor: pointer;"></i>
                                </span>
                            </div>
                        </div>

                        <!-- <div class="mb-3 col-md-6">
                            <label for="department_id" class="form-label fw-semibold">Department <span class="text-danger">*</span></label>
                            <select name="department_id" id="department_id" class="form-select" required>
                                <option value="">-- Select Department --</option>
                                @foreach ($departments as $department)
                                <option value="{{ $department->id }}"
                                    @if(isset($employee) && $employee->department_id == $department->id) selected @endif>
                                    {{ $department->name }}
                                </option>
                                @endforeach
                            </select>
                        </div> -->

                    </div>

                    <button type="submit" class="btn btn-primary mt-2">Update Employee</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function () {
        const selectedDept = $('#department').val();
        const selectedDesig = "{{ $employee->designation_id }}";

        // Load designations on page load if department is selected
        if (selectedDept) {
            loadDesignations(selectedDept, selectedDesig);
        }

        // Load designations on department change
        $('#department').on('change', function () {
            const deptId = $(this).val();
            loadDesignations(deptId, null);
        });

        function loadDesignations(deptId, selectedId = null) {
            if (deptId) {
                $.ajax({
                    url: '/get-designations/' + deptId,
                    type: 'GET',
                    success: function (data) {
                        $('#designation').empty().append('<option value="">-- Select Designation --</option>');
                        $.each(data, function (key, value) {
                            $('#designation').append('<option value="' + value.id + '"' +
                                (value.id == selectedId ? ' selected' : '') +
                                '>' + value.name + '</option>');
                        });
                    }
                });
            } else {
                $('#designation').empty().append('<option value="">-- Select Designation --</option>');
            }
        }
    });
</script>


<script>
    document.querySelectorAll('.toggle-password').forEach(function(icon) {
        icon.addEventListener('click', function() {
            const input = document.querySelector(this.getAttribute('toggle'));
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    });
</script>
@endsection