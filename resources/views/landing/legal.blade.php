@extends('layouts.landing')

@section('title', 'Legal & Privacy - Grain SaaS')

@section('content')

<!-- Section 1: Page Header -->
<section class="section bg-white" style="padding-top: 150px; text-align: center; border-bottom: 1px solid var(--border-light);">
    <div class="container">
        <h1 style="font-size: 56px; margin-bottom: 20px;">Legal & <span class="text-gradient">Privacy</span></h1>
        <p style="font-size: 20px; max-width: 700px; margin: 0 auto; color: var(--text-muted);">Everything you need to know about how we protect your data and the terms of using our service.</p>
        <p style="font-size: 14px; margin-top: 20px; color: var(--text-light);">Last Updated: {{ date('F d, Y') }}</p>
    </div>
</section>

<!-- Section 2: Quick Links Directory -->
<section class="section-sm bg-light border-bottom">
    <div class="container text-center">
        <div style="display: flex; justify-content: center; gap: 20px; flex-wrap: wrap;">
            <a href="#tos" class="btn btn-outline" style="padding: 10px 20px; font-size: 14px;">Terms of Service</a>
            <a href="#privacy" class="btn btn-outline" style="padding: 10px 20px; font-size: 14px;">Privacy Policy</a>
            <a href="#data" class="btn btn-outline" style="padding: 10px 20px; font-size: 14px;">Data Protection</a>
            <a href="#cookies" class="btn btn-outline" style="padding: 10px 20px; font-size: 14px;">Cookie Policy</a>
            <a href="#refund" class="btn btn-outline" style="padding: 10px 20px; font-size: 14px;">Refund Policy</a>
        </div>
    </div>
</section>

<!-- Section 3: Intro to Legal -->
<section class="section pt-5 pb-5">
    <div class="container">
        <div class="light-card" style="max-width: 900px; margin: 0 auto;">
            <p style="font-size: 18px; margin: 0;">Please read these terms carefully. By accessing or using the Grain SaaS platform, you agree to be bound by these Terms of Service and all terms incorporated by reference.</p>
        </div>
    </div>
</section>

<div class="container"><div style="max-width: 900px; margin: 0 auto;">

<!-- Section 4: Terms of Service - General -->
<section id="tos" class="section-sm pt-0 pb-4 border-bottom">
    <h2 style="color: var(--primary-dark); margin-bottom: 20px;">1. Terms of Service</h2>
    <h3>1.1 Acceptance of Terms</h3>
    <p>By registering for and/or using the Services in any manner, including but not limited to visiting or browsing the Site, you agree to these Terms of Service and all other operating rules, policies, and procedures that may be published from time to time on the Site by us.</p>
</section>

<!-- Section 5: Terms of Service - Accounts -->
<section class="section-sm py-4 border-bottom">
    <h3>1.2 Account Registration</h3>
    <p>You must provide accurate and complete information and keep your Account information updated. You shall not select or use as a username a name of another person with the intent to impersonate that person.</p>
</section>

<!-- Section 6: Terms of Service - Responsibilities -->
<section class="section-sm py-4 border-bottom">
    <h3>1.3 User Responsibilities</h3>
    <p>You are solely responsible for the activity that occurs on your Account. You must ensure all data entered into the system complies with your local tax laws and regulations. Grain SaaS is not responsible for the accuracy of the financial data you input.</p>
</section>

<!-- Section 7: Privacy Policy - Intro -->
<section id="privacy" class="section-sm py-4 border-bottom">
    <h2 style="color: var(--primary-dark); margin-bottom: 20px; margin-top: 20px;">2. Privacy Policy</h2>
    <h3>2.1 What data we collect</h3>
    <p>We collect information you provide directly to us. For example, we collect information when you create an account, fill out a form, request customer support, or otherwise communicate with us.</p>
</section>

<!-- Section 8: Privacy Policy - Usage -->
<section class="section-sm py-4 border-bottom">
    <h3>2.2 How we use your data</h3>
    <p>We use the information we collect to provide, maintain, and improve our services. We do not sell your personal or financial data to third parties. We may use anonymized, aggregated data for system analytics.</p>
</section>

<!-- Section 9: Data Protection - Encryption -->
<section id="data" class="section-sm py-4 border-bottom">
    <h2 style="color: var(--primary-dark); margin-bottom: 20px; margin-top: 20px;">3. Data Protection Addendum</h2>
    <h3>3.1 Encryption</h3>
    <p>Your data is yours. We encrypt all sensitive financial information at rest using AES-256 and in transit using TLS 1.2+.</p>
</section>

