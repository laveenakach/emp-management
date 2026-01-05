@extends('layouts.app')

@section('content')
<style>
    .dashboard-card {
        border: none;
        border-radius: 1rem;
        min-height: 120px;
        color: white;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 25px rgba(0, 0, 0, 0.1);
    }

    .dashboard-icon {
        font-size: 2.5rem;
        opacity: 0.85;
    }

    .counter {
        font-size: 1.5rem;
        font-weight: bold;
    }

    .card-title {
        font-size: 1rem;
        margin-bottom: 0.2rem;
        font-weight: 600;
        opacity: 0.9;
    }
</style>

<div class="container py-4">
    <!-- Greeting -->
    <div class="d-flex align-items-center mb-4">
        <img src="{{ asset('uploads/profile_photos/' . auth()->user()->photo) }}"
            alt="Profile"
            class="rounded-circle shadow"
            style="width: 80px; height: 80px; object-fit: cover; margin-right: 15px;">
        <div>
            <h3 class="mb-0">Welcome, {{ auth()->user()->name }} 👋</h3>
            <p class="text-muted mb-0">Here's a quick look at your system overview</p>
        </div>
    </div>

    <!-- Dashboard Cards -->
    <div class="row g-4">
        @php
        $modules = [
        [
        'label' => 'Employees',
        'route' => 'employer.employees.index',
        'background' => 'background: linear-gradient(to right, #2c3e50, #4ca1af);',
        'icon' => 'bi-people',
        'count' => $employeecount
        ],
        [
        'label' => 'Attendance',
        'route' => 'employer.attendance',
        'background' => 'background: linear-gradient(to right, #0f2027, #203a43, #2c5364);',
        'icon' => 'bi-calendar-check',
        'count' => $myAttendances
        ],
        [
        'label' => 'Leaves',
        'route' => 'employee.leaves.index',
        'background' => 'background: linear-gradient(to right, #355c7d, #6c5b7b, #c06c84);',
        'icon' => 'bi-envelope-open',
        'count' => $Leavesrequest
        ],
        [
        'label' => 'Salary Slips',
        'route' => 'employer.salary_slips.index',
        'background' => 'background: linear-gradient(to right, #42275a, #734b6d);',
        'icon' => 'bi-cash-stack',
        'count' => $salarySlips
        ],
        [
        'label' => 'Tasks',
        'route' => 'tasks.index',
        'background' => 'background: linear-gradient(to right, #1e3c72, #2a5298);',
        'icon' => 'bi-card-checklist',
        'count' => $taskManagement
        ],
        [
        'label' => 'Clients',
        'route' => 'accounts.clients.index',
        'background' => 'background: linear-gradient(to right, #141e30, #243b55);',
        'icon' => 'bi-person-lines-fill',
        'count' => $clients
        ],
        [
        'label' => 'Quotations',
        'route' => 'quotations.index',
        'background' => 'background: linear-gradient(to right, #614385, #516395);',
        'icon' => 'bi-file-earmark-text',
        'count' => $Quotation
        ],
        [
        'label' => 'Billing',
        'route' => 'billings.index',
        'background' => 'background: linear-gradient(to right, #3a1c71, #d76d77, #ffaf7b);',
        'icon' => 'bi-receipt',
        'count' => $Bill
        ],
        [
        'label' => 'Invoices',
        'route' => 'invoices.index',
        'background' => 'background: linear-gradient(to right, #283e51, #485563);',
        'icon' => 'bi-file-earmark-ruled',
        'count' => $Invoice
        ],
        ];
        @endphp

        <div class="row g-4">
            @foreach($modules as $module)
            <div class="col-lg-4 col-md-6">
                <a href="{{ route($module['route']) }}" class="text-decoration-none">
                    <div class="card text-white shadow-sm h-100 dashboard-card" style="{{ $module['background'] }} border: none; border-radius: 1rem;">
                        <div class="card-body d-flex align-items-center">
                            <div class="me-3">
                                <i class="bi {{ $module['icon'] }} fs-2"></i>
                            </div>
                            <div>
                                <div class="fs-5 fw-bold">{{ $module['label'] }}</div>
                                <div class="fs-6">Count: {{ $module['count'] }}</div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>


    </div>
</div>

<!-- Animated Counter Script -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const counters = document.querySelectorAll('.counter');
        counters.forEach(counter => {
            const target = +counter.getAttribute('data-count');
            const update = () => {
                let current = +counter.innerText;
                const increment = Math.ceil(target / 30);
                if (current < target) {
                    counter.innerText = current + increment;
                    setTimeout(update, 30);
                } else {
                    counter.innerText = target;
                }
            };
            update();
        });
    });
</script>
@endsection