@extends('layouts.landing')

@section('title', 'About Us - Grain SaaS')

@section('content')

<!-- Section 1: Page Header -->
<section class="section" style="padding-top: 150px; text-align: center; background: url('https://images.unsplash.com/photo-1574323347407-f5e1ad6d020b?auto=format&fit=crop&w=1920&q=80') center/cover; position: relative; color: #fff;">
    <div style="position: absolute; top:0; left:0; width:100%; height:100%; background: rgba(38, 70, 83, 0.85);"></div>
    <div class="container" style="position: relative; z-index: 2;">
        <h1 style="font-size: 56px; margin-bottom: 20px; color: #fff;">About <span style="color: var(--primary-light);">Grain SaaS</span></h1>
        <p style="font-size: 20px; max-width: 700px; margin: 0 auto; color: rgba(255,255,255,0.8);">We are on a mission to bring modern technology to the traditional world of agricultural trading.</p>
    </div>
</section>

<!-- Section 2: Intro Statement -->
<section class="section-sm text-center">
    <div class="container">
        <h2 style="font-size: 32px; font-weight: 400; max-width: 800px; margin: 0 auto; line-height: 1.5; color: var(--primary-dark);">"The agricultural supply chain runs on trust and speed. We built the software that powers both."</h2>
    </div>
</section>

<!-- Section 3: The Problem -->
<section class="section pt-0">
    <div class="container">
        <div class="split-section">
            <div class="split-content">
                <h2 class="mb-3">The Problem</h2>
                <p style="font-size: 18px; color: var(--text-main); line-height: 1.8;">
                    For decades, grain merchants have relied on pen, paper, and disjointed spreadsheets to manage multi-million rupee inventories. We built Grain SaaS because we saw the pain of manual ledger reconciliation, lost stock, and delayed broker payouts. 
                </p>
            </div>
            <div class="split-image">
                <img src="https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&w=800&q=80" alt="Problem" style="filter: grayscale(100%);">
            </div>
        </div>
    </div>
</section>

<!-- Section 4: The Solution -->
<section class="section bg-white" style="border-top: 1px solid var(--border-light); border-bottom: 1px solid var(--border-light);">
    <div class="container">
        <div class="split-section reverse">
            <div class="split-content">
                <h2 class="mb-3">Our Solution</h2>
                <p style="font-size: 18px; color: var(--text-main); line-height: 1.8;">
                    Our platform empowers traders to focus on growing their business by automating the tedious operational tasks that slow them down. From automated broker commissions to lot-wise inventory tracking, we digitize every step of the Mandi trade.
                </p>
            </div>
            <div class="split-image">
                <img src="https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&w=800&q=80" alt="Solution">
            </div>
        </div>
    </div>
</section>

<!-- Section 5: Stats Header -->
<section class="section-sm text-center">
    <div class="container">
        <h2 style="color: var(--primary-dark);">Our Impact in Numbers</h2>
    </div>
</section>

<!-- Section 6: Stats Grid -->
<section class="section bg-white pt-0 pb-0">
    <div class="container text-center py-5">
        <div class="stats-grid">
            <div class="stat-item">
                <h3>500+</h3>
                <p>Active Merchants</p>
            </div>
            <div class="stat-item">
                <h3>₹5B+</h3>
                <p>Transactions Processed</p>
            </div>
            <div class="stat-item">
                <h3>50k+</h3>
                <p>Lots Managed</p>
            </div>
            <div class="stat-item">
                <h3>100%</h3>
                <p>Uptime Record</p>
            </div>
        </div>
    </div>
</section>

<!-- Section 7: Core Values Header -->
<section class="section border-top">
    <div class="container text-center">
        <h2 class="mb-5">Our Core Values</h2>
        <div class="features-grid">
            <div class="light-card">
                <i data-feather="target" style="width:40px; height:40px; color:var(--secondary); margin-bottom:20px;"></i>
                <h4>Accuracy Above All</h4>
                <p>In financial software, there is no room for error. We test every calculation rule to ensure pixel-perfect accounting.</p>
            </div>
            <div class="light-card">
                <i data-feather="shield" style="width:40px; height:40px; color:var(--secondary); margin-bottom:20px;"></i>
                <h4>Uncompromising Security</h4>
                <p>We treat your ledger data with the same level of security as a major banking institution.</p>
            </div>
            <div class="light-card">
                <i data-feather="heart" style="width:40px; height:40px; color:var(--secondary); margin-bottom:20px;"></i>
                <h4>Customer Obsession</h4>
                <p>If our merchants succeed, we succeed. Our support team works tirelessly to resolve any issue.</p>
            </div>
        </div>
    </div>
