@extends('layouts.landing')

@section('title', 'Grain SaaS - Premium Grain Trading Management')

@section('content')

<div class="glow-bg" style="top: -100px; left: -200px;"></div>

<!-- Section 1: Hero Header -->
<section class="hero" style="background-image: linear-gradient(rgba(253, 250, 246, 0.9), rgba(253, 250, 246, 0.9)), url('https://images.unsplash.com/photo-1574323347407-f5e1ad6d020b?auto=format&fit=crop&w=1920&q=80'); background-size: cover; background-position: center;">
    <div class="container">
        <div class="hero-content">
            <h1 class="animate-fade-up">The Future of <span class="text-gradient">Grain Trading</span> is Here</h1>
            <p class="animate-fade-up delay-1">Manage inventory, track lots, automate broker commissions, and handle accounting seamlessly in one premium platform built specifically for agricultural merchants.</p>
            
            <div class="hero-buttons animate-fade-up delay-2">
                <a href="{{ route('login') }}" class="btn btn-primary">Start Your Free Trial</a>
                <a href="{{ route('landing.features') }}" class="btn btn-outline">Explore Features</a>
            </div>
        </div>
        
        <div class="hero-image-wrapper animate-fade-up delay-3">
            <img src="https://images.unsplash.com/photo-1505553531637-a1288e84e55e?auto=format&fit=crop&w=1200&q=80" alt="Dashboard Preview" style="height: 500px; object-fit: cover;">
        </div>
    </div>
</section>

<!-- Section 2: Trust Badges -->
<section class="section-sm bg-white" style="border-bottom: 1px solid var(--border-light);">
    <div class="container text-center">
        <p class="mb-4" style="font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 2px;">Trusted by over 500+ Mandi Traders</p>
        <div style="display: flex; justify-content: center; gap: 50px; flex-wrap: wrap; opacity: 0.6; filter: grayscale(100%);">
            <h3 style="margin:0; font-family:serif;">SHREE KRISHNA TRADERS</h3>
            <h3 style="margin:0; font-family:sans-serif;">AGRO INDIA</h3>
            <h3 style="margin:0; font-family:monospace;">FARM TO MARKET</h3>
            <h3 style="margin:0; font-family:serif;">GLOBAL GRAINS</h3>
            <h3 style="margin:0; font-family:sans-serif;">KISAAN MANDI</h3>
        </div>
    </div>
</section>

<!-- Section 3: Overview Text -->
<section class="section">
    <div class="container text-center">
        <h2 style="font-size: 42px; margin-bottom: 20px;">Everything you need to <span class="text-gradient">scale</span></h2>
        <p style="max-width: 800px; margin: 0 auto; font-size: 20px;">Grain SaaS replaces multiple tools with a single, unified workspace designed for the complexities of agricultural wholesale trading, reducing errors and saving you hours every day.</p>
    </div>
</section>

<!-- Section 4: Feature Split 1 (Inventory) -->
<section class="section pt-0">
    <div class="container">
        <div class="split-section">
            <div class="split-content">
                <div class="feature-icon"><i data-feather="package"></i></div>
                <h2>Lot-wise Inventory Control</h2>
                <p class="mb-3">Grain trading requires precision. Our system doesn't just track total quintals; it tracks individual purchase lots.</p>
                <ul style="list-style: none; padding: 0;">
                    <li style="margin-bottom: 10px;"><i data-feather="check" style="color: var(--accent-green); margin-right: 10px;"></i> Deduct stock from specific lots automatically.</li>
                    <li style="margin-bottom: 10px;"><i data-feather="check" style="color: var(--accent-green); margin-right: 10px;"></i> Track remaining bags and weight in real-time.</li>
                </ul>
            </div>
            <div class="split-image">
                <img src="https://images.unsplash.com/photo-1586201375761-83865001e31c?auto=format&fit=crop&w=800&q=80" alt="Inventory">
            </div>
        </div>
    </div>
</section>

