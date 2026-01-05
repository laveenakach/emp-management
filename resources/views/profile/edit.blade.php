@extends('layouts.app')

@section('title', 'Update Profile')

@section('content')

<!-- CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.min.css" />

<style>
    @media (min-width: 992px) {
        input.respmobile {
            width: 480px;
        }
    }
</style>

<div class="container mt-3">
    <div class="col-lg-10 mx-auto">
        <h2 class="mb-4 text-center">Update Profile</h2>

        <!-- Toasts -->
        <div class="toast-container position-fixed top-0 end-0 p-3">
            @if (session('status'))
                <div class="toast show align-items-center text-white bg-success" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">{{ session('status') }}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="toast show align-items-center text-white bg-danger" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            @endif
        </div>

        <div class="card shadow rounded-3">
            <div class="card-body">
                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    <div class="row">
                        <!-- Name -->
                        <div class="mb-3 col-12 col-md-6">
                            <label for="name" class="form-label">Employee Full Name <span class="text-danger">*</span></label>
                            <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', auth()->user()->name) }}" required autofocus>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-3 col-12 col-md-6">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email', auth()->user()->email) }}" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Mobile -->
                        <div class="mb-3 col-12 col-md-6">
                            <label for="mobile_no" class="form-label">Mobile Number (Bank Linked No.) <span class="text-danger">*</span></label>
                            <input id="mobile_no" name="mobile_no" type="text" class="form-control @error('mobile_no') is-invalid @enderror respmobile"
                                value="{{ old('mobile_no', auth()->user()->mobile_no) }}" maxlength="11" placeholder="Enter at least 10 digit number" required>
                            @error('mobile_no') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Experience -->
                        <div class="mb-3 col-12 col-md-6">
                            <label for="experience" class="form-label">Year of Experience <span class="text-danger">*</span></label>
                            <input id="experience" name="experience" type="text" class="form-control @error('experience') is-invalid @enderror"
                                value="{{ old('experience', auth()->user()->experience) }}" required>
                            @error('experience') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Upload Photo -->
                        <div class="mb-3 col-12 col-md-6">
                            <label for="photo" class="form-label">Upload Photo <span class="text-muted">(Max 5MB)</span></label>
                            @if(auth()->user()->photo)
                                <div class="mb-2">
                                    <img src="{{ asset('uploads/profile_photos/' . auth()->user()->photo) }}"
                                        alt="Profile Photo"
                                        class="img-thumbnail"
                                        style="width: 100px; height: 100px; object-fit: cover; cursor: pointer;"
                                        data-bs-toggle="modal"
                                        data-bs-target="#photoModal">
                                </div>

                                <!-- Modal -->
                                <div class="modal fade" id="photoModal" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Profile Photo</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-center">
                                                <img src="{{ asset('uploads/profile_photos/' . auth()->user()->photo) }}" class="img-fluid">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <input id="photo" name="photo" type="file" class="form-control @error('photo') is-invalid @enderror" accept="image/*">
                            @error('photo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- City -->
                        <div class="mb-3 col-12 col-md-6">
                            <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                            <input id="city" name="city" type="text" class="form-control @error('city') is-invalid @enderror"
                                value="{{ old('city', auth()->user()->city) }}" required>
                            @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Location -->
                        <div class="mb-3 col-12 col-md-6">
                            <label for="location" class="form-label">Location <span class="text-danger">*</span></label>
                            <input id="location" name="location" type="text" class="form-control @error('location') is-invalid @enderror"
                                value="{{ old('location', auth()->user()->location) }}" required>
                            @error('location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Address -->
                        <div class="mb-3 col-6">
                            <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                            <textarea id="address" name="address" rows="3"
                                class="form-control @error('address') is-invalid @enderror"
                                required>{{ old('address', auth()->user()->address) }}</textarea>
                            @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Aadhar Card -->
                        <div class="mb-3 col-12 col-md-6">
                            <label for="aadhar_card" class="form-label">Aadhar Card Number <span class="text-danger">*</span></label>
                            <input id="aadhar_card" name="aadhar_card" type="text" maxlength="12"
                                class="form-control @error('aadhar_card') is-invalid @enderror"
                                value="{{ old('aadhar_card', auth()->user()->aadhar_card) }}" required>
                            @error('aadhar_card') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- PAN Card -->
                        <div class="mb-3 col-12 col-md-6">
                            <label for="pan_card" class="form-label">PAN Card Number <span class="text-danger">*</span></label>
                            <input id="pan_card" name="pan_card" type="text" maxlength="10"
                                class="form-control @error('pan_card') is-invalid @enderror"
                                value="{{ old('pan_card', auth()->user()->pan_card) }}" required>
                            @error('pan_card') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Bank Account -->
                        <div class="mb-3 col-12 col-md-6">
                            <label for="bank_account" class="form-label">Bank Account Number <span class="text-danger">*</span></label>
                            <input id="bank_account" name="bank_account" type="text" maxlength="18"
                                class="form-control @error('bank_account') is-invalid @enderror"
                                value="{{ old('bank_account', auth()->user()->bank_account) }}" required>
                            @error('bank_account') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- IFSC Code -->
                        <div class="mb-3 col-12 col-md-6">
                            <label for="ifsc_code" class="form-label">IFSC Code <span class="text-danger">*</span></label>
                            <input id="ifsc_code" name="ifsc_code" type="text" maxlength="18"
                                class="form-control @error('ifsc_code') is-invalid @enderror"
                                value="{{ old('ifsc_code', auth()->user()->ifsc_code) }}" required>
                            @error('ifsc_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Designation -->
                        <div class="mb-3 col-12 col-md-6">
                            <label for="designation" class="form-label">Designation <span class="text-danger">*</span></label>
                            <input id="designation" name="designation" type="text"
                                class="form-control @error('designation') is-invalid @enderror"
                                value="{{ old('designation', auth()->user()->designation) }}" required>
                            @error('designation') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Role (Read Only) -->
                        <div class="mb-3 col-12 col-md-6">
                            <label class="form-label">Role</label>
                            <input type="text" class="form-control" value="{{ ucfirst(auth()->user()->role) }}" disabled>
                        </select>

                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
<script>
    document.getElementById('photo').addEventListener('change', function () {
        const file = this.files[0];
        if (file && file.size > 5 * 1024 * 1024) {
            alert('File size must be less than 5MB.');
            this.value = '';
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        setTimeout(() => {
            let toast = new bootstrap.Toast(document.querySelector('.toast'));
            toast.hide();
        }, 7000);

        const input = document.querySelector("#mobile_no");
        const iti = window.intlTelInput(input, {
            initialCountry: "in",
            nationalMode: false,
            formatOnDisplay: true,
            separateDialCode: true,
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"
        });

        input.form.addEventListener("submit", function (e) {
            const number = iti.getNumber().replace(/\D/g, '');
            if (number.length < 10) {
                e.preventDefault();
                alert("Please enter a valid mobile number with at least 10 digits.");
                input.focus();
            }
        });
    });
</script>

@endsection