</section>

<!-- Section 8: Image Banner -->
<section class="section" style="padding: 0;">
    <img src="https://images.unsplash.com/photo-1505553531637-a1288e84e55e?auto=format&fit=crop&w=1920&q=80" style="width: 100%; height: 400px; object-fit: cover;" alt="Grain Sacks">
</section>

<!-- Section 9: Our Story Timeline 1 -->
<section class="section bg-white">
    <div class="container">
        <h2 class="text-center mb-5">Our Journey</h2>
        <div class="split-section mb-5">
            <div class="split-content text-end" style="text-align: right;">
                <h3 style="color: var(--primary-dark);">2023</h3>
                <h4>The Idea</h4>
                <p>After visiting several local Mandis, we realized that despite massive trade volumes, technology adoption was near zero due to the complexity of the commission structures.</p>
            </div>
            <div class="split-image" style="display:flex; justify-content:center; align-items:center; max-width: 50px;">
                <div style="width: 2px; height: 100%; background: var(--primary-light); position: relative;">
                    <div style="width: 20px; height: 20px; background: var(--primary-dark); border-radius: 50%; position: absolute; top: 50%; left: -9px; transform: translateY(-50%);"></div>
                </div>
            </div>
            <div class="split-content">
                <!-- Empty for spacing -->
            </div>
        </div>
    </div>
</section>

<!-- Section 10: Our Story Timeline 2 -->
<section class="section bg-white pt-0 pb-0">
    <div class="container">
        <div class="split-section mb-5">
            <div class="split-content">
                <!-- Empty for spacing -->
            </div>
            <div class="split-image" style="display:flex; justify-content:center; align-items:center; max-width: 50px;">
                <div style="width: 2px; height: 100%; background: var(--primary-light); position: relative;">
                    <div style="width: 20px; height: 20px; background: var(--primary-dark); border-radius: 50%; position: absolute; top: 50%; left: -9px; transform: translateY(-50%);"></div>
                </div>
            </div>
            <div class="split-content text-start">
                <h3 style="color: var(--primary-dark);">2024</h3>
                <h4>The First Prototype</h4>
                <p>We built our first prototype focused solely on solving the lot-wise inventory problem and piloted it with 5 local merchants.</p>
            </div>
        </div>
    </div>
</section>

<!-- Section 11: Our Story Timeline 3 -->
<section class="section bg-white pt-0">
    <div class="container">
        <div class="split-section">
            <div class="split-content text-end" style="text-align: right;">
                <h3 style="color: var(--primary-dark);">2026</h3>
                <h4>Scaling Up</h4>
                <p>Today, we handle billions of rupees in trade volume, having expanded our software to cover complex accounting, billing, and reporting.</p>
            </div>
            <div class="split-image" style="display:flex; justify-content:center; align-items:center; max-width: 50px;">
                <div style="width: 2px; height: 100%; background: var(--primary-light); position: relative;">
                    <div style="width: 20px; height: 20px; background: var(--primary-dark); border-radius: 50%; position: absolute; top: 50%; left: -9px; transform: translateY(-50%);"></div>
                </div>
            </div>
            <div class="split-content">
                <!-- Empty for spacing -->
            </div>
        </div>
    </div>
</section>

<!-- Section 12: Leadership Header -->
<section class="section bg-dark text-center">
    <div class="container">
        <h2 style="color: #fff;">Meet the Leadership Team</h2>
        <p style="color: rgba(255,255,255,0.7);">A mix of technology veterans and agricultural experts.</p>
    </div>
</section>

<!-- Section 13: Team Grid -->
<section class="section pt-0" style="margin-top: -50px;">
    <div class="container">
        <div class="features-grid">
            <div class="light-card text-center" style="padding: 30px;">
                <img src="https://ui-avatars.com/api/?name=Arjun+S&background=d4a373&color=fff&size=120" style="border-radius: 50%; margin-bottom: 20px;" alt="CEO">
                <h4>Arjun Sharma</h4>
                <p style="color: var(--primary-dark); font-weight: bold; margin-bottom: 10px;">Founder & CEO</p>
                <p style="font-size: 14px;">15 years of experience in agricultural supply chain logistics.</p>
            </div>
            <div class="light-card text-center" style="padding: 30px;">
                <img src="https://ui-avatars.com/api/?name=Neha+P&background=2a9d8f&color=fff&size=120" style="border-radius: 50%; margin-bottom: 20px;" alt="CTO">
                <h4>Neha Patel</h4>
                <p style="color: var(--primary-dark); font-weight: bold; margin-bottom: 10px;">Co-Founder & CTO</p>
                <p style="font-size: 14px;">Former lead engineer at a major fintech startup.</p>
            </div>
            <div class="light-card text-center" style="padding: 30px;">
                <img src="https://ui-avatars.com/api/?name=Vikram+R&background=b88656&color=fff&size=120" style="border-radius: 50%; margin-bottom: 20px;" alt="Head of Sales">
                <h4>Vikram Rathore</h4>
                <p style="color: var(--primary-dark); font-weight: bold; margin-bottom: 10px;">Head of Customer Success</p>
                <p style="font-size: 14px;">Dedicated to ensuring every merchant gets maximum value from our platform.</p>
            </div>
        </div>
    </div>
