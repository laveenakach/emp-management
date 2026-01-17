<!-- resources/views/layouts/navigation.blade.php -->

<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm mb-4">
    <div class="container-fluid">
        <span class="navbar-brand">Welcome, {{ Auth::user()->name }}!</span>

        <div class="d-flex align-items-center">
            <a href="{{ route('notifications.index') }}">
                <i class="bi bi-bell fs-4 me-2"></i>
            </a>
            <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary me-2">
                <i class="bi bi-person-circle"></i> Profile
            </a>

            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-danger">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            </form>
        </div>
    </div>
</nav>
