<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Grain SaaS - The Future of Grain Trading')</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Outfit:wght@400;500;700;800&display=swap" rel="stylesheet">
    
    <!-- Feather Icons -->
    <script src="https://unpkg.com/feather-icons"></script>
    
    <!-- Custom Landing CSS -->
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
</head>
<body>
    <!-- Subtle Background Grain Pattern -->
    <div class="grain-pattern"></div>

    <!-- Navbar -->
    <nav class="navbar" id="navbar">
        <div class="container">
            <a href="{{ route('landing.home') }}" class="navbar-brand">
                <i data-feather="box" style="color: var(--primary-dark);"></i> Grain SaaS
            </a>
            
            <button class="mobile-menu-btn" id="mobile-menu-btn">
                <i data-feather="menu"></i>
            </button>
            
            <div class="nav-links" id="nav-links">
                <a href="{{ route('landing.home') }}" class="{{ request()->routeIs('landing.home') ? 'active' : '' }}">Home</a>
                <a href="{{ route('landing.features') }}" class="{{ request()->routeIs('landing.features') ? 'active' : '' }}">Features</a>
                <a href="{{ route('landing.pricing') }}" class="{{ request()->routeIs('landing.pricing') ? 'active' : '' }}">Pricing</a>
                <a href="{{ route('landing.about') }}" class="{{ request()->routeIs('landing.about') ? 'active' : '' }}">About</a>
                <a href="{{ route('landing.contact') }}" class="{{ request()->routeIs('landing.contact') ? 'active' : '' }}">Contact</a>
                
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-outline" style="padding: 10px 24px; font-size: 15px;">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary" style="padding: 10px 24px; font-size: 15px;">Log In</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <a href="{{ route('landing.home') }}" class="navbar-brand" style="font-size: 24px;">
                        <i data-feather="box" style="color: var(--primary-light);"></i> Grain SaaS
                    </a>
                    <p style="margin-top: 15px; max-width: 300px; color: rgba(255,255,255,0.7);">Revolutionizing the grain trading industry with powerful inventory management, broker commissions, and automated accounting.</p>
                </div>
                
                <div class="footer-links">
                    <h4>Product</h4>
                    <ul>
                        <li><a href="{{ route('landing.features') }}">Features</a></li>
                        <li><a href="{{ route('landing.pricing') }}">Pricing</a></li>
                        <li><a href="#">Security</a></li>
                        <li><a href="#">Updates</a></li>
                    </ul>
                </div>
                
                <div class="footer-links">
                    <h4>Company</h4>
                    <ul>
                        <li><a href="{{ route('landing.about') }}">About Us</a></li>
                        <li><a href="#">Careers</a></li>
                        <li><a href="{{ route('landing.contact') }}">Contact Us</a></li>
                    </ul>
                </div>
                
                <div class="footer-links">
                    <h4>Legal</h4>
                    <ul>
                        <li><a href="{{ route('landing.legal') }}">Terms of Service</a></li>
                        <li><a href="{{ route('landing.legal') }}">Privacy Policy</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} Grain SaaS. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        feather.replace();

        window.addEventListener('scroll', () => {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const navLinks = document.getElementById('nav-links');
        
        mobileMenuBtn.addEventListener('click', () => {
            navLinks.classList.toggle('active');
        });
    </script>
</body>
</html>
