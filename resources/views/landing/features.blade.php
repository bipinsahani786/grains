@extends('layouts.landing')

@section('title', 'Features - Grain SaaS')

@section('content')

<div class="grain-pattern"></div>

<!-- Section 1: Page Header -->
<section class="section bg-white" style="padding-top: 150px; text-align: center; border-bottom: 1px solid var(--border-light);">
    <div class="container">
        <h1 style="font-size: 56px; margin-bottom: 20px;">Powerful <span class="text-gradient">Features</span></h1>
        <p style="font-size: 20px; max-width: 700px; margin: 0 auto; color: var(--text-muted);">Discover how our specialized tools can streamline your entire trading workflow from procurement to payment.</p>
    </div>
</section>

<!-- Section 2: Deep Dive Intro -->
<section class="section-sm text-center">
    <div class="container">
        <p style="text-transform: uppercase; letter-spacing: 2px; color: var(--secondary); font-weight: bold;">Built for Traders, by Traders</p>
        <h2 style="font-size: 36px; max-width: 800px; margin: 20px auto;">We've digitized every complex process of the Mandi trade so you can focus on making deals.</h2>
    </div>
</section>

<!-- Section 3: Feature 1 (Inventory) -->
<section class="section">
    <div class="container">
        <div class="split-section">
            <div class="split-content">
                <div class="feature-icon mb-3"><i data-feather="package"></i></div>
                <h2>Lot-wise Inventory Control</h2>
                <p class="mb-3">Grain trading requires precision. Our system doesn't just track total quintals; it tracks individual purchase lots.</p>
                <ul style="list-style: none; padding: 0;">
                    <li style="margin-bottom: 10px; display:flex; align-items:center;"><i data-feather="check" style="color: var(--accent-green); margin-right: 10px;"></i> Deduct stock from specific lots during sales.</li>
                    <li style="margin-bottom: 10px; display:flex; align-items:center;"><i data-feather="check" style="color: var(--accent-green); margin-right: 10px;"></i> Track remaining bags and weight in real-time.</li>
                    <li style="margin-bottom: 10px; display:flex; align-items:center;"><i data-feather="check" style="color: var(--accent-green); margin-right: 10px;"></i> Multi-godown support for distributed storage.</li>
                </ul>
            </div>
            <div class="split-image">
                <img src="https://images.unsplash.com/photo-1586201375761-83865001e31c?auto=format&fit=crop&w=800&q=80" alt="Inventory">
            </div>
        </div>
    </div>
</section>

<!-- Section 4: Inventory Highlight Quote -->
<section class="section-sm bg-white" style="border-top: 1px solid var(--border-light); border-bottom: 1px solid var(--border-light);">
    <div class="container text-center">
        <p style="font-size: 24px; font-style: italic; color: var(--primary-dark); max-width: 800px; margin: 0 auto;">"Knowing exactly which lot of wheat is sitting in which godown has saved us from so much shrinkage and loss."</p>
    </div>
</section>

<!-- Section 5: Feature 2 (Brokers) -->
<section class="section">
    <div class="container">
        <div class="split-section reverse">
            <div class="split-content">
                <div class="feature-icon mb-3"><i data-feather="users"></i></div>
                <h2>Automated Broker Commissions</h2>
                <p class="mb-3">Managing brokers manually leads to errors. Set up rules once and let the system calculate exactly what you owe.</p>
                <ul style="list-style: none; padding: 0;">
                    <li style="margin-bottom: 10px; display:flex; align-items:center;"><i data-feather="check" style="color: var(--accent-green); margin-right: 10px;"></i> Support for Fixed, Percentage, and Per Qtl rules.</li>
                    <li style="margin-bottom: 10px; display:flex; align-items:center;"><i data-feather="check" style="color: var(--accent-green); margin-right: 10px;"></i> Detailed commission ledgers for every broker.</li>
                    <li style="margin-bottom: 10px; display:flex; align-items:center;"><i data-feather="check" style="color: var(--accent-green); margin-right: 10px;"></i> One-click payout tracking.</li>
                </ul>
            </div>
            <div class="split-image">
                <img src="https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&w=800&q=80" alt="Brokers">
            </div>
        </div>
    </div>
</section>

<!-- Section 6: Image Banner -->
<section class="section" style="padding: 0;">
    <img src="https://images.unsplash.com/photo-1574323347407-f5e1ad6d020b?auto=format&fit=crop&w=1920&q=80" style="width: 100%; height: 400px; object-fit: cover;" alt="Field">
</section>