<!-- Section 5: Feature Split 2 (Brokers) -->
<section class="section bg-white" style="border-top: 1px solid var(--border-light); border-bottom: 1px solid var(--border-light);">
    <div class="container">
        <div class="split-section reverse">
            <div class="split-content">
                <div class="feature-icon"><i data-feather="percent"></i></div>
                <h2>Automated Broker Commissions</h2>
                <p class="mb-3">Managing brokers manually leads to errors. Set up rules once and let the system calculate exactly what you owe.</p>
                <ul style="list-style: none; padding: 0;">
                    <li style="margin-bottom: 10px;"><i data-feather="check" style="color: var(--accent-green); margin-right: 10px;"></i> Support for Fixed, Percentage, and Per Qtl rules.</li>
                    <li style="margin-bottom: 10px;"><i data-feather="check" style="color: var(--accent-green); margin-right: 10px;"></i> One-click payout tracking.</li>
                </ul>
            </div>
            <div class="split-image">
                <img src="https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&w=800&q=80" alt="Brokers">
            </div>
        </div>
    </div>
</section>

<!-- Section 6: Feature Split 3 (Accounting) -->
<section class="section">
    <div class="container">
        <div class="split-section">
            <div class="split-content">
                <div class="feature-icon"><i data-feather="book-open"></i></div>
                <h2>Integrated Ledgers</h2>
                <p class="mb-3">No more manual entries. Every purchase, sale, and payment automatically updates party ledgers, giving you instant outstanding reports.</p>
            </div>
            <div class="split-image">
                <img src="https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&w=800&q=80" alt="Accounting">
            </div>
        </div>
    </div>
</section>

<!-- Section 7: Feature Split 4 (Billing) -->
<section class="section bg-white" style="border-top: 1px solid var(--border-light); border-bottom: 1px solid var(--border-light);">
    <div class="container">
        <div class="split-section reverse">
            <div class="split-content">
                <div class="feature-icon"><i data-feather="file-text"></i></div>
                <h2>Professional Invoicing</h2>
                <p class="mb-3">Generate GST-ready bills of supply and tax invoices instantly. Customize with your own header and footer letterhead graphics.</p>
            </div>
            <div class="split-image">
                <img src="https://images.unsplash.com/photo-1554224154-22dec7ec8818?auto=format&fit=crop&w=800&q=80" alt="Invoicing">
            </div>
        </div>
    </div>
</section>

<!-- Section 8: Feature Split 5 (Godowns) -->
<section class="section">
    <div class="container">
        <div class="split-section">
            <div class="split-content">
                <div class="feature-icon"><i data-feather="home"></i></div>
                <h2>Multi-Godown Management</h2>
                <p class="mb-3">Track stock across multiple warehouses or cold storages. Transfer stock seamlessly and view location-wise reports.</p>
            </div>
            <div class="split-image">
                <img src="https://images.unsplash.com/photo-1586201375761-83865001e31c?auto=format&fit=crop&w=800&q=80" alt="Godowns" style="filter: brightness(0.9);">
            </div>
        </div>
    </div>
</section>

<!-- Section 9: Stats Counter Row -->
<section class="section bg-dark">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item">
                <h3 style="color: var(--primary-light);">500+</h3>
                <p style="color: #fff;">Mandi Traders</p>
            </div>
            <div class="stat-item">
                <h3 style="color: var(--primary-light);">₹5B+</h3>
                <p style="color: #fff;">Volume Managed</p>
            </div>
            <div class="stat-item">
                <h3 style="color: var(--primary-light);">50k+</h3>
                <p style="color: #fff;">Invoices Generated</p>
            </div>
            <div class="stat-item">
                <h3 style="color: var(--primary-light);">24/7</h3>
                <p style="color: #fff;">Premium Support</p>
            </div>
        </div>
    </div>
</section>

<!-- Section 10: Video Explainer Placeholder -->
<section class="section">
    <div class="container text-center">
        <h2 class="mb-4">See how it works in 2 minutes</h2>
        <div style="width: 100%; max-width: 900px; margin: 0 auto; height: 500px; background: #000; border-radius: 20px; display: flex; align-items: center; justify-content: center; position: relative; cursor: pointer; background-image: url('https://images.unsplash.com/photo-1574323347407-f5e1ad6d020b?auto=format&fit=crop&w=1200&q=80'); background-size: cover;">
            <div style="width: 80px; height: 80px; background: rgba(255,255,255,0.9); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i data-feather="play" style="color: var(--primary-dark); margin-left: 5px; width: 30px; height: 30px;"></i>
            </div>
        </div>
    </div>
</section>

