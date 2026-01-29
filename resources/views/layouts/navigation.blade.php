<!-- resources/views/layouts/navigation.blade.php -->
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<nav class="d-none d-md-flex gap-2 navbar navbar-expand-lg navbar-light bg-light shadow-sm mb-4">
    <div class="container-fluid">
        <span class="navbar-brand">Welcome, {{ Auth::user()->name }}!</span>

        <div class="d-flex align-items-center">
           <div class="dropdown me-1">
                <button
                    class="btn position-relative"
                    id="notificationDropdown"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                    type="button">

                    <i class="bi bi-bell fs-2"></i>

                    @if(auth()->user()->unreadNotifications->count() > 0)
                       <span class="position-absolute badge rounded-pill bg-danger"
                            style="top: 4px; right: 6px; font-size: 0.6rem; padding: 4px 6px;">
                            {{ auth()->user()->unreadNotifications->count() }}
                        </span>


                    @endif
                </button>

                <ul class="dropdown-menu dropdown-menu-end shadow"
                    aria-labelledby="notificationDropdown"
                    style="width: 350px;">

                    <li class="dropdown-header d-flex justify-content-between align-items-center">
                        <span>Notifications</span>
                        <div class="d-flex gap-2">
                            <a href="{{ route('notifications.index') }}" class="text-primary">
                                View All
                            </a>
                        @if(auth()->user()->unreadNotifications->count())
                            <a href="{{ route('notifications.markAllRead') }}" class="text-primary">
                                Mark all read
                            </a>
                        @endif
                        </div>
                    </li>

                    <li><hr class="dropdown-divider"></li>

                    @forelse(auth()->user()->notifications->take(5) as $notification)
                        <li>
                            <a href="{{ route('notifications.read', $notification->id) }}"
                            class="dropdown-item {{ $notification->read_at ? '' : 'fw-bold bg-light' }}">

                                <small class="text-muted d-block mb-1">
                                    {{ $notification->created_at->diffForHumans() }}
                                </small>

                                <div class="d-block text-truncate" style="max-width: 100%;">
                                    {{ $notification->data['message'] ?? 'New notification' }}
                                </div>
                            </a>
                        </li>
                    @empty
                        <li class="dropdown-item text-center text-muted">
                            No notifications
                        </li>
                    @endforelse
                </ul>
            </div>

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

<!-- <nav class="d-md-none bg-light shadow-sm mb-4">
    <div class="d-flex align-items-center justify-content-between px-2"
         style="min-height: 56px; flex-wrap: nowrap;">

        <div class="fw-semibold text-dark ms-5 text-truncate"
             style="max-width: 55%;">
            Welcome, {{ Auth::user()->name }}!
        </div>

        <div class="d-flex align-items-center gap-2 flex-shrink-0">

            <button class="btn position-relative p-1">
                <i class="bi bi-bell fs-5"></i>

                @if(auth()->user()->unreadNotifications->count() > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                          style="font-size: 0.6rem;">
                        {{ auth()->user()->unreadNotifications->count() }}
                    </span>
                @endif
            </button>

            <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary btn-sm px-2">
                <i class="bi bi-person"></i>
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-outline-danger btn-sm px-2">
                    <i class="bi bi-box-arrow-right"></i>
                </button>
            </form>
        </div>
    </div>
</nav> -->


