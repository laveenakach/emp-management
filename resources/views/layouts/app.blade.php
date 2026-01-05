<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'My App')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    

    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }

        body {
            background-color: #f8fafc;
        }

        .sidebar {
            background-color: #070617;
            min-height: 100vh;
        }

        .sidebar .nav-link {
            color: #d15693;
            margin-bottom: 10px;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: #d15693;
            border-radius: 8px;
        }

        @media (max-width: 768px) {
            .sidebar {
                position: absolute;
                z-index: 1000;
                width: 250px;
                left: -250px;
                transition: left 0.3s;
            }

            .sidebar.active {
                left: 0;
            }
        }

        main {
            min-height: 100vh;
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

    <!-- Navbar for mobile toggle -->
    <nav class="navbar navbar-light bg-light d-md-none">
        <div class="container-fluid">
            <button class="btn btn-primary" id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row flex-nowrap">

            <!-- Sidebar -->
            <div id="sidebar" class="col-auto p-0 sidebar">
                @include('layouts.sidebar')
            </div>

            <!-- Main Content -->
            <div class="col ps-0">
                @include('layouts.navigation')

                <main class="p-4">
                    @yield('content')
                </main>
            </div>

        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    
    <!-- Bootstrap JS Bundle (includes Popper) -->
    <!-- jQuery (Optional, if needed for other scripts) -->
    <!-- <script src="https://code.jquery.com/jquery-3.6.4.min.js" crossorigin="anonymous"></script> -->


    <!-- Sidebar Toggle Script -->
    <!-- <script>
    const toggleBtn = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('active');
    });
</script> -->

    <script>
        document.querySelectorAll('.has-submenu > a').forEach(function(menu) {
            menu.addEventListener('click', function(e) {
                e.preventDefault();
                const submenu = this.nextElementSibling;
                submenu.classList.toggle('show');
            });
        });
    </script>


</body>

</html>