<!-- Section 11: Feature Grid Cards -->
<section class="section bg-white" style="border-top: 1px solid var(--border-light);">
    <div class="container">
        <h2 class="text-center mb-5">More than just accounting</h2>
        <div class="features-grid">
            <div class="light-card text-center">
                <i data-feather="cloud" style="width: 40px; height: 40px; color: var(--secondary); margin-bottom: 20px;"></i>
                <h4>Cloud Backups</h4>
                <p>Your data is backed up daily to secure cloud servers.</p>
            </div>
            <div class="light-card text-center">
                <i data-feather="lock" style="width: 40px; height: 40px; color: var(--secondary); margin-bottom: 20px;"></i>
                <h4>Bank-Grade Security</h4>
                <p>256-bit encryption for all your financial data.</p>
            </div>
            <div class="light-card text-center">
                <i data-feather="smartphone" style="width: 40px; height: 40px; color: var(--secondary); margin-bottom: 20px;"></i>
                <h4>Mobile Responsive</h4>
                <p>Access your ledgers from anywhere, on any device.</p>
            </div>
        </div>
    </div>
</section>

<!-- Section 12: Testimonial Grid 1 -->
<section class="section">
    <div class="container">
        <h2 class="text-center mb-5">What Traders Are Saying</h2>
        <div class="testimonial-grid">
            <div class="light-card">
                <div class="testimonial-text">"Grain SaaS completely eliminated our ledger mismatch issues. We now know exactly what we owe our brokers and parties in real-time."</div>
                <div class="testimonial-author">
                    <img src="https://ui-avatars.com/api/?name=Rajesh+K&background=random" alt="Rajesh">
                    <div class="author-info">
                        <h4>Rajesh Kumar</h4>
                        <p>Owner, Agro India</p>
                    </div>
                </div>
            </div>
            <div class="light-card">
                <div class="testimonial-text">"The lot-wise inventory tracking is a game changer. We never lose track of old stock in our godowns anymore."</div>
                <div class="testimonial-author">
                    <img src="https://ui-avatars.com/api/?name=Amit+S&background=random" alt="Amit">
                    <div class="author-info">
                        <h4>Amit Singh</h4>
                        <p>Director, Global Grains</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 13: Mid-page Call to Action -->
<section class="section-sm text-center" style="background: linear-gradient(135deg, rgba(212, 163, 115, 0.1), rgba(42, 157, 143, 0.1)); border-top: 1px solid var(--border-light); border-bottom: 1px solid var(--border-light);">
    <div class="container">
        <h2 style="font-size: 36px; margin-bottom: 20px;">Ready to digitize your Mandi business?</h2>
        <a href="{{ route('login') }}" class="btn btn-primary mt-3">Start your 14-day free trial</a>
    </div>
</section>

<!-- Section 14: How it Works Step 1 -->
<section class="section bg-white">
    <div class="container text-center">
        <h2 class="mb-5">How It Works</h2>
        <div class="split-section">
            <div class="split-content text-start">
                <span style="font-size: 80px; font-weight: 800; color: rgba(212, 163, 115, 0.2); line-height: 1;">01</span>
                <h3>Setup your Masters</h3>
                <p>Easily import your existing parties, brokers, grains, and godowns into the system.</p>
            </div>
            <div class="split-content text-start">
                <span style="font-size: 80px; font-weight: 800; color: rgba(212, 163, 115, 0.2); line-height: 1;">02</span>
                <h3>Record Purchases</h3>
                <p>Log incoming stock, assign it to a godown, and automatically generate purchase lots.</p>
            </div>
            <div class="split-content text-start">
                <span style="font-size: 80px; font-weight: 800; color: rgba(212, 163, 115, 0.2); line-height: 1;">03</span>
                <h3>Make Sales</h3>
                <p>Sell from specific lots. The system automatically calculates broker commissions and updates party ledgers.</p>
            </div>
        </div>
    </div>
</section>

