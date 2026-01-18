<nav class="d-flex flex-column p-3">
    <a href="#" class="d-flex align-items-center mb-4 mb-md-0 me-md-auto text-white text-decoration-none">
        <div style="text-align: center;">
            <img src="{{ asset('Images/cropped-1-1.png') }}" alt="logo" style="height: 70px;">
            <hr>
            @if(auth()->user()->role == 'employee')
            <span class="fs-4">Employee Portal</span>
            @elseif(auth()->user()->role == 'team_leader')
            <span class="fs-4">Team leader Portal</span>
            @elseif(auth()->user()->role == 'manager')
            <span class="fs-4">Manager Portal</span>
            @else
            <span class="fs-4">Employer/Hr Portal</span>
            @endif
        </div>
    </a>

    <hr class="text-white">

    <ul class="nav nav-pills flex-column mb-auto">

        @if(auth()->user()->role == 'employee')
        <li class="nav-item">
            <a href="{{ route('dashboard.employee') }}" class="nav-link {{ request()->is('dashboard') ? 'active' : 'text-white' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="{{ route('employee.attendance') }}" class="nav-link text-white">
                <i class="bi bi-calendar-check"></i> My Attendance
            </a>
        </li>
        <li>
            <a href="{{ route('employee.salary-slips.index') }}" class="nav-link text-white">
                <i class="bi bi-cash-coin"></i> My Salary Slips
            </a>
        </li>
        <li>
            <a href="{{ route('employee.leaves.index') }}" class="nav-link text-white">
                <i class="bi bi-clipboard-data"></i> Leave Requests
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('letters.index') }}" class="nav-link text-white">
                <i class="bi bi-envelope-plus"></i> Offer/Appointment letters
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link text-white d-flex justify-content-between align-items-center"
                data-bs-toggle="collapse"
                href="#taskSubMenu"
                role="button"
                aria-expanded="false"
                aria-controls="taskSubMenu">
                <span><i class="bi bi-card-checklist me-2"></i> Task Management</span>
                <i class="bi bi-chevron-down toggle-icon"></i>
            </a>
            <div class="collapse" id="taskSubMenu">
                <ul class="nav flex-column ms-3 mt-1">
                    <li class="nav-item">
                        <a href="{{ route('tasks.index') }}" class="nav-link text-white">
                            <i class="bi bi-list-check me-2"></i>All Tasks
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('tasks.submitedtask') }}" class="nav-link text-white">
                            <i class="bi bi-check2-square"></i> Submitted Task
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        <li>
            <a href="{{ route('notifications.index') }}" class="nav-link text-white">
                <i class="bi bi-bell"></i> Notifications
            </a>
        </li>


        @elseif(auth()->user()->role == 'employer')
        <li class="nav-item">
            <a href="{{ route('dashboard.employer') }}" class="nav-link {{ request()->is('dashboard') ? 'active' : 'text-white' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>


        <li class="nav-item has-submenu">
            <a href="#" class="nav-link text-white">
                <i class="bi bi-people me-2"></i>Employee Management
                <i class="bi bi-chevron-down float-end"></i>
            </a>
            <ul class="submenu collapse">
                <li class="nav-item">
                    <a href="{{ route('employer.employees.index') }}" class="nav-link text-white">
                        <i class="bi bi-people"></i> Employee
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('employer.attendance') }}" class="nav-link text-white">
                        <i class="bi bi-calendar3"></i> Attendance
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('employee.leaves.index') }}" class="nav-link text-white">
                        <i class="bi bi-file-earmark-text"></i> Leaves
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('employer.salary_slips.index') }}" class="nav-link text-white">
                        <i class="bi bi-upload"></i> Upload Salary Slips
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('letters.index') }}" class="nav-link text-white">
                        <i class="bi bi-upload"></i> Upload letters
                    </a>
                </li>
            </ul>
        </li>



        <!-- <li>
            <a href="{{ route('employer.employees.index') }}" class="nav-link text-white">
                <i class="bi bi-people"></i> Employee Management
            </a>
        </li>
        <li>
            <a href="{{ route('employer.attendance') }}" class="nav-link text-white">
                <i class="bi bi-calendar3"></i> Attendance Management
            </a>
        </li>
        <li>
            <a href="{{ route('employee.leaves.index') }}" class="nav-link text-white">
                <i class="bi bi-file-earmark-text"></i> Leaves Management
            </a>
        </li>
        <li>
            <a href="{{ route('employer.salary_slips.index') }}" class="nav-link text-white">
                <i class="bi bi-upload"></i> Upload Salary Slips
            </a>
        </li> -->

        <!-- <li class="nav-item">
            <a class="nav-link text-white d-flex justify-content-between align-items-center"
                data-bs-toggle="collapse"
                href="#taskSubMenu"
                role="button"
                aria-expanded="false"
                aria-controls="taskSubMenu">
                <span><i class="bi bi-card-checklist me-2"></i> Task Management</span>
                <i class="bi bi-chevron-down toggle-icon"></i>
            </a>
            <div class="collapse" id="taskSubMenu">
                <ul class="nav flex-column ms-3 mt-1">
                    <li class="nav-item">
                        <a href="{{ route('tasks.index') }}" class="nav-link text-white">
                            <i class="bi bi-list-check me-2"></i> All Tasks
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('tasks.submitedtask') }}" class="nav-link text-white">
                            <i class="bi bi-check2-square"></i> Submitted Task
                        </a>
                    </li>
                </ul>
            </div>
        </li> -->

        <li class="nav-item has-submenu">
            <a href="#" class="nav-link text-white">
                <i class="bi bi-cash-coin me-2"></i>Task Management
                <i class="bi bi-chevron-down float-end"></i>
            </a>
            <ul class="submenu collapse">
                <li class="nav-item">
                    <a href="{{ route('tasks.index') }}" class="nav-link text-white">
                        <i class="bi bi-list-check me-2"></i> All Tasks
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('tasks.submitedtask') }}" class="nav-link text-white">
                        <i class="bi bi-check2-square"></i> Submitted Task
                    </a>
                </li>
            </ul>
        </li>

        <li class="nav-item has-submenu">
            <a href="#" class="nav-link text-white">
                <i class="bi bi-cash-coin me-2"></i> Accounts Management
                <i class="bi bi-chevron-down float-end"></i>
            </a>
            <ul class="submenu collapse">
                <li>
                    <a href="{{ route('accounts.clients.index') }}" class="nav-link text-white">
                        <i class="bi bi-person-lines-fill me-1"></i> Clients
                    </a>
                </li>
                <li>
                    <a href="{{ route('quotations.index') }}" class="nav-link text-white">
                        <i class="bi bi-file-earmark-text me-1"></i> Quotations
                    </a>
                </li>
                <li>
                    <a href="{{ route('billings.index') }}" class="nav-link text-white">
                        <i class="bi bi-receipt me-1"></i> Billing
                    </a>
                </li>
                <li>
                    <a href="{{ route('invoices.index') }}" class="nav-link text-white">
                        <i class="bi bi-file-earmark-ruled me-1"></i> Invoices
                    </a>
                </li>
                <!-- <li>
                    <a href="{{ route('accounts.payments.index') }}" class="nav-link text-white">
                        <i class="bi bi-wallet2 me-1"></i> Payments
                    </a>
                </li> -->
            </ul>
        </li>

        <li class="nav-item has-submenu">
            <a href="#" class="nav-link text-white">
                <i class="bi bi-person-badge-fill me-2"></i> Candidate Admission
                <i class="bi bi-chevron-down float-end"></i>
            </a>
            <ul class="submenu collapse">
                <li>
                    <a href="{{ route('candidates.index') }}" class="nav-link text-white">
                        <i class="bi bi-person-lines-fill me-1"></i> Candidates
                    </a>
                </li>
                <li>
                    <a href="{{ route('candidates.invoices.index') }}" class="nav-link text-white">
                        <i class="bi bi-file-earmark-ruled me-1"></i> Candidate Invoices
                    </a>
                </li>
            </ul>
        </li>

        <li>
            <a href="{{ route('notifications.index') }}" class="nav-link text-white">
                <i class="bi bi-bell"></i> Notifications
            </a>
        </li>

        @elseif(in_array(auth()->user()->role, ['team_leader', 'manager']))

        <li class="nav-item">
            <a href="{{ route('dashboard.team_leader') }}" class="nav-link {{ request()->is('dashboard') ? 'active' : 'text-white' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="{{ route('team_leader.attendance') }}" class="nav-link text-white">
                <i class="bi bi-calendar-check"></i> My Attendance
            </a>
        </li>
        <li>
            <a href="{{ route('team_leader.salary_slips.index') }}" class="nav-link text-white">
                <i class="bi bi-cash-coin"></i> My Salary Slips
            </a>
        </li>
        <li>
            <a href="{{ route('employee.leaves.index') }}" class="nav-link text-white">
                <i class="bi bi-clipboard-data"></i> Leave Requests
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link text-white d-flex justify-content-between align-items-center"
                data-bs-toggle="collapse"
                href="#taskSubMenu"
                role="button"
                aria-expanded="false"
                aria-controls="taskSubMenu">
                <span><i class="bi bi-card-checklist me-2"></i>Task Management</span>
                <i class="bi bi-chevron-down toggle-icon"></i>
            </a>
            <div class="collapse" id="taskSubMenu">
                <ul class="nav flex-column ms-3 mt-1">
                    <li class="nav-item">
                        <a href="{{ route('tasks.index') }}" class="nav-link text-white">
                            <i class="bi bi-list-check me-2"></i> All Tasks
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('tasks.submitedtask') }}" class="nav-link text-white">
                            <i class="bi bi-check2-square"></i> Submitted Task
                        </a>
                    </li>
                </ul>
            </div>
        </li>
        @endif
    </ul>

    <hr class="text-white">

    <div class="dropdown">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-danger w-100" type="submit">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </form>
    </div>
</nav>