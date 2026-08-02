<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $brandName ?? 'Login' }}</title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="{{ asset('Duralux-admin-1.0.0/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('Duralux-admin-1.0.0/assets/vendors/css/vendors.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('Duralux-admin-1.0.0/assets/css/theme.min.css') }}">

    <style>
        /* Custom Modern Aesthetics */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }
        
        .auth-cover-wrapper {
            background: linear-gradient(135deg, #e0e7ff 0%, #f3e8ff 100%);
            position: relative;
            overflow: hidden;
        }

        /* Subtle animated background shapes */
        .auth-cover-wrapper::before,
        .auth-cover-wrapper::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            z-index: 0;
            animation: pulse 10s ease-in-out infinite alternate;
        }
        .auth-cover-wrapper::before {
            width: 500px;
            height: 500px;
            background: rgba(99, 102, 241, 0.2);
            top: -100px;
            left: -100px;
        }
        .auth-cover-wrapper::after {
            width: 400px;
            height: 400px;
            background: rgba(168, 85, 247, 0.2);
            bottom: -50px;
            right: 15%;
            animation-delay: -5s;
        }

        .auth-cover-content-inner, .auth-cover-sidebar-inner {
            z-index: 1;
            position: relative;
        }

        .auth-cover-sidebar-inner {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-left: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: -10px 0 30px rgba(0, 0, 0, 0.05);
        }

        .auth-cover-card {
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .form-control-lg {
            border-radius: 12px;
            border: 1px solid rgba(226, 232, 240, 0.8);
            padding: 0.85rem 1.25rem;
            transition: all 0.3s ease;
            background-color: rgba(248, 250, 252, 0.8);
            font-size: 0.95rem;
        }

        .form-control-lg:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
            background-color: #fff;
        }

        .btn-primary {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            border: none;
            border-radius: 12px;
            padding: 0.85rem;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 14px 0 rgba(99, 102, 241, 0.39);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.23);
        }

        .btn-primary::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: all 0.5s ease;
        }

        .btn-primary:hover::after {
            left: 100%;
        }

        .custom-control-label {
            transition: color 0.2s;
            padding-top: 2px;
        }

        .custom-control-label:hover {
            color: #6366f1 !important;
        }

        .auth-img {
            animation: float 6s ease-in-out infinite;
            filter: drop-shadow(0 20px 30px rgba(0,0,0,0.08));
        }

        /* Typography Enhancements */
        .text-primary {
            color: #4f46e5 !important;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .form-label {
            letter-spacing: 0.5px;
            color: #64748b !important;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
            100% { transform: translateY(0px); }
        }

        @keyframes pulse {
            0% { transform: scale(1); opacity: 0.6; }
            100% { transform: scale(1.1); opacity: 1; }
        }
    </style>
</head>
<body>
    <main class="auth-cover-wrapper">
        <div class="auth-cover-content-inner">
            <div class="auth-cover-content-wrapper">
                <div class="auth-img text-center">
                    <img src="{{ asset('Duralux-admin-1.0.0/assets/images/auth/auth-cover-login-bg.svg') }}" alt="Login Cover" class="img-fluid" style="max-width: 85%;">
                </div>
            </div>
        </div>
        <div class="auth-cover-sidebar-inner">
            <div class="auth-cover-card-wrapper">
                <div class="auth-cover-card p-sm-5">
                    
                    <div class="mb-5 text-center text-sm-start">
                        @if(!empty($logoPath))
                            <img src="{{ Storage::url($logoPath) }}" alt="{{ $brandName ?? 'Brand' }}" class="img-fluid mb-3" style="max-height: 60px;">
                        @else
                            <h2 class="fw-bolder text-primary mb-3" style="letter-spacing: -0.5px; font-size: 28px;">{{ $brandName ?? 'Grain SAAS' }}</h2>
                        @endif
                    </div>
                    
                    <div class="mb-4">
                        <h2 class="fs-24 fw-bolder mb-2" style="color: #1e293b;">Welcome Back 👋</h2>
                        <p class="fs-14 fw-medium" style="color: #64748b;">Please sign in to your account and continue to the dashboard.</p>
                    </div>
                    
                    @if ($errors->any())
                        <div class="alert alert-danger mt-4 mb-4 py-3 px-4 fs-13 border-0 shadow-sm rounded-3" style="background-color: #fef2f2; color: #ef4444;">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="w-100 mt-4 pt-2">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="form-label fs-11 fw-bold text-uppercase">Email Address</label>
                            <input type="email" name="email" class="form-control form-control-lg shadow-sm" placeholder="name@company.com" value="{{ old('email') }}" required autofocus>
                        </div>
                        
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fs-11 fw-bold text-uppercase mb-0">Password</label>
                                <!-- Optional: Add forgot password link here if route exists -->
                                <!-- <a href="#" class="fs-12 fw-semibold" style="color: #6366f1; text-decoration: none;">Forgot Password?</a> -->
                            </div>
                            <input type="password" name="password" class="form-control form-control-lg shadow-sm" placeholder="••••••••" required>
                        </div>
                        
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div>
                                <div class="custom-control custom-checkbox d-flex align-items-center gap-2">
                                    <input type="checkbox" name="remember" class="custom-control-input form-check-input mt-0 shadow-sm" id="rememberMe" style="cursor: pointer; width: 1.1em; height: 1.1em; border-color: #cbd5e1;">
                                    <label class="custom-control-label c-pointer fs-13 fw-medium mb-0" for="rememberMe" style="color: #475569;">Remember me for 30 days</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-lg btn-primary w-100 fw-bold shadow-sm" style="letter-spacing: 0.5px; font-size: 15px;">Sign In to Dashboard</button>
                        </div>
                    </form>
                    
                </div>
            </div>
        </div>
    </main>
</body>
</html>