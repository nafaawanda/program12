<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Perpustakaan</title>
    
    <!-- Custom fonts for this template-->
    <link href="{{ asset('assets/vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('assets/css/sb-admin-2.min.css') }}" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #00A0E9;
            --secondary-color: #4e73df;
        }
        
        body {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #00A0E9 100%);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }
        
        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        /* Floating particles */
        .particles {
            position: fixed;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }
        
        .particle {
            position: absolute;
            width: 10px;
            height: 10px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            animation: float-particle 10s ease-in-out infinite;
        }
        
        .particle:nth-child(1) { left: 10%; top: 20%; animation-delay: 0s; width: 8px; height: 8px; }
        .particle:nth-child(2) { left: 20%; top: 60%; animation-delay: 1s; width: 12px; height: 12px; }
        .particle:nth-child(3) { left: 35%; top: 30%; animation-delay: 2s; width: 6px; height: 6px; }
        .particle:nth-child(4) { left: 50%; top: 70%; animation-delay: 3s; width: 10px; height: 10px; }
        .particle:nth-child(5) { left: 65%; top: 25%; animation-delay: 4s; width: 8px; height: 8px; }
        .particle:nth-child(6) { left: 80%; top: 55%; animation-delay: 5s; width: 14px; height: 14px; }
        .particle:nth-child(7) { left: 90%; top: 40%; animation-delay: 6s; width: 7px; height: 7px; }
        .particle:nth-child(8) { left: 15%; top: 80%; animation-delay: 7s; width: 11px; height: 11px; }
        
        @keyframes float-particle {
            0%, 100% { 
                transform: translateY(0) scale(1); 
                opacity: 0.3;
            }
            50% { 
                transform: translateY(-40px) scale(1.5); 
                opacity: 0.8;
            }
        }
        
        /* Light rays */
        .light-rays {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(ellipse at 20% 80%, rgba(255, 215, 0, 0.15) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 20%, rgba(0, 160, 233, 0.1) 0%, transparent 40%);
            pointer-events: none;
            z-index: 1;
            animation: rays-pulse 5s ease-in-out infinite;
        }
        
        @keyframes rays-pulse {
            0%, 100% { opacity: 0.5; }
            50% { opacity: 1; }
        }
        
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            background: rgba(255, 255, 255, 0.95);
            overflow: hidden;
            position: relative;
            z-index: 10;
            animation: card-appear 0.8s ease-out;
        }
        
        @keyframes card-appear {
            from {
                opacity: 0;
                transform: scale(0.8) translateY(50px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }
        
        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 8px;
            background: linear-gradient(90deg, var(--primary-color), #87CEEB, var(--primary-color));
        }
        
        .card-header {
            background: transparent;
            border-bottom: none;
            padding-top: 1.5rem;
        }
        
        .card-body {
            padding: 1.5rem 2rem 2rem;
        }
        
        /* Logo image styling */
        .logo-container {
            text-align: center;
            margin-bottom: 1rem;
        }
        
        .logo-img {
            width: 150px;
            height: 150px;
            object-fit: contain;
            border-radius: 50%;
            animation: logo-float 4s ease-in-out infinite;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease;
        }
        
        .logo-img:hover {
            transform: scale(1.1) rotate(5deg);
        }
        
        @keyframes logo-float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        /* Input styling */
        .input-group {
            position: relative;
            margin-bottom: 1.2rem;
        }
        
        .input-group-text {
            background: var(--primary-color);
            border: none;
            color: white;
            border-radius: 50px 0 0 50px;
            padding-left: 1rem;
            padding-right: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .form-control-user {
            border-radius: 0 50px 50px 0;
            padding: 0.8rem 1rem;
            font-size: 0.9rem;
            border: 2px solid #e3e6f0;
            transition: all 0.3s ease;
        }
        
        .form-control-user:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(0, 160, 233, 0.2);
        }
        
        /* Login button */
        .btn-login {
            border-radius: 50px;
            padding: 0.8rem 2rem;
            font-size: 1rem;
            font-weight: bold;
            background: linear-gradient(180deg, var(--primary-color) 0%, #0080c0 100%);
            border: none;
            color: white;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s ease;
        }
        
        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 160, 233, 0.5);
            background: linear-gradient(180deg, #00b0f0 0%, var(--primary-color) 100%);
        }
        
        .btn-login:hover::before {
            left: 100%;
        }
        
        /* Checkbox styling */
        .custom-checkbox .custom-control-input:checked ~ .custom-control-label::before {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        /* Alert animation */
        .alert {
            animation: slideDown 0.4s ease-out;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Loading spinner */
        .spinner {
            display: none;
            width: 18px;
            height: 18px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin-right: 8px;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .btn-login.loading .spinner {
            display: inline-block;
        }
        
        .btn-login.loading {
            pointer-events: none;
            opacity: 0.9;
        }
        
        .password-toggle {
            cursor: pointer;
            transition: color 0.3s ease;
            color: #6c757d !important;
        }
        
        .password-toggle:hover {
            color: var(--primary-color) !important;
        }
        
        /* Link hover */
        .a-link {
            color: var(--primary-color);
            transition: all 0.3s ease;
        }
        
        .a-link:hover {
            color: #0080c0;
            text-decoration: none;
            transform: scale(1.05);
            display: inline-block;
        }
        
        /* Title styling */
        .title {
            color: #333;
            font-weight: 700;
        }
        
        .subtitle {
            color: #666;
        }
    </style>
</head>
<body>

    <!-- Light rays effect -->
    <div class="light-rays"></div>
    
    <!-- Floating particles -->
    <div class="particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-5 col-lg-6 col-md-7">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="p-5">
                                    <div class="text-center">
                                        <!-- Logo Image -->
                                        <div class="logo-container">
                                            <img src="{{ asset('assets/img/image.png') }}" 
                                                 alt="Logo" 
                                                 class="logo-img"
                                                 title="Perpustakaan">
                                        </div>
                                        
                                        <h1 class="h4 title mb-2">Perpustakaan</h1>
                                        <p class="subtitle mb-4">Login Administrator / Staff / Mahasiswa</p>
                                    </div>
                                    
                                    @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <i class="fas fa-exclamation-circle mr-2"></i>
                                            @foreach ($errors->all() as $error)
                                                {{ $error }}<br>
                                            @endforeach
                                        </div>
                                    @endif

                                    @if(session('error'))
                                        <div class="alert alert-danger">
                                            <i class="fas fa-exclamation-circle mr-2"></i>
                                            {{ session('error') }}
                                        </div>
                                    @endif

                                    <form class="user" id="loginForm" action="/login" method="POST">
                                        @csrf
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fas fa-user"></i>
                                                </span>
                                            </div>
                                            <input type="text" class="form-control form-control-user"
                                                id="identity" name="identity" required autofocus
                                                placeholder="Email / NIM (Mahasiswa)">
                                        </div>
                                        
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fas fa-lock"></i>
                                                </span>
                                            </div>
                                            <input type="password" class="form-control form-control-user"
                                                id="password" name="password" required
                                                placeholder="Password">
                                            <div class="input-group-append">
                                                <span class="input-group-text bg-white border-left-0 password-toggle" id="togglePassword">
                                                    <i class="fas fa-eye-slash"></i>
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox small">
                                                <input type="checkbox" class="custom-control-input" id="remember">
                                                <label class="custom-control-label" for="remember">
                                                    Ingat Saya
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-login btn-user btn-block" id="loginBtn">
                                            <span class="spinner"></span>
                                            <span class="btn-text"><i class="fas fa-sign-in-alt mr-2"></i>Login</span>
                                        </button>
                                    </form>
                                    
                                    <hr>
                                    <!-- Lupa Password dihapus -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{ asset('assets/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{ asset('assets/js/sb-admin-2.min.js') }}"></script>
    
    <script>
        // Toggle password visibility
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
        
        // Form submission with loading
        const loginForm = document.getElementById('loginForm');
        const loginBtn = document.getElementById('loginBtn');
        
        loginForm.addEventListener('submit', function() {
            // Prevent double submission
            if (loginBtn.classList.contains('loading')) {
                return false;
            }
            loginBtn.classList.add('loading');
            loginBtn.querySelector('.btn-text').textContent = 'Loading...';
        });
    </script>

</body>
</html>