<!-- Section 10: Data Protection - Backups -->
<section class="section-sm py-4 border-bottom">
    <h3>3.2 Backups and Recovery</h3>
    <p>We perform daily automated backups of your ledger databases. In the event of a system failure, we can restore your data from these snapshots.</p>
</section>

<!-- Section 11: Image Break -->
</div></div>
<section class="section" style="padding: 0;">
    <img src="https://images.unsplash.com/photo-1505553531637-a1288e84e55e?auto=format&fit=crop&w=1920&q=80" style="width: 100%; height: 200px; object-fit: cover; opacity: 0.8;" alt="Sacks of grain">
</section>
<div class="container"><div style="max-width: 900px; margin: 0 auto;">

<!-- Section 12: Cookie Policy -->
<section id="cookies" class="section-sm py-4 border-bottom">
    <h2 style="color: var(--primary-dark); margin-bottom: 20px; margin-top: 40px;">4. Cookie Policy</h2>
    <p>We use cookies and similar tracking technologies to track the activity on our Service and hold certain information. You can instruct your browser to refuse all cookies or to indicate when a cookie is being sent.</p>
</section>

<!-- Section 13: Refund Policy -->
<section id="refund" class="section-sm py-4 border-bottom">
    <h2 style="color: var(--primary-dark); margin-bottom: 20px; margin-top: 20px;">5. Refund Policy</h2>
    <p>Subscriptions can be cancelled at any time. We do not offer prorated refunds for partial months used, but you will retain access to the system until the end of your billing cycle. If you cancel within the 14-day trial period, you will not be charged.</p>
</section>

<!-- Section 14: Limitation of Liability -->
<section class="section-sm py-4 border-bottom">
    <h2 style="color: var(--primary-dark); margin-bottom: 20px; margin-top: 20px;">6. Limitation of Liability</h2>
    <p>In no event shall Grain SaaS, nor its directors, employees, partners, agents, suppliers, or affiliates, be liable for any indirect, incidental, special, consequential or punitive damages, including without limitation, loss of profits, data, use, goodwill, or other intangible losses.</p>
</section>

<!-- Section 15: Governing Law -->
<section class="section-sm py-4 border-bottom">
    <h2 style="color: var(--primary-dark); margin-bottom: 20px; margin-top: 20px;">7. Governing Law</h2>
    <p>These Terms shall be governed and construed in accordance with the laws of India, without regard to its conflict of law provisions.</p>
</section>

<!-- Section 16: Changes to Terms -->
<section class="section-sm py-4">
    <h2 style="color: var(--primary-dark); margin-bottom: 20px; margin-top: 20px;">8. Changes to Terms</h2>
    <p>We reserve the right, at our sole discretion, to modify or replace these Terms at any time. What constitutes a material change will be determined at our sole discretion.</p>
</section>

</div></div>

<!-- Section 17: Support Box -->
<section class="section bg-white border-top">
    <div class="container text-center">
        <div class="light-card" style="max-width: 800px; margin: 0 auto;">
            <h3>Have Legal Questions?</h3>
            <p>If you have any questions about these Terms, please contact our legal department.</p>
            <a href="mailto:legal@grainsaas.com" class="btn btn-outline mt-3">legal@grainsaas.com</a>
        </div>
    </div>
</section>

<!-- Section 18: Security Certifications Info -->
<section class="section pt-0 bg-white">
    <div class="container text-center">
        <p style="color: var(--text-muted);"><i data-feather="shield" style="width: 14px; height: 14px;"></i> SOC2 Type II Certified | ISO 27001 Compliant Data Centers</p>
    </div>
</section>

<!-- Section 19: Report Vulnerability -->
<section class="section-sm bg-dark text-center">
    <div class="container">
        <p style="color: rgba(255,255,255,0.7); margin: 0;">Found a security vulnerability? We run a bug bounty program.</p>
        <a href="#" style="color: var(--primary-light); text-decoration: underline;">Report an issue here.</a>
    </div>
</section>

<!-- Section 20: Bottom Buffer -->
<section class="section text-center" style="background: url('https://images.unsplash.com/photo-1574323347407-f5e1ad6d020b?auto=format&fit=crop&w=1920&q=80') center/cover; position: relative;">
    <div style="position: absolute; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.8);"></div>
    <div class="container" style="position: relative; z-index: 2; color: #fff; padding: 60px 0;">
        <h2 style="font-size: 32px; color: #fff; margin-bottom: 20px;">Ready to trade securely?</h2>
        <a href="{{ route('login') }}" class="btn btn-primary" style="font-size: 18px; padding: 15px 40px;">Create Free Account</a>
    </div>
</section>

@endsection
