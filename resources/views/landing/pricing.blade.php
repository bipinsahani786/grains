@extends('layouts.landing')

@section('title', 'Pricing - Grain SaaS')

@section('content')

<!-- Section 1: Page Header -->
<section class="section bg-white" style="padding-top: 150px; text-align: center; border-bottom: 1px solid var(--border-light);">
    <div class="container">
        <h1 style="font-size: 56px; margin-bottom: 20px;">Simple, <span class="text-gradient">Transparent</span> Pricing</h1>
        <p style="font-size: 20px; max-width: 700px; margin: 0 auto; color: var(--text-muted);">No hidden fees. Choose the plan that best fits the size and needs of your agricultural trading business.</p>
    </div>
</section>

<!-- Section 2: Pricing Toggle/Intro -->
<section class="section-sm text-center">
    <div class="container">
        <p style="text-transform: uppercase; letter-spacing: 2px; color: var(--secondary); font-weight: bold;">Pay Monthly or Annually</p>
        <p>Save 20% when you pay annually. All plans include a 14-day free trial.</p>
    </div>
</section>

<!-- Section 3: Pricing Grid -->
<section class="section pt-0">
    <div class="container">
        <div class="pricing-grid">
            
            <!-- Basic Plan -->
            <div class="pricing-card">
                <h3>Starter</h3>
                <p style="color: var(--text-muted); margin-bottom: 20px;">Perfect for small, single-godown operations.</p>
                <div class="pricing-price">₹1,999<span>/mo</span></div>
                <a href="{{ route('login') }}" class="btn btn-outline" style="width: 100%;">Get Started</a>
                <ul class="pricing-features">
                    <li><i data-feather="check"></i> 1 Godown Limit</li>
                    <li><i data-feather="check"></i> 2 User Accounts</li>
                    <li><i data-feather="check"></i> 500 Transactions/mo</li>
                    <li><i data-feather="check"></i> Basic Ledger Management</li>
                    <li><i data-feather="check"></i> Email Support</li>
                </ul>
            </div>
            
            <!-- Professional Plan -->
            <div class="pricing-card popular" style="background: #ffffff;">
                <div class="pricing-badge">MOST POPULAR</div>
                <h3>Professional</h3>
                <p style="color: var(--text-muted); margin-bottom: 20px;">For growing businesses with multiple locations.</p>
                <div class="pricing-price">₹4,999<span>/mo</span></div>
                <a href="{{ route('login') }}" class="btn btn-primary" style="width: 100%;">Start Free Trial</a>
                <ul class="pricing-features">
                    <li><i data-feather="check"></i> Up to 5 Godowns</li>
                    <li><i data-feather="check"></i> 5 User Accounts</li>
                    <li><i data-feather="check"></i> Unlimited Transactions</li>
                    <li><i data-feather="check"></i> Automated Broker Commissions</li>
                    <li><i data-feather="check"></i> Custom Invoice Letterheads</li>
                    <li><i data-feather="check"></i> Priority Email & Chat Support</li>
                </ul>
            </div>
            
            <!-- Enterprise Plan -->
            <div class="pricing-card">
                <h3>Enterprise</h3>
                <p style="color: var(--text-muted); margin-bottom: 20px;">Complete control for large scale wholesale traders.</p>
                <div class="pricing-price">₹9,999<span>/mo</span></div>
                <a href="{{ route('landing.contact') }}" class="btn btn-outline" style="width: 100%;">Contact Sales</a>
                <ul class="pricing-features">
                    <li><i data-feather="check"></i> Unlimited Godowns</li>
                    <li><i data-feather="check"></i> Unlimited User Accounts</li>
                    <li><i data-feather="check"></i> Multi-branch Management</li>
                    <li><i data-feather="check"></i> API Access & Integrations</li>
                    <li><i data-feather="check"></i> Custom Reporting</li>
                    <li><i data-feather="check"></i> 24/7 Dedicated Phone Support</li>
                </ul>
            </div>
            
        </div>
    </div>
</section>

<!-- Section 4: Trusted Banner -->
<section class="section-sm bg-dark text-center">
    <div class="container">
        <h3 style="color: #fff; margin-bottom: 10px;">Trusted by 500+ Mandi Traders across India</h3>
        <p style="color: rgba(255,255,255,0.7); margin: 0;">Over ₹5 Billion in trade volume managed annually on Grain SaaS.</p>
    </div>
