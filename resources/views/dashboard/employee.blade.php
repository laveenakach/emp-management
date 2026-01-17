@extends('layouts.app')

@section('content')
<style>
    .dashboard-card {
        transition: transform 0.2s ease-in-out;
        min-height: 100px;
        border: none;
        border-radius: 1rem;
    }

    .dashboard-card:hover {
        transform: scale(1.03);
    }

    .dashboard-icon {
        transition: transform 0.3s ease;
    }

    .dashboard-card:hover .dashboard-icon {
        transform: rotate(10deg) scale(1.1);
    }

    .notification-card {
        border-left: 5px solid #0d6efd;
        border-radius: 1rem;
        background-color: #f8f9fa;
        transition: 0.3s ease-in-out;
    }

    .notification-card:hover {
        background-color: #e9f2ff;
        transform: scale(1.01);
    }

    .notification-title {
        font-weight: bold;
        font-size: 1rem;
    }

    .notification-date {
        font-size: 0.85rem;
    }

    .notification-body {
        font-size: 0.95rem;
    }
</style>

<div class="container mt-4">
    <!-- Profile Section -->
    <div class="row align-items-center mb-4">
        <div class="col-md-2 text-center">
            <img src="{{ asset('uploads/profile_photos/' . auth()->user()->photo) }}"
                alt="Profile Photo"
                class="rounded-circle shadow"
                style="width: 100px; height: 100px; object-fit: cover;">
        </div>
        <div class="col-md-10">
            <h3>Welcome, {{ auth()->user()->name }} 👋</h3>
            <p class="text-muted">Here's your dashboard overview.</p>
        </div>
    </div>

    <!-- Dashboard Cards -->
    <div class="row g-3">
        <div class="col-md-3 col-sm-6">
            <a href="{{ auth()->user()->role == 'employee' ? route('employee.attendance') : route('team_leader.attendance') }}" class="text-decoration-none">
                <div class="card text-white shadow dashboard-card" style="background: linear-gradient(to right, #2c3e50, #4ca1af);">
                    <div class="card-body d-flex align-items-center p-3">
                        <i class="bi bi-calendar-check fs-1 me-3 dashboard-icon"></i>
                        <div>
                            <h6 class="mb-1">My Attendance</h6>
                            <h4 class="mb-0 counter" data-count="{{ $myAttendances }}">0</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-3 col-sm-6">
            <a href="{{ auth()->user()->role == 'employee' ? route('employee.salary-slips.index') : route('team_leader.salary_slips.index') }}" class="text-decoration-none">
                <div class="card text-white shadow dashboard-card" style="background: linear-gradient(to right, #0f2027, #203a43, #2c5364);">
                    <div class="card-body d-flex align-items-center p-3">
                        <i class="bi bi-cash-stack fs-1 me-3 dashboard-icon"></i>
                        <div>
                            <h6 class="mb-1">Salary Slips</h6>
                            <h4 class="mb-0 counter" data-count="{{ $salarySlips }}">0</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-3 col-sm-6">
            <a href="{{ route('employee.leaves.index') }}" class="text-decoration-none">
                <div class="card text-white shadow dashboard-card" style="background: linear-gradient(to right, #355c7d, #203a43, #2c5364);">
                    <div class="card-body d-flex align-items-center p-3">
                        <i class="bi bi-envelope-open fs-1 me-3 dashboard-icon"></i>
                        <div>
                            <h6 class="mb-1">Leave Requests</h6>
                            <h4 class="mb-0 counter" data-count="{{ $Leavesrequest }}">0</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-3 col-sm-6">
            <a href="{{ route('letters.index') }}" class="text-decoration-none" >
                <div class="card text-white shadow dashboard-card" style="background: linear-gradient(to right, #42275a, #734b6d);">
                    <div class="card-body d-flex align-items-center p-3">
                        <i class="bi bi-envelope-plus fs-1 me-3 dashboard-icon"></i>
                        <div>
                            <h6 class="mb-1">Offer/Appointment letters</h6>
                            <h4 class="mb-0 counter" data-count="{{ $letters }}">0</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-3 col-sm-6">
            <a href="{{ route('tasks.index') }}" class="text-decoration-none">
                <div class="card text-white shadow dashboard-card" style="background: linear-gradient(to right, #1e3c72, #2a5298);">
                    <div class="card-body d-flex align-items-center p-3">
                        <i class="bi bi-card-checklist fs-1 me-3 dashboard-icon"></i>
                        <div>
                            <h6 class="mb-1">Task Assigned</h6>
                            <h4 class="mb-0 counter" data-count="{{ $taskManagement }}">0</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-3 col-sm-6">
            <a href="{{ route('tasks.submitedtask') }}" class="text-decoration-none" >
                <div class="card text-white shadow dashboard-card" style="background: linear-gradient(to right, #141e30, #243b55);">
                    <div class="card-body d-flex align-items-center p-3">
                        <i class="bi bi-check2-square fs-1 me-3 dashboard-icon"></i>
                        <div>
                            <h6 class="mb-1">Task Submitted</h6>
                            <h4 class="mb-0 counter" data-count="{{ $taskSubmited }}">0</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Notifications Section -->
    <div class="mt-5">
        <h4 class="mb-4 text-primary">
            <i class="bi bi-bell-fill me-2"></i>Latest Notifications
        </h4>
        @forelse($notifications as $notification)
        <div class="p-3 mb-3 shadow-sm notification-card">
            <div class="d-flex justify-content-between align-items-start mb-1">
                <span class="notification-title text-dark">
                    <i class="bi bi-megaphone-fill text-primary me-1"></i>{{ $notification->title }}
                </span>
                <span class="notification-date text-muted">
                    <i class="bi bi-calendar-event me-1"></i>{{ \Carbon\Carbon::parse($notification->date)->format('d M Y') }}
                </span>
            </div>
            <div class="notification-body text-secondary mb-2">
                {{ $notification->description }}
            </div>
            @if($notification->attachment)
            <a href="{{ asset($notification->attachment) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill">
                <i class="bi bi-paperclip me-1"></i> View Attachment
            </a>
            @endif
        </div>
        @empty
        <div class="alert alert-info">No notifications at the moment.</div>
        @endforelse

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $notifications->links('pagination::bootstrap-5') }}
        </div>
    </div>

</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const counters = document.querySelectorAll('.counter');
        counters.forEach(counter => {
            const updateCount = () => {
                const target = +counter.getAttribute('data-count');
                const count = +counter.innerText;
                const increment = Math.ceil(target / 30);
                if (count < target) {
                    counter.innerText = count + increment;
                    setTimeout(updateCount, 20);
                } else {
                    counter.innerText = target;
                }
            };
            updateCount();
        });
    });
</script>
@endsection