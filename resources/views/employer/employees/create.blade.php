@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="col-lg-10 mx-auto">

        <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold text-primary">Add New Employee</h2>
                <a href="javascript:void(0);" onclick="window.history.back();" class="btn btn-outline-secondary rounded-pill">
                    <i class="bi bi-arrow-left"></i> Back to List
                </a>
        </div>

        <div class="card shadow rounded-3 border-0">
            <div class="card-body p-4">
                <form action="{{ route('employer.employees.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <!-- Name -->
                        <div class="col-md-6">
                            <label for="name" class="form-label">Employee Full Name <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name') }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email') }}" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Mobile -->
                        <div class="col-md-6">
                            <label for="mobile_no" class="form-label">Mobile Number (Bank Linked) <span class="text-danger">*</span></label>
                            <input type="text" id="mobile_no" name="mobile_no" class="form-control @error('mobile_no') is-invalid @enderror" minlength="10" maxlength="11"
                                value="{{ old('mobile_no') }}" required>
                            @error('mobile_no') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Experience -->
                        <div class="col-md-6">
                            <label for="experience" class="form-label">Years of Experience <span class="text-danger">*</span></label>
                            <input type="text" id="experience" name="experience" class="form-control @error('experience') is-invalid @enderror"
                                value="{{ old('experience') }}" required>
                            @error('experience') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Upload Photo -->
                        <div class="col-md-6">
                            <label for="photo" class="form-label">Upload Photo <span class="text-muted">(Max 5MB)</span></label>
                            <input type="file" id="photo" name="photo" class="form-control @error('photo') is-invalid @enderror" accept="image/*">
                            @error('photo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Modal for photo -->
                        @if(auth()->user()->photo)
                        <div class="modal fade" id="photoModal" tabindex="-1" aria-labelledby="photoModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Profile Photo</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-center">
                                        <img src="{{ asset('uploads/profile_photos/' . auth()->user()->photo) }}" class="img-fluid" alt="Large Photo">
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Location & City -->
                        <div class="col-md-6">
                            <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                            <input type="text" id="city" name="city" class="form-control @error('city') is-invalid @enderror"
                                value="{{ old('city') }}" required>
                            @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="location" class="form-label">Location <span class="text-danger">*</span></label>
                            <input type="text" id="location" name="location" class="form-control @error('location') is-invalid @enderror"
                                value="{{ old('location') }}" required>
                            @error('location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Address -->
                        <div class="col-md-6">
                            <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                            <textarea id="address" name="address" class="form-control @error('address') is-invalid @enderror"
                                rows="3" required>{{ old('address') }}</textarea>
                            @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Aadhar / PAN -->
                        <div class="col-md-6">
                            <label for="aadhar_card" class="form-label">Aadhar Card Number <span class="text-danger">*</span></label>
                            <input type="text" id="aadhar_card" name="aadhar_card" class="form-control @error('aadhar_card') is-invalid @enderror" maxlength="12"
                                value="{{ old('aadhar_card') }}" required>
                            @error('aadhar_card') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="pan_card" class="form-label">PAN Card Number <span class="text-danger">*</span></label>
                            <input type="text" id="pan_card" name="pan_card"
                                class="form-control @error('pan_card') is-invalid @enderror"
                                maxlength="10" value="{{ old('pan_card') }}" required
                                pattern="[A-Z]{5}[0-9]{4}[A-Z]{1}" oninput="this.value = this.value.toUpperCase();"
                                title="Enter valid PAN (e.g., ABCDE1234F)">
                            @error('pan_card') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Bank Info -->
                        <div class="col-md-6">
                            <label for="bank_account" class="form-label">Bank Account Number <span class="text-danger">*</span></label>
                            <input type="text" id="bank_account" name="bank_account" class="form-control @error('bank_account') is-invalid @enderror"
                                value="{{ old('bank_account') }}">
                            @error('bank_account') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="ifsc_code" class="form-label">IFSC Code <span class="text-danger">*</span></label>
                            <input type="text" id="ifsc_code" name="ifsc_code" class="form-control @error('ifsc_code') is-invalid @enderror" maxlength="11"
                                value="{{ old('ifsc_code') }}">
                            @error('ifsc_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- <div class="col-md-6">
                            <label class="form-label">Department <span class="text-danger">*</span></label>
                            <select name="department_id" id="department" class="form-control" required>
                                <option value="">-- Select Department --</option>
                                @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                            @error('department_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Designation <span class="text-danger">*</span></label>
                            <select name="designation_id" id="designation" class="form-control" required>
                                <option value="">-- Select Designation --</option>
                            </select>
                            @error('designation_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div> -->

                        <!-- Department -->
                        <div class="col-md-6">
                            <label for="Department" class="form-label">Department <span class="text-danger">*</span></label>
                            <input type="text" id="department" name="department" class="form-control @error('department') is-invalid @enderror"
                                value="{{ old('department') }}" required>
                            @error('department') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Designation -->
                        <div class="col-md-6">
                            <label for="designation" class="form-label">Designation <span class="text-danger">*</span></label>
                            <input type="text" id="designation_id" name="designation_id" class="form-control @error('designation_id') is-invalid @enderror"
                                value="{{ old('designation_id') }}" required>
                            @error('designation_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Passwords -->
                        <div class="col-md-6 position-relative">
                            <label for="password" class="form-label">Set New Password</label>
                            <div class="input-group">
                                <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror">
                                <span class="input-group-text">
                                    <i class="fas fa-eye toggle-password" toggle="#password" style="cursor: pointer;"></i>
                                </span>
                            </div>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 position-relative">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <div class="input-group">
                                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
                                <span class="input-group-text">
                                    <i class="fas fa-eye toggle-password" toggle="#password_confirmation" style="cursor: pointer;"></i>
                                </span>
                            </div>
                        </div>



                    </div>

                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-success px-4">
                            <i class="bi bi-save me-1"></i> Save Employee
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->

<!-- Include jQuery in your layout before your script -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


<script>
    $(document).ready(function () {
        $('#department').on('change', function () {
            let deptID = $(this).val();
            if (deptID) {
                $.ajax({
                    url: '/get-designations/' + deptID,
                    type: "GET",
                    dataType: 'json',
                    success: function (data) {
                        $('#designation').empty();
                        $('#designation').append('<option value="">-- Select Designation --</option>');

                        $.each(data, function (index, designation) {
                            $('#designation').append(
                                '<option value="' + designation.id + '">' + designation.name + '</option>'
                            );
                        });
                    },
                    error: function () {
                        alert("Something went wrong while fetching designations.");
                    }
                });
            } else {
                $('#designation').empty().append('<option value="">-- Select Designation --</option>');
            }
        });
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

<script>
    document.getElementById('photo').addEventListener('change', function() {
        const file = this.files[0];
        if (file && file.size > 5 * 1024 * 1024) {
            alert('File size must be less than 5MB.');
            this.value = '';
        }
    });
</script>
@endsection