</section>

<!-- Section 5: Comparison Header -->
<section class="section bg-white pb-0">
    <div class="container text-center">
        <h2 style="font-size: 36px;">Compare Plans in Detail</h2>
        <p style="max-width: 600px; margin: 20px auto 0;">See exactly what you get with each tier.</p>
    </div>
</section>

<!-- Section 6: Comparison Table (Inventory) -->
<section class="section bg-white pt-0">
    <div class="container">
        <h3 class="mb-4 text-center" style="color: var(--primary-dark);">Inventory Features</h3>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; background: var(--bg-light); border: 1px solid var(--border-light);">
                <thead>
                    <tr style="border-bottom: 2px solid var(--border-light); background: #ffffff;">
                        <th style="padding: 20px;">Feature</th>
                        <th style="padding: 20px;">Starter</th>
                        <th style="padding: 20px; border-left: 2px solid var(--primary); border-right: 2px solid var(--primary); background: rgba(212, 163, 115, 0.05);">Professional</th>
                        <th style="padding: 20px;">Enterprise</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="border-bottom: 1px solid var(--border-light);">
                        <td style="padding: 15px 20px;">Lot-wise Tracking</td>
                        <td style="padding: 15px 20px;"><i data-feather="check" style="color: var(--secondary);"></i></td>
                        <td style="padding: 15px 20px; border-left: 2px solid var(--primary); border-right: 2px solid var(--primary); background: rgba(212, 163, 115, 0.05);"><i data-feather="check" style="color: var(--secondary);"></i></td>
                        <td style="padding: 15px 20px;"><i data-feather="check" style="color: var(--secondary);"></i></td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border-light);">
                        <td style="padding: 15px 20px;">Multi-Unit (Qtl/Kg/Ton)</td>
                        <td style="padding: 15px 20px;"><i data-feather="check" style="color: var(--secondary);"></i></td>
                        <td style="padding: 15px 20px; border-left: 2px solid var(--primary); border-right: 2px solid var(--primary); background: rgba(212, 163, 115, 0.05);"><i data-feather="check" style="color: var(--secondary);"></i></td>
                        <td style="padding: 15px 20px;"><i data-feather="check" style="color: var(--secondary);"></i></td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border-light);">
                        <td style="padding: 15px 20px;">Stock Adjustments</td>
                        <td style="padding: 15px 20px;"><i data-feather="minus" style="color: var(--text-light);"></i></td>
                        <td style="padding: 15px 20px; border-left: 2px solid var(--primary); border-right: 2px solid var(--primary); background: rgba(212, 163, 115, 0.05);"><i data-feather="check" style="color: var(--secondary);"></i></td>
                        <td style="padding: 15px 20px;"><i data-feather="check" style="color: var(--secondary);"></i></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- Section 7: Comparison Table (Accounting) -->
<section class="section bg-white pt-0">
    <div class="container">
        <h3 class="mb-4 text-center" style="color: var(--primary-dark);">Accounting Features</h3>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; background: var(--bg-light); border: 1px solid var(--border-light);">
                <thead>
                    <tr style="border-bottom: 2px solid var(--border-light); background: #ffffff;">
                        <th style="padding: 20px;">Feature</th>
                        <th style="padding: 20px;">Starter</th>
                        <th style="padding: 20px; border-left: 2px solid var(--primary); border-right: 2px solid var(--primary); background: rgba(212, 163, 115, 0.05);">Professional</th>
                        <th style="padding: 20px;">Enterprise</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="border-bottom: 1px solid var(--border-light);">
                        <td style="padding: 15px 20px;">Party Ledgers</td>
                        <td style="padding: 15px 20px;"><i data-feather="check" style="color: var(--secondary);"></i></td>
                        <td style="padding: 15px 20px; border-left: 2px solid var(--primary); border-right: 2px solid var(--primary); background: rgba(212, 163, 115, 0.05);"><i data-feather="check" style="color: var(--secondary);"></i></td>
                        <td style="padding: 15px 20px;"><i data-feather="check" style="color: var(--secondary);"></i></td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border-light);">
                        <td style="padding: 15px 20px;">Broker Commissions</td>
                        <td style="padding: 15px 20px;"><i data-feather="minus" style="color: var(--text-light);"></i></td>
                        <td style="padding: 15px 20px; border-left: 2px solid var(--primary); border-right: 2px solid var(--primary); background: rgba(212, 163, 115, 0.05);"><i data-feather="check" style="color: var(--secondary);"></i></td>
                        <td style="padding: 15px 20px;"><i data-feather="check" style="color: var(--secondary);"></i></td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border-light);">
                        <td style="padding: 15px 20px;">Profit & Loss Tracking</td>
                        <td style="padding: 15px 20px;"><i data-feather="minus" style="color: var(--text-light);"></i></td>
                        <td style="padding: 15px 20px; border-left: 2px solid var(--primary); border-right: 2px solid var(--primary); background: rgba(212, 163, 115, 0.05);"><i data-feather="check" style="color: var(--secondary);"></i></td>
                        <td style="padding: 15px 20px;"><i data-feather="check" style="color: var(--secondary);"></i></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- Section 8: Image Break -->
