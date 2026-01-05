@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card shadow rounded">
        <div class="card-header text-white d-flex justify-content-between align-items-center" style="background-color: rgb(26 24 57) !important;">
            <h4 class="mb-0">Client Details</h4>
            <a href="{{ route('accounts.clients.index') }}" class="btn btn-light btn-sm">← Back to List</a>
        </div>

        <div class="card-body">
            <table class="table table-bordered table-striped">
                <tr>
                    <th width="30%">Client Unique ID</th>
                    <td>{{ $client->CLTuniq_id }}</td>
                </tr>
                <tr>
                    <th>Name</th>
                    <td>{{ $client->name }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $client->email }}</td>
                </tr>
                <tr>
                    <th>Phone</th>
                    <td>{{ $client->phone }}</td>
                </tr>
                <tr>
                    <th>Address</th>
                    <td>{{ $client->address }}</td>
                </tr>
                <tr>
                    <th>GSTIN</th>
                    <td>{{ $client->gstin }}</td>
                </tr>
                <tr>
                    <th>Bank Account</th>
                    <td>{{ $client->bank_account }}</td>
                </tr>
                <tr>
                    <th>IFSC Code</th>
                    <td>{{ $client->ifsc_code }}</td>
                </tr>
                <tr>
                    <th>Project Requirement (PDF)</th>
                    <td>
                        @if($client->project_requirement)
                            <a href="{{ asset($client->project_requirement) }}" class="btn btn-sm btn-outline-info" target="_blank">
                                <i class="bi bi-file-earmark-pdf"></i> View PDF
                            </a>
                        @else
                            <span class="text-muted">Not Uploaded</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
@endsection
