@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold text-primary">{{ isset($candidate) ? 'Edit Candidate' : 'Create Candidate' }}</h2>
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
                    <form method="POST" action="{{ $url }}">
                        @csrf
                        @if(isset($candidate)) @method('PUT') @endif

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" value="{{ old('name', $candidate->name ?? '') }}" class="form-control rounded-pill" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label fw-semibold">Email <span class="text-danger"></span></label>
                                <input type="email" name="email" id="email" value="{{ old('email', $candidate->email ?? '') }}" class="form-control rounded-pill">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label fw-semibold">Phone <span class="text-danger">*</span></label>
                                <input type="text" name="phone" id="phone" value="{{ old('phone', $candidate->phone ?? '') }}" class="form-control rounded-pill" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="gst_number" class="form-label fw-semibold">GST Number</label>
                                <input type="text" name="gst_number" id="gst_number" value="{{ old('gst_number', $candidate->gst_number ?? '') }}" class="form-control rounded-pill">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="bank_account_number" class="form-label fw-semibold">Bank Account Number</label>
                                <input type="text" name="bank_account_number" id="bank_account_number" value="{{ old('bank_account_number', $candidate->bank_account_number ?? '') }}" class="form-control rounded-pill">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="ifsc_code" class="form-label fw-semibold">IFSC Code</label>
                                <input type="text" name="ifsc_code" id="ifsc_code" value="{{ old('ifsc_code', $candidate->ifsc_code ?? '') }}" class="form-control rounded-pill">
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="address" class="form-label fw-semibold">Address</label>
                                <textarea name="address" id="address" class="form-control rounded-3" rows="3">{{ old('address', $candidate->address ?? '') }}</textarea>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-success rounded-pill px-5 py-2">
                                <i class="bi bi-save me-1"></i> {{ isset($candidate) ? 'Update' : 'Save' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