<section class="section" style="padding: 0;">
    <img src="https://images.unsplash.com/photo-1505553531637-a1288e84e55e?auto=format&fit=crop&w=1920&q=80" style="width: 100%; height: 300px; object-fit: cover;" alt="Sacks of grain">
</section>

<!-- Section 9: 14 Day Guarantee -->
<section class="section text-center">
    <div class="container">
        <i data-feather="shield" style="width: 60px; height: 60px; color: var(--primary-dark); margin-bottom: 20px;"></i>
        <h2>14-Day Money Back Guarantee</h2>
        <p style="max-width: 600px; margin: 0 auto;">If you upgrade to a paid plan and decide Grain SaaS isn't the right fit for your business within 14 days, we will refund your payment in full. No questions asked.</p>
    </div>
</section>

<!-- Section 10: FAQ Header -->
<section class="section bg-white border-top">
    <div class="container text-center">
        <h2>Frequently Asked Questions</h2>
        <p>Everything you need to know about billing and plans.</p>
    </div>
</section>

<!-- Section 11: FAQ General -->
<section class="section bg-white pt-0">
    <div class="container">
        <h3 class="mb-4" style="text-align: center;">General Questions</h3>
        <div style="max-width: 800px; margin: 0 auto;">
            <div class="light-card mb-4" style="padding: 30px;">
                <h4 style="margin-bottom: 10px;">Do you offer a free trial?</h4>
                <p style="margin: 0;">Yes! Our Professional plan comes with a fully-featured 14-day free trial. No credit card required to start.</p>
            </div>
            <div class="light-card mb-4" style="padding: 30px;">
                <h4 style="margin-bottom: 10px;">Can I change plans later?</h4>
                <p style="margin: 0;">Absolutely. You can upgrade or downgrade your plan at any time directly from your billing dashboard. Prorated charges will apply.</p>
            </div>
        </div>
    </div>
</section>

<!-- Section 12: FAQ Data -->
<section class="section pt-0">
    <div class="container">
        <h3 class="mb-4" style="text-align: center;">Data & Security</h3>
        <div style="max-width: 800px; margin: 0 auto;">
            <div class="light-card mb-4" style="padding: 30px;">
                <h4 style="margin-bottom: 10px;">What happens to my data if I cancel?</h4>
                <p style="margin: 0;">You can export all your ledgers, purchases, sales, and stock reports to Excel or PDF before you cancel. We keep your data securely archived for 60 days in case you decide to return.</p>
            </div>
            <div class="light-card mb-4" style="padding: 30px;">
                <h4 style="margin-bottom: 10px;">Is my financial data secure?</h4>
                <p style="margin: 0;">Yes. We use bank-grade AES-256 encryption. Your data is backed up daily and stored on secure cloud servers.</p>
            </div>
        </div>
    </div>
</section>