</section>

<!-- Section 14: Tech Stack Intro -->
<section class="section bg-white border-top">
    <div class="container text-center">
        <h2 class="mb-3">Powered by Modern Technology</h2>
        <p style="max-width: 600px; margin: 0 auto;">We don't use legacy frameworks. Our platform is built on a modern, scalable cloud architecture ensuring 99.99% uptime.</p>
    </div>
</section>

<!-- Section 15: Tech Stack Details -->
<section class="section bg-white pt-0">
    <div class="container">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; max-width: 800px; margin: 0 auto;">
            <div class="light-card" style="display:flex; align-items:center; gap: 20px; padding: 20px;">
                <i data-feather="server" style="color: var(--secondary);"></i>
                <div>
                    <h4 style="margin:0;">Cloud Infrastructure</h4>
                    <p style="margin:0; font-size:14px;">AWS Auto-scaling servers</p>
                </div>
            </div>
            <div class="light-card" style="display:flex; align-items:center; gap: 20px; padding: 20px;">
                <i data-feather="database" style="color: var(--secondary);"></i>
                <div>
                    <h4 style="margin:0;">Data Storage</h4>
                    <p style="margin:0; font-size:14px;">PostgreSQL with replication</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 16: Office Image -->
<section class="section" style="padding: 0;">
    <img src="https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=1920&q=80" style="width: 100%; height: 400px; object-fit: cover;" alt="Office">
</section>

<!-- Section 17: Office Location -->
<section class="section bg-white">
    <div class="container text-center">
        <h2 class="mb-4">Our Headquarters</h2>
        <p style="font-size: 18px; color: var(--text-main);">
            <strong>Grain SaaS Technologies Pvt. Ltd.</strong><br>
            123 Tech Park, Tower A<br>
            New Delhi, 110001<br>
            India
        </p>
    </div>
</section>

<!-- Section 18: Join Us Banner -->
<section class="section-sm text-center" style="background: var(--bg-light); border-top: 1px solid var(--border-light); border-bottom: 1px solid var(--border-light);">
    <div class="container">
        <h3>We are hiring!</h3>
        <p>Passionate about digitizing agriculture? We're looking for engineers and sales executives.</p>
        <a href="{{ route('landing.contact') }}" class="btn btn-outline mt-3">View Open Positions</a>
    </div>
</section>

<!-- Section 19: Partner Badges -->
<section class="section bg-white">
    <div class="container text-center">
        <p style="text-transform: uppercase; letter-spacing: 2px; color: var(--text-muted); font-weight: bold; margin-bottom: 30px;">Backed By</p>
        <div style="display: flex; justify-content: center; gap: 40px; opacity: 0.5; filter: grayscale(100%);">
            <h3 style="margin:0; font-family:sans-serif;">AGRI VENTURES</h3>
            <h3 style="margin:0; font-family:serif;">TECH CAPITAL</h3>
            <h3 style="margin:0; font-family:monospace;">SEED FUND INDIA</h3>
        </div>
    </div>
</section>

<!-- Section 20: Final CTA -->
<section class="section text-center" style="background: url('https://images.unsplash.com/photo-1574323347407-f5e1ad6d020b?auto=format&fit=crop&w=1920&q=80') center/cover; position: relative;">
    <div style="position: absolute; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.8);"></div>
    <div class="container" style="position: relative; z-index: 2; color: #fff; padding: 80px 0;">
        <h2 style="font-size: 48px; color: #fff; margin-bottom: 20px;">Join our growing community</h2>
        <p style="font-size: 18px; max-width: 600px; margin: 0 auto 30px; color: rgba(255,255,255,0.8);">Be part of the technological revolution in grain trading.</p>
        <a href="{{ route('landing.contact') }}" class="btn btn-primary" style="font-size: 18px; padding: 15px 40px;">Contact Us</a>
    </div>
</section>

@endsection