<!-- Section 7: Feature 3 (Ledgers) -->
<section class="section">
    <div class="container">
        <div class="split-section">
            <div class="split-content">
                <div class="feature-icon mb-3"><i data-feather="book-open"></i></div>
                <h2>Live Party Ledgers</h2>
                <p class="mb-3">Stop doing double-entry bookkeeping. Every purchase, sale, receipt, and payment automatically hits the party ledger.</p>
                <ul style="list-style: none; padding: 0;">
                    <li style="margin-bottom: 10px; display:flex; align-items:center;"><i data-feather="check" style="color: var(--accent-green); margin-right: 10px;"></i> View Opening & Closing Balances instantly.</li>
                    <li style="margin-bottom: 10px; display:flex; align-items:center;"><i data-feather="check" style="color: var(--accent-green); margin-right: 10px;"></i> Filter by date ranges for tax filing.</li>
                    <li style="margin-bottom: 10px; display:flex; align-items:center;"><i data-feather="check" style="color: var(--accent-green); margin-right: 10px;"></i> Print or export ledger statements as PDF.</li>
                </ul>
            </div>
            <div class="split-image">
                <img src="https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&w=800&q=80" alt="Ledgers">
            </div>
        </div>
    </div>
</section>

<!-- Section 8: Feature Grid 1 -->
<section class="section bg-white" style="border-top: 1px solid var(--border-light);">
    <div class="container text-center">
        <h2 class="mb-5">Core Modules</h2>
        <div class="features-grid">
            <div class="light-card">
                <i data-feather="download" style="width:40px; height:40px; color:var(--secondary); margin-bottom:20px;"></i>
                <h4>Purchase Management</h4>
                <p>Record arrivals, deduct shortage/wastage, and automatically create inventory lots.</p>
            </div>
            <div class="light-card">
                <i data-feather="shopping-cart" style="width:40px; height:40px; color:var(--secondary); margin-bottom:20px;"></i>
                <h4>Sales Module</h4>
                <p>Create sales dispatch entries, map to brokers, and calculate freight charges.</p>
            </div>
            <div class="light-card">
                <i data-feather="dollar-sign" style="width:40px; height:40px; color:var(--secondary); margin-bottom:20px;"></i>
                <h4>Cash/Bank Books</h4>
                <p>Record daily expenses, party receipts, and broker payments in one place.</p>
            </div>
        </div>
    </div>
</section>

<!-- Section 9: Feature Grid 2 -->
<section class="section bg-white pt-0">
    <div class="container text-center">
        <div class="features-grid">
            <div class="light-card">
                <i data-feather="printer" style="width:40px; height:40px; color:var(--secondary); margin-bottom:20px;"></i>
                <h4>GST Billing</h4>
                <p>Generate professional, compliant tax invoices with your custom letterhead.</p>
            </div>
            <div class="light-card">
                <i data-feather="pie-chart" style="width:40px; height:40px; color:var(--secondary); margin-bottom:20px;"></i>
                <h4>Profit & Loss</h4>
                <p>Real-time analytics on your trade margins, factoring in all expenses and commissions.</p>
            </div>
            <div class="light-card">
                <i data-feather="truck" style="width:40px; height:40px; color:var(--secondary); margin-bottom:20px;"></i>
                <h4>Stock Adjustments</h4>
                <p>Easily log shortages, spillage, and physical stock count corrections.</p>
            </div>
        </div>
    </div>
</section>

<!-- Section 10: Divider -->
<div style="width: 100%; height: 1px; background: var(--border-light);"></div>

<!-- Section 11: Feature 4 (Multi-unit) -->
<section class="section">
    <div class="container">
        <div class="split-section reverse">
            <div class="split-content">
                <div class="feature-icon mb-3"><i data-feather="layers"></i></div>
                <h2>Multi-Unit Support</h2>
                <p class="mb-3">Trade in Quintals, Tons, or Kilograms. Our system natively converts and displays inventory accurately regardless of the purchase unit.</p>
                <ul style="list-style: none; padding: 0;">
                    <li style="margin-bottom: 10px; display:flex; align-items:center;"><i data-feather="check" style="color: var(--accent-green); margin-right: 10px;"></i> Purchase in Tons, Sell in Quintals.</li>
                    <li style="margin-bottom: 10px; display:flex; align-items:center;"><i data-feather="check" style="color: var(--accent-green); margin-right: 10px;"></i> Define custom Bag Weights (e.g., 50kg, 100kg) globally.</li>
                </ul>
            </div>
            <div class="split-image">
                <img src="https://images.unsplash.com/photo-1505553531637-a1288e84e55e?auto=format&fit=crop&w=800&q=80" alt="Units">
            </div>
        </div>
    </div>
</section>

<!-- Section 12: Micro CTA -->
<section class="section-sm text-center bg-dark">
    <div class="container">
        <h2 style="color: #fff; margin-bottom: 20px;">No more manual calculations.</h2>
        <a href="{{ route('login') }}" class="btn btn-primary">Try it free for 14 days</a>
    </div>
</section>

