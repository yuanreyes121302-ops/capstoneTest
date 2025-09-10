@extends('layouts.app')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

    /* Override the main layout background - matching navbar red theme */
    main.py-4 {
        padding: 0 !important;
        background: linear-gradient(135deg, rgba(200, 50, 50, 0.9) 0%, rgba(150, 30, 30, 1) 100%) !important;
        min-height: calc(100vh - 56px) !important;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    /* Animated background shapes */
    .bg-shapes {
        position: absolute;
        width: 100%;
        height: 100%;
        overflow: hidden;
        z-index: 0;
    }

    .shape {
        position: absolute;
        border-radius: 50%;
        opacity: 0.05;
        animation: float 20s infinite ease-in-out;
    }

    .shape-1 {
        width: 400px;
        height: 400px;
        background: white;
        top: -200px;
        left: -200px;
        animation-delay: 0s;
    }

    .shape-2 {
        width: 300px;
        height: 300px;
        background: white;
        bottom: -150px;
        right: -150px;
        animation-delay: 5s;
    }

    .shape-3 {
        width: 250px;
        height: 250px;
        background: white;
        top: 40%;
        left: 15%;
        animation-delay: 10s;
    }

    .shape-4 {
        width: 200px;
        height: 200px;
        background: white;
        top: 60%;
        right: 20%;
        animation-delay: 15s;
    }

    @keyframes float {
        0%, 100% {
            transform: translateY(0) rotate(0deg);
        }
        33% {
            transform: translateY(-30px) rotate(120deg);
        }
        66% {
            transform: translateY(30px) rotate(240deg);
        }
    }

    /* Register container - centered */
    .register-container {
        position: relative;
        z-index: 10;
        width: 100%;
        max-width: 900px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .register-card {
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(20px);
        border-radius: 24px;
        padding: 2.5rem;
        box-shadow: 0 25px 70px rgba(0, 0, 0, 0.15);
        animation: slideUp 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(40px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    /* Logo and title */
    .register-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .logo-icon {
        width: 70px;
        height: 70px;
        background: rgba(200, 50, 50, 1);
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.2rem;
        animation: pulse 2.5s infinite;
        box-shadow: 0 8px 25px rgba(200, 50, 50, 0.3);
    }

    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(200, 50, 50, 0.4);
            transform: scale(1);
        }
        50% {
            box-shadow: 0 0 0 20px rgba(200, 50, 50, 0);
            transform: scale(1.05);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(200, 50, 50, 0);
            transform: scale(1);
        }
    }

    .logo-icon i {
        color: white;
        font-size: 32px;
    }

    .register-title {
        font-family: 'Poppins', sans-serif;
        font-size: 2rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 0.3rem;
        letter-spacing: -0.5px;
    }

    .register-subtitle {
        font-family: 'Poppins', sans-serif;
        font-size: 0.95rem;
        color: #7f8c8d;
        font-weight: 400;
    }

    /* Form grid layout */
    .register-form {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    /* Form styling */
    .form-floating {
        position: relative;
        margin-bottom: 0;
    }

    .form-floating input,
    .form-floating select {
        font-family: 'Poppins', sans-serif;
        width: 100%;
        padding: 1rem 2.8rem 1rem 1rem;
        font-size: 0.95rem;
        border: 2px solid #e8ebed;
        border-radius: 14px;
        background: #fafbfc;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        height: 56px;
    }

    .form-floating select {
        cursor: pointer;
    }

    .form-floating label {
        position: absolute;
        top: 50%;
        left: 1rem;
        transform: translateY(-50%);
        font-family: 'Poppins', sans-serif;
        font-size: 0.9rem;
        color: #95a5a6;
        pointer-events: none;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: linear-gradient(to bottom, transparent 45%, #fafbfc 45%, #fafbfc 55%, transparent 55%);
        padding: 0 0.4rem;
    }

    .form-floating input:focus,
    .form-floating input:not(:placeholder-shown),
    .form-floating select:focus,
    .form-floating select:not([value=""]) {
        border-color: rgba(200, 50, 50, 0.8);
        outline: none;
        background: white;
        box-shadow: 0 0 0 4px rgba(200, 50, 50, 0.1);
    }

    .form-floating input:focus ~ label,
    .form-floating input:not(:placeholder-shown) ~ label,
    .form-floating select:focus ~ label,
    .form-floating select:not([value=""]) ~ label {
        top: -2px;
        font-size: 0.75rem;
        color: rgba(200, 50, 50, 1);
        font-weight: 500;
        background: linear-gradient(to bottom, transparent 45%, white 45%, white 55%, transparent 55%);
    }

    /* Icon in input */
    .input-icon {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #95a5a6;
        transition: all 0.3s ease;
        font-size: 18px;
    }

    .form-floating input:focus ~ .input-icon,
    .form-floating select:focus ~ .input-icon {
        color: rgba(200, 50, 50, 1);
        transform: translateY(-50%) scale(1.1);
    }

    /* Error messages */
    .error-message {
        display: block;
        color: #e74c3c;
        font-size: 0.8rem;
        margin-top: 0.3rem;
        font-family: 'Poppins', sans-serif;
        animation: shake 0.4s ease;
        padding-left: 0.5rem;
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-3px); }
        20%, 40%, 60%, 80% { transform: translateX(3px); }
    }

    /* Password fields with visibility toggle */
    .password-field {
        position: relative;
    }

    .password-toggle {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #95a5a6;
        transition: all 0.3s ease;
        font-size: 18px;
        cursor: pointer;
        z-index: 10;
    }

    .password-toggle:hover {
        color: rgba(200, 50, 50, 1);
    }

    /* Submit button */
    .btn-submit {
        width: 100%;
        padding: 0.95rem;
        font-family: 'Poppins', sans-serif;
        font-size: 1rem;
        font-weight: 600;
        color: white;
        background: rgba(200, 50, 50, 1);
        border: none;
        border-radius: 14px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 15px rgba(200, 50, 50, 0.3);
        grid-column: 1 / -1;
    }

    .btn-submit:before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.6s ease;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(200, 50, 50, 0.4);
        background: rgba(180, 40, 40, 1);
    }

    .btn-submit:hover:before {
        left: 100%;
    }

    .btn-submit:active {
        transform: translateY(0);
        box-shadow: 0 2px 10px rgba(200, 50, 50, 0.3);
    }

    /* Sign in link */
    .signin-section {
        text-align: center;
        margin-top: 1.5rem;
        font-family: 'Poppins', sans-serif;
    }

    .signin-text {
        font-size: 0.95rem;
        color: #5a6c7d;
        font-weight: 400;
    }

    .signin-link {
        color: rgba(200, 50, 50, 1);
        text-decoration: none;
        font-weight: 600;
        margin-left: 0.3rem;
        transition: all 0.3s ease;
        position: relative;
    }

    .signin-link::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 0;
        height: 2px;
        background: rgba(200, 50, 50, 1);
        transition: width 0.3s ease;
    }

    .signin-link:hover {
        color: rgba(150, 30, 30, 1);
    }

    .signin-link:hover::after {
        width: 100%;
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .register-card {
            padding: 2rem 1.5rem;
            margin: 1rem;
        }

        .register-form {
            grid-template-columns: 1fr;
            gap: 1.2rem;
        }

        .register-title {
            font-size: 1.6rem;
        }

        .form-floating input,
        .form-floating select {
            padding: 0.9rem 2.5rem 0.9rem 0.9rem;
            height: 52px;
        }

        .form-floating label {
            left: 0.9rem;
        }

        .input-icon,
        .password-toggle {
            right: 0.9rem;
        }
    }

    @media (max-width: 480px) {
        .register-card {
            padding: 1.5rem 1rem;
        }

        .logo-icon {
            width: 60px;
            height: 60px;
        }

        .logo-icon i {
            font-size: 28px;
        }
    }

    /* Loading state */
    .btn-submit.loading {
        pointer-events: none;
        opacity: 0.8;
    }

    .btn-submit.loading::after {
        content: '';
        position: absolute;
        width: 20px;
        height: 20px;
        top: 50%;
        left: 50%;
        margin-left: -10px;
        margin-top: -10px;
        border: 2px solid transparent;
        border-top: 2px solid white;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Success message */
    .alert-success {
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        border: 1px solid #c3e6cb;
        color: #155724;
        padding: 1rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        font-family: 'Poppins', sans-serif;
        font-size: 0.9rem;
        animation: slideDown 0.4s ease;
        grid-column: 1 / -1;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<!-- Animated background shapes -->
<div class="bg-shapes">
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>
    <div class="shape shape-3"></div>
    <div class="shape shape-4"></div>
</div>

<div class="register-container">
    <div class="register-card">
        <!-- Header -->
        <div class="register-header">
            <div class="logo-icon">
                <i class="fas fa-user-plus"></i>
            </div>
            <h1 class="register-title">Create Account</h1>
            <p class="register-subtitle">Join us and start your journey today</p>
        </div>

        <!-- Success Message -->
        @if (session('status'))
            <div class="alert-success">
                {{ session('status') }}
            </div>
        @endif

        <!-- Registration Form -->
        <form method="POST" action="{{ route('register') }}" id="registerForm" class="register-form">
            @csrf

            <!-- First Name -->
            <div class="form-floating">
                <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}" required autofocus placeholder=" ">
                <label for="first_name">First Name</label>
                <i class="fas fa-user input-icon"></i>
            </div>

            <!-- Last Name -->
            <div class="form-floating">
                <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}" required placeholder=" ">
                <label for="last_name">Last Name</label>
                <i class="fas fa-user input-icon"></i>
            </div>

            <!-- Email -->
            <div class="form-floating">
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder=" ">
                <label for="email">Email Address</label>
                <i class="fas fa-envelope input-icon"></i>
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- User ID -->
            <div class="form-floating">
                <input id="user_id" type="text" name="user_id" value="{{ old('user_id') }}" required placeholder=" ">
                <label for="user_id">User ID</label>
                <i class="fas fa-id-card input-icon"></i>
                @error('user_id')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Gender -->
            <div class="form-floating">
                <select id="gender" name="gender" required>
                    <option value=""></option>
                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                </select>
                <label for="gender">Gender</label>
                <i class="fas fa-venus-mars input-icon"></i>
            </div>

            <!-- Role -->
            <div class="form-floating">
                <select id="role" name="role" required>
                    <option value=""></option>
                    <option value="tenant" {{ old('role') == 'tenant' ? 'selected' : '' }}>Tenant</option>
                    <option value="landlord" {{ old('role') == 'landlord' ? 'selected' : '' }}>Landlord</option>
                </select>
                <label for="role">Register As</label>
                <i class="fas fa-user-tag input-icon"></i>
            </div>

            <!-- Contact Number (for landlords only) -->
            <div class="form-floating" id="contact-number-field" style="display: none;">
                <input id="contact_number" type="text" name="contact_number" value="{{ old('contact_number') }}" placeholder=" ">
                <label for="contact_number">Contact Number</label>
                <i class="fas fa-phone input-icon"></i>
                @error('contact_number')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Password -->
            <div class="form-floating password-field">
                <input id="password" type="password" name="password" required autocomplete="new-password" placeholder=" ">
                <label for="password">Password</label>
                <i class="fas fa-lock password-toggle" id="passwordToggle"></i>
                @error('password')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="form-floating password-field">
                <input id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password" placeholder=" ">
                <label for="password-confirm">Confirm Password</label>
                <i class="fas fa-lock password-toggle" id="confirmPasswordToggle"></i>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-submit" id="submitBtn">
                Create Account
            </button>
        </form>

        <!-- Sign In Link -->
        <div class="signin-section">
            <span class="signin-text">Already have an account?</span>
            @if (Route::has('login'))
                <a href="{{ route('login') }}" class="signin-link">Sign In</a>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registerForm');
    const submitBtn = document.getElementById('submitBtn');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('password-confirm');
    const passwordToggle = document.getElementById('passwordToggle');
    const confirmPasswordToggle = document.getElementById('confirmPasswordToggle');

    // Add loading state on form submit
    form.addEventListener('submit', function() {
        submitBtn.classList.add('loading');
        submitBtn.textContent = '';
    });

    // Password visibility toggle for password field
    passwordToggle.addEventListener('click', function() {
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            passwordToggle.classList.remove('fa-lock');
            passwordToggle.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            passwordToggle.classList.remove('fa-eye-slash');
            passwordToggle.classList.add('fa-lock');
        }
    });

    // Password visibility toggle for confirm password field
    confirmPasswordToggle.addEventListener('click', function() {
        if (confirmPasswordInput.type === 'password') {
            confirmPasswordInput.type = 'text';
            confirmPasswordToggle.classList.remove('fa-lock');
            confirmPasswordToggle.classList.add('fa-eye-slash');
        } else {
            confirmPasswordInput.type = 'password';
            confirmPasswordToggle.classList.remove('fa-eye-slash');
            confirmPasswordToggle.classList.add('fa-lock');
        }
    });

    // Enhanced form validation feedback
    const inputs = document.querySelectorAll('input, select');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value.trim() !== '' || (this.tagName === 'SELECT' && this.value !== '')) {
                this.classList.add('has-value');
            } else {
                this.classList.remove('has-value');
            }
        });

        // Check if input has value on page load
        if (input.value.trim() !== '' || (input.tagName === 'SELECT' && input.value !== '')) {
            input.classList.add('has-value');
        }
    });

    // Password confirmation validation
    confirmPasswordInput.addEventListener('input', function() {
        if (passwordInput.value !== this.value) {
            this.setCustomValidity('Passwords do not match');
        } else {
            this.setCustomValidity('');
        }
    });

    // Show/hide contact number field based on role selection
    const roleSelect = document.getElementById('role');
    const contactNumberField = document.getElementById('contact-number-field');

    roleSelect.addEventListener('change', function() {
        if (this.value === 'landlord') {
            contactNumberField.style.display = 'block';
            document.getElementById('contact_number').setAttribute('required', 'required');
        } else {
            contactNumberField.style.display = 'none';
            document.getElementById('contact_number').removeAttribute('required');
        }
    });

    // Check initial role selection on page load
    if (roleSelect.value === 'landlord') {
        contactNumberField.style.display = 'block';
        document.getElementById('contact_number').setAttribute('required', 'required');
    }
});
</script>

@endsection
