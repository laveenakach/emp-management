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
                    <h3 class="text-center login-title pt-2">Register</h3>
                </div>
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <!-- Name -->
                    <div class="form-group mb-3">
                        <label for="name">Full Name <span class="text-danger">*</span></label>
                        <input id="name" class="form-control" type="text" name="name" required autofocus />
                        @error('name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Email Address -->
                    <div class="form-group mb-3">
                        <label for="email">Email <span class="text-danger">*</span></label>
                        <input id="email" class="form-control" type="email" name="email" required />
                        @error('email')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="form-group mb-3">
                        <label for="password">Password <span class="text-danger">*</span></label>
                        <input id="password" class="form-control" type="password" name="password" required />
                        @error('password')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-group mb-4">
                        <label for="password_confirmation">Confirm Password <span class="text-danger">*</span></label>
                        <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" required />
                    </div>

                    <!-- Role -->
                    <div class="form-group mb-3">
                        <label for="role">Role<span class="text-danger">*</span></label>
                        <select id="role" class="form-control" name="role" required>
                        <option value="">- Select -</option>
                            <option value="employee">Employee</option>
                            <option value="employer">Employer/HR</option>
                            <option value="team_leader">Team Leader</option>
                            <option value="manager">Manager</option>
                            <option value="ceo">CEO</option>
                        </select>
                        @error('role')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-success">Register</button>
                    </div>

                    <div class="text-center mt-3">
                        <a href="{{ route('login') }}">Already have an account? Login</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-guest-layout>