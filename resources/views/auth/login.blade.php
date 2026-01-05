<x-guest-layout>
    <style>

        .login-card {
            max-width: 420px;
            width: 100%;
            border-radius: 1rem;
        }

        .form-label {
            font-weight: 500;
        }

        .login-title {
            font-weight: 600;
            color: #333;
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
    </style>

    <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="card shadow login-card p-4">
            <div class="card-body">
                <div class="login-header">
                    <img src="{{ asset('Images/cropped-1-1.png') }}" alt="Logo" class="login-logo" width="80%">
                    <h3 class="text-center login-title pt-2">Login</h3>
                </div>

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input id="email" class="form-control" type="email" name="email" required autofocus />
                        @error('email')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input id="password" class="form-control" type="password" name="password" required />
                        @error('password')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" name="remember" id="remember">
                        <label class="form-check-label" for="remember">Remember Me</label>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Login</button>
                    </div>

                    <div class="text-center mt-3">
                        <a href="{{ route('register') }}" class="text-decoration-none">Don't have an account? Register</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>