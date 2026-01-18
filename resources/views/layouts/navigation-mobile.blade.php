<div class="d-flex align-items-center px-2 w-100"
     style="height:56px; overflow:hidden;">

    <!-- Welcome Text -->
    <div class="fw-semibold text-dark text-truncate flex-grow-1 me-2">

        Welcome, {{ Auth::user()->name }}!
    </div>

    <!-- Right Actions -->
    <div class="d-flex align-items-center gap-2 flex-shrink-0">

        <!-- Bell -->
        <button class="btn position-relative p-1">
            <i class="bi bi-bell fs-5"></i>

            @if(auth()->user()->unreadNotifications->count())
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                      style="font-size:0.6rem;">
                    {{ auth()->user()->unreadNotifications->count() }}
                </span>
            @endif
        </button>

        <!-- Profile -->
        <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-person"></i>
        </a>

        <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-outline-danger btn-sm">
                <i class="bi bi-box-arrow-right"></i>
            </button>
        </form>
    </div>
</div>
