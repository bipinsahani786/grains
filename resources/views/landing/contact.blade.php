@extends('layouts.landing')

@section('title', 'Contact Us - Grain SaaS')

@section('content')

<!-- Section 1: Page Header -->
<section class="section" style="padding-top: 150px; text-align: center; background: url('https://images.unsplash.com/photo-1574323347407-f5e1ad6d020b?auto=format&fit=crop&w=1920&q=80') center/cover; position: relative; color: #fff;">
    <div style="position: absolute; top:0; left:0; width:100%; height:100%; background: rgba(38, 70, 83, 0.9);"></div>
    <div class="container" style="position: relative; z-index: 2;">
        <h1 style="font-size: 56px; margin-bottom: 20px; color: #fff;">Get in <span style="color: var(--primary-light);">Touch</span></h1>
        <p style="font-size: 20px; max-width: 700px; margin: 0 auto; color: rgba(255,255,255,0.8);">Have questions about our software? Need a custom demo? Our team is here to help.</p>
    </div>
</section>

<!-- Section 2: Contact Options Grid -->
<section class="section bg-white" style="border-bottom: 1px solid var(--border-light);">
    <div class="container">
        <div class="features-grid">
            <div class="light-card text-center" style="padding: 40px 20px;">
                <i data-feather="message-circle" style="width:50px; height:50px; color:var(--primary-dark); margin-bottom:20px;"></i>
                <h3>Chat with Sales</h3>
                <p>Speak directly with our product experts.</p>
                <p style="font-weight:bold; color:var(--text-main);">sales@grainsaas.com</p>
            </div>
            <div class="light-card text-center" style="padding: 40px 20px;">
                <i data-feather="headphones" style="width:50px; height:50px; color:var(--primary-dark); margin-bottom:20px;"></i>
                <h3>Technical Support</h3>
                <p>Get help with your existing account.</p>
                <p style="font-weight:bold; color:var(--text-main);">support@grainsaas.com</p>
            </div>
            <div class="light-card text-center" style="padding: 40px 20px;">
                <i data-feather="phone-call" style="width:50px; height:50px; color:var(--primary-dark); margin-bottom:20px;"></i>
                <h3>Call Us</h3>
                <p>Mon-Sat, 9AM to 7PM (IST)</p>
                <p style="font-weight:bold; color:var(--text-main);">+91 98765 43210</p>
            </div>
        </div>
    </div>
</section>

<!-- Section 3: Contact Form Layout -->
<section class="section">
    <div class="container">
        
        @if(session('success'))
            <div style="background: rgba(42, 157, 143, 0.1); border: 1px solid var(--accent-green); color: var(--accent-green); padding: 20px; border-radius: 8px; margin-bottom: 40px; text-align: center; font-weight: bold;">
                <i data-feather="check-circle" style="margin-right: 10px; vertical-align: middle;"></i>
                {{ session('success') }}
            </div>
        @endif

        <div class="contact-container">
            <!-- Left Side Info -->
            <div class="contact-info">
                <h2 style="margin-bottom: 20px;">Send us a message</h2>
                <p class="mb-5" style="font-size: 18px;">Fill out the form and a member of our team will get back to you within 24 hours.</p>
                
                <div class="light-card" style="padding: 30px; margin-bottom: 30px;">
                    <h4 style="color: var(--primary-dark); margin-bottom: 15px;"><i data-feather="clock" style="margin-right: 10px;"></i> Business Hours</h4>
                    <ul style="list-style:none; padding:0; margin:0;">
                        <li style="display:flex; justify-content:space-between; margin-bottom:10px;"><span>Monday - Friday</span> <span>9:00 AM - 7:00 PM</span></li>
                        <li style="display:flex; justify-content:space-between; margin-bottom:10px;"><span>Saturday</span> <span>9:00 AM - 2:00 PM</span></li>
                        <li style="display:flex; justify-content:space-between; color: var(--text-light);"><span>Sunday</span> <span>Closed</span></li>
                    </ul>
                </div>
                
                <img src="https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&w=800&q=80" style="width: 100%; border-radius: 12px; height: 200px; object-fit: cover;" alt="Contact">
            </div>
            
            <!-- Form -->
            <div class="light-card" style="padding: 40px;">
                <form action="{{ route('landing.contact.submit') }}" method="POST">
                    @csrf
                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div class="form-group">
                            <label style="display:block; margin-bottom:8px; color:var(--text-main); font-weight:600;">First Name *</label>
                            <input type="text" name="name" class="form-control" required placeholder="John">
                        </div>
                        <div class="form-group">
                            <label style="display:block; margin-bottom:8px; color:var(--text-main); font-weight:600;">Last Name *</label>
                            <input type="text" class="form-control" required placeholder="Doe">
                        </div>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display:block; margin-bottom:8px; color:var(--text-main); font-weight:600;">Email Address *</label>
                        <input type="email" name="email" class="form-control" required placeholder="john@example.com">
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display:block; margin-bottom:8px; color:var(--text-main); font-weight:600;">Phone Number</label>
                        <input type="text" class="form-control" placeholder="+91 00000 00000">
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display:block; margin-bottom:8px; color:var(--text-main); font-weight:600;">Subject *</label>
                        <select name="subject" class="form-control" required>
                            <option value="">Select a subject...</option>
                            <option value="Sales Inquiry">Sales Inquiry / Demo Request</option>
                            <option value="Technical Support">Technical Support</option>
                            <option value="Billing Issue">Billing Issue</option>
                            <option value="Partnership">Partnership / Integration</option>
                        </select>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 30px;">
                        <label style="display:block; margin-bottom:8px; color:var(--text-main); font-weight:600;">Message *</label>
                        <textarea name="message" class="form-control" required rows="5" placeholder="How can we help you today?"></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%; font-size: 18px; padding: 15px;">Send Message</button>
                    <p style="font-size: 13px; color: var(--text-light); margin-top: 15px; text-align: center;">By submitting this form, you agree to our Privacy Policy.</p>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Section 4: Image Break -->
