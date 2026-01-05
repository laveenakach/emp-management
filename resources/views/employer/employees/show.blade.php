@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card shadow rounded">
        <div class="card-header text-white d-flex justify-content-between align-items-center" style="background-color: rgb(26 24 57) !important;">
            <h4 class="mb-0">Employee Details</h4>
            <a href="{{ route('employer.employees.index') }}" class="btn btn-light btn-sm">← Back to List</a>
        </div>

        <div class="card-body">
                        <!-- Profile Photo -->
            <div class="row mb-4">
                <div class="col-md-2 text-center">
                    @if($user->photo)
                    <img src="{{ asset('uploads/profile_photos/' . $user->photo) }}" class="rounded-circle shadow" style="width: 100px; height: 100px; object-fit: cover;" alt="Profile Photo">
                    @else
                    <img src="{{ asset('default-user.png') }}" class="rounded-circle shadow" style="width: 100px; height: 100px;" alt="Default">
                    @endif
                </div>
                <div class="col-md-10 d-flex align-items-center">
                    <h5 class="mb-0">Hello, <strong>{{ $user->name }}</strong> 👋</h5>
                </div>
            </div>

            <!-- 4-column Table -->
            <table class="table table-bordered table-striped">
                <tr>
                    <th style="width: 15%; background-color: #f8f9fa;">Employee ID</th>
                    <td style="width: 35%;">{{ $user->empuniq_id }}</td>
                    <th style="width: 15%; background-color: #f8f9fa;">Email</th>
                    <td style="width: 35%;">{{ $user->email }}</td>
                </tr>
                <tr>
                    <th style="background-color: #f8f9fa;">Mobile No</th>
                    <td>{{ $user->mobile_no }}</td>
                    <th style="background-color: #f8f9fa;">Experience</th>
                    <td>{{ $user->experience }}</td>
                </tr>
                <tr>
                    <th style="background-color: #f8f9fa;">City</th>
                    <td>{{ $user->city }}</td>
                    <th style="background-color: #f8f9fa;">Location</th>
                    <td>{{ $user->location }}</td>
                </tr>
                <tr>
                    <th style="background-color: #f8f9fa;">Aadhar Card</th>
                    <td>{{ $user->aadhar_card }}</td>
                    <th style="background-color: #f8f9fa;">PAN Card</th>
                    <td>{{ $user->pan_card }}</td>
                </tr>
                <tr>
                    <th style="background-color: #f8f9fa;">Bank Account</th>
                    <td>{{ $user->bank_account }}</td>
                    <th style="background-color: #f8f9fa;">IFSC Code</th>
                    <td>{{ $user->ifsc_code }}</td>
                </tr>
                <tr>
                    <th style="background-color: #f8f9fa;">Department</th>
                    <td>{{ $user->department ?? 'N/A' }}</td>
                    <th style="background-color: #f8f9fa;">Designation</th>
                    <td>{{ $user->designation_id ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th style="background-color: #f8f9fa;">Role</th>
                    <td>{{ ucfirst($user->role) }}</td>
                    <th style="background-color: #f8f9fa;">Address</th>
                    <td>{{ $user->address }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>
@endsection