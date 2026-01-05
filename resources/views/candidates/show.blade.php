@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card shadow rounded">
        <div class="card-header text-white d-flex justify-content-between align-items-center" style="background-color: rgb(26 24 57) !important;">
            <h4 class="mb-0">Candidate Details</h4>
            <a href="{{ route('candidates.index') }}" class="btn btn-light btn-sm">← Back to List</a>
        </div>

        <div class="card-body">
            <table class="table table-bordered table-striped">
                 <tr>
                    <th width="30%">Candidate ID</th>
                    <td>{{ $candidate->candidate_id }}</td>
                </tr>
                <tr>
                    <th>Name</th>
                    <td>{{ $candidate->name }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $candidate->email }}</td>
                </tr>
                <tr>
                    <th>Phone</th>
                    <td>{{ $candidate->phone }}</td>
                </tr>
                <tr>
                    <th>Address</th>
                    <td>{{ $candidate->address }}</td>
                </tr>
                <tr>
                    <th>GST Number</th>
                    <td>{{ $candidate->gst_number }}</td>
                </tr>
                <tr>
                    <th>Bank Account</th>
                    <td>{{ $candidate->bank_account_number }}</td>
                </tr>
                <tr>
                    <th>IFSC Code</th>
                    <td>{{ $candidate->ifsc_code }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>
@endsection
