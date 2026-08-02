<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function home()
    {
        return view('landing.home');
    }

    public function features()
    {
        return view('landing.features');
    }

    public function pricing()
    {
        return view('landing.pricing');
    }

    public function about()
    {
        return view('landing.about');
    }

    public function contact()
    {
        return view('landing.contact');
    }
    
    public function submitContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);
        
        // Normally this would send an email. For now we just return back with success.
        return back()->with('success', 'Your message has been sent successfully. Our team will contact you shortly.');
    }

    public function legal()
    {
        return view('landing.legal');
    }
}