<!-- Section 13: Technical Specs -->
<section class="section bg-white">
    <div class="container">
        <h2 class="text-center mb-5">Built for Speed & Reliability</h2>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">
            <div>
                <h4><i data-feather="zap" style="color: var(--primary-dark); margin-right: 10px;"></i> Lightning Fast</h4>
                <p>Load thousands of ledger entries instantly without browser lag.</p>
            </div>
            <div>
                <h4><i data-feather="database" style="color: var(--primary-dark); margin-right: 10px;"></i> Automated Backups</h4>
                <p>We snapshot your entire database daily to prevent any data loss.</p>
            </div>
            <div>
                <h4><i data-feather="lock" style="color: var(--primary-dark); margin-right: 10px;"></i> AES-256 Encryption</h4>
                <p>Your financial data is encrypted at rest and in transit.</p>
            </div>
            <div>
                <h4><i data-feather="smartphone" style="color: var(--primary-dark); margin-right: 10px;"></i> Mobile Optimized</h4>
                <p>Create sales entries or check stock directly from your phone while at the Mandi.</p>
            </div>
        </div>
    </div>
</section>

<!-- Section 14: Data Portability -->
<section class="section">
    <div class="container text-center">
        <div class="feature-icon" style="margin: 0 auto 20px;"><i data-feather="download-cloud"></i></div>
        <h2 class="mb-3">Your Data is Yours</h2>
        <p style="max-width: 600px; margin: 0 auto;">We believe in open ecosystems. You can export your Ledgers, Purchases, Sales, and Stock Reports to Excel (CSV) or PDF format at any time with a single click.</p>
    </div>
</section>

<!-- Section 15: Workflow Steps 1-2 -->
<section class="section bg-white" style="border-top: 1px solid var(--border-light);">
    <div class="container">
        <h2 class="text-center mb-5">A Seamless Workflow</h2>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-bottom: 40px;">
            <div class="light-card">
                <h3 style="color: var(--primary-dark);">1. Inward (Purchase)</h3>
                <p>Log incoming trucks, assign gross/tare weights, and automatically calculate the net quintals. The system instantly creates a distinct inventory lot.</p>
            </div>
            <div class="light-card">
                <h3 style="color: var(--primary-dark);">2. Storage</h3>
                <p>Allocate the purchased lot to specific Godowns. Monitor real-time shrinkage and perform physical stock adjustments when necessary.</p>
            </div>
        </div>
    </div>
</section>

<!-- Section 16: Workflow Steps 3-4 -->
<section class="section bg-white pt-0">
    <div class="container">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">
            <div class="light-card">
                <h3 style="color: var(--primary-dark);">3. Outward (Sale)</h3>
                <p>Select the buyer, choose the broker, and pick which specific lot you are selling from. Print the GST bill instantly.</p>
            </div>
            <div class="light-card">
                <h3 style="color: var(--primary-dark);">4. Settlement</h3>
                <p>Record payments received against specific bills, and pay out broker commissions based on automated calculations.</p>
            </div>
        </div>
    </div>
</section>

<!-- Section 17: User Roles -->
<section class="section text-center">
    <div class="container">
        <h2 class="mb-5">Multi-User Access</h2>
        <p style="max-width: 600px; margin: 0 auto 40px;">Give your accountants, godown managers, and data entry staff their own logins with restricted permissions.</p>
        <div style="display:flex; justify-content:center; gap:20px; flex-wrap:wrap;">
            <span class="btn btn-outline" style="pointer-events:none;">Admin Role</span>
            <span class="btn btn-outline" style="pointer-events:none;">Manager Role</span>
            <span class="btn btn-outline" style="pointer-events:none;">Staff/Entry Role</span>
            <span class="btn btn-outline" style="pointer-events:none;">Accountant Role</span>
        </div>
    </div>
</section>

<!-- Section 18: Image Banner 2 -->
<section class="section" style="padding: 0;">
    <img src="https://images.unsplash.com/photo-1586201375761-83865001e31c?auto=format&fit=crop&w=1920&q=80" style="width: 100%; height: 300px; object-fit: cover;" alt="Grain">
</section>

<!-- Section 19: FAQ Link -->
<section class="section-sm text-center bg-white" style="border-bottom: 1px solid var(--border-light);">
    <div class="container">
        <p style="font-size: 20px;">Have questions about specific feature implementations?</p>
        <a href="{{ route('landing.contact') }}" style="font-weight: bold; font-size: 20px;">Contact our technical team &rarr;</a>
    </div>
</section>

<!-- Section 20: Final CTA -->
<section class="section text-center" style="background: url('https://images.unsplash.com/photo-1574323347407-f5e1ad6d020b?auto=format&fit=crop&w=1920&q=80') center/cover; position: relative;">
    <div style="position: absolute; top:0; left:0; width:100%; height:100%; background: rgba(38, 70, 83, 0.9);"></div>
    <div class="container" style="position: relative; z-index: 2; color: #fff; padding: 100px 0;">
        <h2 style="font-size: 56px; color: #fff; margin-bottom: 20px;">Stop losing money to manual errors.</h2>
        <p style="font-size: 20px; max-width: 600px; margin: 0 auto 40px; color: rgba(255,255,255,0.8);">Automate your Mandi trade with Grain SaaS.</p>
        <a href="{{ route('login') }}" class="btn btn-primary" style="font-size: 20px; padding: 18px 50px;">Get Started Now</a>
    </div>
</section>

@endsection