<!-- Section 15: How it Works Step 4-6 -->
<section class="section">
    <div class="container text-center">
        <div class="split-section">
            <div class="split-content text-start">
                <span style="font-size: 80px; font-weight: 800; color: rgba(42, 157, 143, 0.2); line-height: 1;">04</span>
                <h3>Track Payments</h3>
                <p>Record receipts and payments. Ledger balances are instantly reconciled.</p>
            </div>
            <div class="split-content text-start">
                <span style="font-size: 80px; font-weight: 800; color: rgba(42, 157, 143, 0.2); line-height: 1;">05</span>
                <h3>Print Bills</h3>
                <p>Print professional GST bills on your letterhead directly from the platform.</p>
            </div>
            <div class="split-content text-start">
                <span style="font-size: 80px; font-weight: 800; color: rgba(42, 157, 143, 0.2); line-height: 1;">06</span>
                <h3>Analyze Profit</h3>
                <p>View real-time profit and loss reports based on actual purchase vs sale rates.</p>
            </div>
        </div>
    </div>
</section>

<!-- Section 16: Security Details -->
<section class="section bg-white" style="border-top: 1px solid var(--border-light); border-bottom: 1px solid var(--border-light);">
    <div class="container text-center">
        <i data-feather="shield" style="width: 60px; height: 60px; color: var(--secondary); margin-bottom: 20px;"></i>
        <h2 class="mb-4">Enterprise Grade Security</h2>
        <p style="max-width: 700px; margin: 0 auto; font-size: 18px;">Your financial data is your most valuable asset. We employ strict data segregation, SSL encryption in transit, and AES-256 encryption at rest to ensure your business information remains completely private and secure.</p>
    </div>
</section>

<!-- Section 17: FAQ Group 1 -->
<section class="section">
    <div class="container">
        <h2 class="text-center mb-5">Frequently Asked Questions</h2>
        <div style="max-width: 800px; margin: 0 auto;">
            <div class="light-card mb-4">
                <h4>Is it suitable for Commission Agents (Kachha Arhtiya)?</h4>
                <p class="mt-2 mb-0">Yes, the system is designed to handle complex commission structures and broker payouts effortlessly.</p>
            </div>
            <div class="light-card mb-4">
                <h4>Can I use my own letterhead for printing?</h4>
                <p class="mt-2 mb-0">Absolutely. You can toggle headers/footers off to print perfectly onto your pre-printed stationery.</p>
            </div>
        </div>
    </div>
</section>

<!-- Section 18: FAQ Group 2 -->
<section class="section pt-0">
    <div class="container">
        <div style="max-width: 800px; margin: 0 auto;">
            <div class="light-card mb-4">
                <h4>Do I need accounting knowledge to use this?</h4>
                <p class="mt-2 mb-0">No! If you can record a basic purchase or sale, the system handles all the complex double-entry accounting in the background automatically.</p>
            </div>
            <div class="light-card mb-4">
                <h4>Is my data backed up?</h4>
                <p class="mt-2 mb-0">Yes, we perform automated encrypted backups daily. You can also export your ledger reports to Excel or PDF at any time.</p>
            </div>
        </div>
    </div>
</section>

<!-- Section 19: Support Information -->
<section class="section bg-white" style="border-top: 1px solid var(--border-light);">
    <div class="container text-center">
        <h2 class="mb-3">We're here to help</h2>
        <p class="mb-4">Our dedicated support team is available via phone and email to assist you with onboarding and training.</p>
        <div style="display: flex; justify-content: center; gap: 40px; align-items: center; margin-top: 30px;">
            <div><i data-feather="phone-call" style="color: var(--primary-dark); margin-right: 10px;"></i> +91 98765 43210</div>
            <div><i data-feather="mail" style="color: var(--primary-dark); margin-right: 10px;"></i> support@grainsaas.com</div>
        </div>
    </div>
</section>

<!-- Section 20: Final Massive CTA Section -->
<section class="section text-center" style="background: url('https://images.unsplash.com/photo-1574323347407-f5e1ad6d020b?auto=format&fit=crop&w=1920&q=80') center/cover; position: relative;">
    <div style="position: absolute; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.7);"></div>
    <div class="container" style="position: relative; z-index: 2; color: #fff; padding: 100px 0;">
        <h2 style="font-size: 56px; color: #fff; margin-bottom: 20px;">Take control of your trade today</h2>
        <p style="font-size: 20px; max-width: 600px; margin: 0 auto 40px; color: rgba(255,255,255,0.8);">Join the fastest growing platform for grain merchants.</p>
        <a href="{{ route('login') }}" class="btn btn-primary" style="font-size: 20px; padding: 18px 50px;">Create Free Account</a>
    </div>
</section>

@endsection
