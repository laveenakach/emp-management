@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Today's Attendance</h2>

    @if(session('success'))
    <div id="success-message" class="alert alert-success">
        {{ session('success') }}
    </div>

    <script>
        setTimeout(function() {
            let successMessage = document.getElementById('success-message');
            if (successMessage) {
                successMessage.style.transition = "opacity 1s ease";
                successMessage.style.opacity = 0;
                setTimeout(() => successMessage.style.display = 'none', 1000); // after fade out
            }
        }, 5000);
    </script>
    @endif

    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card p-4 mb-4">
        <p><strong>Date:</strong> {{ \Carbon\Carbon::now()->toFormattedDateString() }}</p>
        <p><strong>Check In Time:</strong> {{ $attendance->check_in ?? 'Not Checked In' }}</p>
        <p><strong>Check Out Time:</strong> {{ $attendance->check_out ?? 'Not Checked Out' }}</p>

        <div class="mt-3">
            @if(!$attendance || !$attendance->check_in)
            <form method="POST" action="{{ route('employee.checkin') }}">
                @csrf
                <button type="submit" class="btn btn-primary">Check In</button>
            </form>
            @elseif(!$attendance->check_out)
            <form method="POST" action="{{ route('employee.checkout') }}">
                @csrf
                <button type="submit" class="btn btn-success">Check Out</button>
            </form>
            @else
            <div class="alert alert-info">You have completed attendance for today!</div>
            @endif
        </div>
    </div>
</div>


<div class="container">
    <h2>Attendance List</h2>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Date</th>
                <th>Chack In</th>
                <th>Chack Out</th>
                <!-- <th>Actions</th> -->
            </tr>
        </thead>
        <tbody>
            @foreach ($attendancelist as $attendance_list)
            <tr>
                <td>{{ $attendance_list->user_name }}</td>
                <td>{{ \Carbon\Carbon::parse($attendance_list->date)->format('d F, Y') }}</td>
                <td>
                    {{ $attendance_list->check_in ? \Carbon\Carbon::parse($attendance_list->check_in)->format('h:i A') : '-' }}
                </td>
                <td>
                    {{ $attendance_list->check_out ? \Carbon\Carbon::parse($attendance_list->check_out)->format('h:i A') : '-' }}
                </td>

                <!-- <td>
                    <div style="display: flex; gap: 5px;">
                        <a href="{{ route('employer.employees.edit', $attendance_list->id) }}" class="btn btn-warning btn-sm">Edit/Assign Department</a>

                        <form action="{{ route('employer.employees.delete', $attendance_list->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </div>
                </td> -->
            </tr>
            @endforeach
        </tbody>
    </table>
</div>


@endsection