<section class="section" style="padding: 0;">
    <img src="https://images.unsplash.com/photo-1505553531637-a1288e84e55e?auto=format&fit=crop&w=1920&q=80" style="width: 100%; height: 300px; object-fit: cover;" alt="Sacks of grain">
</section>

<!-- Section 5: Map Header -->
<section class="section bg-white pb-0 text-center">
    <div class="container">
        <h2>Visit Our Office</h2>
        <p>Come see us in person. We'd love to show you around.</p>
    </div>
</section>

<!-- Section 6: Map Location 1 -->
<section class="section bg-white">
    <div class="container">
        <div class="split-section">
            <div class="split-content">
                <h3 style="color: var(--primary-dark);">New Delhi HQ</h3>
                <p>123 Tech Park, Tower A, Business District</p>
                <p>New Delhi, 110001, India</p>
            </div>
            <div class="split-image" style="background: #f0f0f0; height: 300px; display:flex; align-items:center; justify-content:center; border-radius:12px;">
                <i data-feather="map-pin" style="width:40px; height:40px; color:var(--text-muted);"></i>
            </div>
        </div>
    </div>
</section>

<!-- Section 7: Map Location 2 -->
<section class="section bg-white pt-0">
    <div class="container">
        <div class="split-section reverse">
            <div class="split-content">
                <h3 style="color: var(--primary-dark);">Mumbai Branch</h3>
                <p>456 Trade Center, Floor 5</p>
                <p>Mumbai, 400001, India</p>
            </div>
            <div class="split-image" style="background: #f0f0f0; height: 300px; display:flex; align-items:center; justify-content:center; border-radius:12px;">
                <i data-feather="map-pin" style="width:40px; height:40px; color:var(--text-muted);"></i>
            </div>
        </div>
    </div>
</section>

<!-- Section 8: Divider -->
<div style="width: 100%; height: 1px; background: var(--border-light);"></div>

<!-- Section 9: FAQ For Support -->
<section class="section text-center">
    <div class="container">
        <h2 class="mb-5">Before you ask...</h2>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; text-align: left; max-width: 900px; margin: 0 auto;">
            <div class="light-card">
                <h4>I forgot my password.</h4>
                <p>You can reset it by clicking the "Forgot Password" link on the login page.</p>
            </div>
            <div class="light-card">
                <h4>How do I upgrade?</h4>
                <p>Go to your dashboard, click Settings, then Billing, and select your new plan.</p>
            </div>
        </div>
    </div>
</section>

<!-- Section 10: Social Media Header -->
<section class="section bg-dark text-center">
    <div class="container">
        <h2 style="color: #fff;">Connect with us on Social Media</h2>
    </div>
</section>

