@extends('layouts.app')

@section('content')
<div class="container mt-4">

    {{-- Employee Card --}}
    <div class="card shadow rounded mb-4">
        <div class="card-header text-white d-flex justify-content-between align-items-center"
             style="background-color: rgb(26 24 57) !important;">
            <h4 class="mb-0">Employee Details</h4>
            <a href="{{ route('employer.employees.index') }}" class="btn btn-light btn-sm">
                ← Back to List
            </a>
        </div>

        <div class="card-body">

            {{-- Profile --}}
            <div class="row mb-4">
                <div class="col-md-2 text-center">
                    @if($user->photo)
                        <img src="{{ asset('uploads/profile_photos/' . $user->photo) }}"
                             class="rounded-circle shadow"
                             style="width:100px;height:100px;object-fit:cover;">
                    @else
                        <img src="{{ asset('default-user.png') }}"
                             class="rounded-circle shadow"
                             style="width:100px;height:100px;">
                    @endif
                </div>

                <div class="col-md-10 d-flex align-items-center">
                    <h5 class="mb-0">
                        Hello, <strong>{{ $user->name }}</strong> 👋
                    </h5>
                </div>
            </div>

            {{-- Details Table --}}
            <table class="table table-bordered table-striped">
                <tr>
                    <th width="15%">Employee ID</th>
                    <td width="35%">{{ $user->empuniq_id }}</td>
                    <th width="15%">Email</th>
                    <td width="35%">{{ $user->email }}</td>
                </tr>
                <tr>
                    <th>Mobile</th>
                    <td>{{ $user->mobile_no }}</td>
                    <th>Experience</th>
                    <td>{{ $user->experience }}</td>
                </tr>
                <tr>
                    <th>City</th>
                    <td>{{ $user->city }}</td>
                    <th>Location</th>
                    <td>{{ $user->location }}</td>
                </tr>
                <tr>
                    <th>Aadhar</th>
                    <td>{{ $user->aadhar_card }}</td>
                    <th>PAN</th>
                    <td>{{ $user->pan_card }}</td>
                </tr>
                <tr>
                    <th>Bank Account</th>
                    <td>{{ $user->bank_account }}</td>
                    <th>IFSC</th>
                    <td>{{ $user->ifsc_code }}</td>
                </tr>
                <tr>
                    <th>Department</th>
                    <td>{{ $user->department ?? 'N/A' }}</td>
                    <th>Designation</th>
                    <td>{{ $user->designation_id ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Role</th>
                    <td>{{ ucfirst($user->role) }}</td>
                    <th>Address</th>
                    <td>{{ $user->address }}</td>
                </tr>
            </table>
        </div>
    </div>

    {{-- Performance Chart --}}
    <div class="card shadow rounded mt-5">
    <div class="card-body text-center">

        <h5 class="fw-bold mb-1">Attendance Performance</h5>
        <small class="text-muted d-block mb-4">
            {{ \Carbon\Carbon::create($year, $month)->format('F Y') }}
        </small>

        <div class="d-flex justify-content-center">
            <div style="max-width: 420px; width: 100%;">
                <canvas id="performanceChartCanvas" height="160"></canvas>
            </div>
        </div>

    </div>
</div>


</div>

{{-- Chart JS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const ctx = document
        .getElementById('performanceChartCanvas')
        .getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Present', 'Leave', 'Absent'],
            datasets: [{
                data: [
                    {{ $presentDays }},
                    {{ $leaveDays }},
                    {{ $absentDays }}
                ],
                backgroundColor: [
                    '#28a745',
                    '#ffc107',
                    '#dc3545'
                ],
                borderRadius: 6,
                barThickness: 35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    },
                    grid: {
                        drawBorder: false
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
</script>

@endsection