<!-- Section 13: FAQ Support -->
<section class="section bg-white border-top">
    <div class="container">
        <h3 class="mb-4" style="text-align: center;">Support & Training</h3>
        <div style="max-width: 800px; margin: 0 auto;">
            <div class="light-card mb-4" style="padding: 30px;">
                <h4 style="margin-bottom: 10px;">Will you help me import my old data?</h4>
                <p style="margin: 0;">Yes. Users on the Professional and Enterprise plans get complimentary onboarding support where our team helps you import your existing master data (parties, grains, godowns).</p>
            </div>
            <div class="light-card mb-4" style="padding: 30px;">
                <h4 style="margin-bottom: 10px;">Is training provided?</h4>
                <p style="margin: 0;">We provide comprehensive video tutorials and documentation. Enterprise customers also receive a 1-on-1 live training session for their staff.</p>
            </div>
        </div>
    </div>
</section>

<!-- Section 14: Value Proposition -->
<section class="section text-center">
    <div class="container">
        <h2>Save Time. Reduce Errors. Increase Profit.</h2>
        <p style="max-width: 700px; margin: 20px auto;">Most of our users report saving over 10 hours a week on manual accounting and reducing broker commission errors by 100%.</p>
    </div>
</section>

<!-- Section 15: Image Banner 3 -->
<section class="section" style="padding: 0;">
    <img src="https://images.unsplash.com/photo-1586201375761-83865001e31c?auto=format&fit=crop&w=1920&q=80" style="width: 100%; height: 300px; object-fit: cover;" alt="Grain">
</section>

<!-- Section 16: Still have questions? -->
<section class="section bg-white text-center">
    <div class="container">
        <h2 class="mb-3">Still have questions?</h2>
        <p class="mb-4">Our sales team is ready to answer any questions you have about pricing or features.</p>
        <a href="{{ route('landing.contact') }}" class="btn btn-outline">Contact Sales Team</a>
    </div>
</section>

<!-- Section 17: Payment Methods -->
<section class="section-sm text-center border-top">
    <div class="container">
        <p style="font-weight: bold; color: var(--text-muted); margin-bottom: 20px;">Accepted Payment Methods</p>
        <div style="display: flex; justify-content: center; gap: 30px; opacity: 0.5;">
            <span style="font-size: 24px; font-weight:bold;">UPI</span>
            <span style="font-size: 24px; font-weight:bold;">Visa</span>
            <span style="font-size: 24px; font-weight:bold;">Mastercard</span>
            <span style="font-size: 24px; font-weight:bold;">Net Banking</span>
        </div>
    </div>
</section>

<!-- Section 18: Custom Plan Notice -->
<section class="section bg-white border-top">
    <div class="container text-center">
        <div class="light-card" style="max-width: 800px; margin: 0 auto; background: rgba(42, 157, 143, 0.05); border-color: rgba(42, 157, 143, 0.2);">
            <h3>Need a Custom Solution?</h3>
            <p style="margin-top: 15px;">If you have massive scale requirements, custom ERP integrations, or need on-premise deployment, we can tailor a solution specifically for your enterprise.</p>
            <a href="{{ route('landing.contact') }}" class="btn btn-secondary mt-3">Talk to an Expert</a>
        </div>
    </div>
</section>

<!-- Section 19: Security Badge -->
<section class="section-sm text-center">
    <div class="container">
        <p style="display: flex; align-items: center; justify-content: center; color: var(--text-muted);">
            <i data-feather="lock" style="margin-right: 10px; width: 16px; height: 16px;"></i> Secure Checkout processed by Razorpay.
        </p>
    </div>
</section>

<!-- Section 20: Final CTA -->
<section class="section text-center" style="background: url('https://images.unsplash.com/photo-1574323347407-f5e1ad6d020b?auto=format&fit=crop&w=1920&q=80') center/cover; position: relative;">
    <div style="position: absolute; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.8);"></div>
    <div class="container" style="position: relative; z-index: 2; color: #fff; padding: 80px 0;">
        <h2 style="font-size: 48px; color: #fff; margin-bottom: 20px;">Ready to start?</h2>
        <p style="font-size: 18px; max-width: 600px; margin: 0 auto 30px; color: rgba(255,255,255,0.8);">Set up your account in less than 2 minutes.</p>
        <a href="{{ route('login') }}" class="btn btn-primary" style="font-size: 18px; padding: 15px 40px;">Create Free Account</a>
    </div>
</section>

@endsection