<!-- Section 11: Social Grid -->
<section class="section pt-0 bg-dark" style="padding-bottom: 80px;">
    <div class="container text-center">
        <div style="display: flex; justify-content: center; gap: 30px;">
            <a href="#" style="background: rgba(255,255,255,0.1); width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; border-radius: 50%; color: #fff;"><i data-feather="twitter"></i></a>
            <a href="#" style="background: rgba(255,255,255,0.1); width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; border-radius: 50%; color: #fff;"><i data-feather="facebook"></i></a>
            <a href="#" style="background: rgba(255,255,255,0.1); width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; border-radius: 50%; color: #fff;"><i data-feather="instagram"></i></a>
            <a href="#" style="background: rgba(255,255,255,0.1); width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; border-radius: 50%; color: #fff;"><i data-feather="linkedin"></i></a>
        </div>
    </div>
</section>

<!-- Section 12: Sales Team Intro -->
<section class="section bg-white text-center border-bottom">
    <div class="container">
        <h2>Talk directly to our Sales Heads</h2>
        <p>Large enterprise? Skip the form and email our directors directly.</p>
    </div>
</section>

<!-- Section 13: Sales Contacts -->
<section class="section bg-white pt-0">
    <div class="container">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; max-width: 800px; margin: 0 auto;">
            <div class="light-card text-center">
                <h4 style="color: var(--primary-dark);">North India Sales</h4>
                <p>amit.sales@grainsaas.com</p>
            </div>
            <div class="light-card text-center">
                <h4 style="color: var(--primary-dark);">South India Sales</h4>
                <p>rahul.sales@grainsaas.com</p>
            </div>
        </div>
    </div>
</section>

<!-- Section 14: Support Center Link -->
<section class="section">
    <div class="container text-center">
        <i data-feather="book" style="width: 50px; height: 50px; color: var(--secondary); margin-bottom: 20px;"></i>
        <h2>Need help with a specific feature?</h2>
        <p style="margin-bottom: 20px;">Check out our comprehensive knowledge base with over 100+ articles and video tutorials.</p>
        <a href="#" class="btn btn-outline">Visit Knowledge Base</a>
    </div>
</section>

<!-- Section 15: Image Banner 4 -->
<section class="section" style="padding: 0;">
    <img src="https://images.unsplash.com/photo-1586201375761-83865001e31c?auto=format&fit=crop&w=1920&q=80" style="width: 100%; height: 250px; object-fit: cover;" alt="Grain">
</section>

<!-- Section 16: Newsletter Subscribe -->
<section class="section bg-white border-bottom">
    <div class="container text-center">
        <h2>Subscribe to Market Updates</h2>
        <p style="max-width: 600px; margin: 0 auto 30px;">Get weekly insights into the grain trading industry and software updates.</p>
        <form style="display: flex; max-width: 500px; margin: 0 auto; gap: 10px;">
            <input type="email" class="form-control" placeholder="Enter your email" required>
            <button class="btn btn-secondary" type="button">Subscribe</button>
        </form>
    </div>
</section>

<!-- Section 17: Partnerships -->
<section class="section">
    <div class="container text-center">
        <h2>Looking to Partner?</h2>
        <p style="max-width: 700px; margin: 0 auto 30px;">We partner with CA firms, ERP integrators, and Mandi associations.</p>
        <a href="mailto:partners@grainsaas.com" class="btn btn-outline">Email partners@grainsaas.com</a>
    </div>
</section>

<!-- Section 18: Demo Request Special CTA -->
<section class="section bg-dark">
    <div class="container text-center" style="color: #fff;">
        <h2 style="color: #fff; margin-bottom: 20px;">Request a Personalized Demo</h2>
        <p style="max-width: 600px; margin: 0 auto 30px; color: rgba(255,255,255,0.7);">Our team will walk you through exactly how Grain SaaS can solve the unique challenges of your specific Godown setup.</p>
    </div>
</section>

<!-- Section 19: Privacy Assurance -->
<section class="section-sm text-center border-top bg-white">
    <div class="container">
        <p style="color: var(--text-muted); font-size: 14px;"><i data-feather="lock" style="width: 14px; height: 14px;"></i> Your information is secure. We never sell your data to third parties.</p>
    </div>
</section>

<!-- Section 20: Bottom Buffer CTA -->
<section class="section" style="padding: 0;">
    <div style="background: linear-gradient(135deg, var(--primary), var(--primary-dark)); padding: 60px 0; text-align: center; color: #fff;">
        <div class="container">
            <h3 style="color: #fff; margin-bottom: 10px;">Don't wait. Digitize your trade today.</h3>
            <a href="{{ route('login') }}" class="btn" style="background: #fff; color: var(--primary-dark) !important; margin-top: 20px;">Create Free Account</a>
        </div>
    </div>
</section>

@endsection
