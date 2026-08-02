<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Core\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonateController extends Controller
{
    public function start($id)
    {
        $adminToImpersonate = User::findOrFail($id);

        // Security check: Only super admins can impersonate
        if (auth()->user()->role !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        // Security check: Don't let a super admin impersonate another super admin to avoid confusion
        if ($adminToImpersonate->role === 'super_admin') {
            return redirect()->back()->with('error', 'Cannot impersonate another Super Admin.');
        }

        // Store current user id in session
        session(['impersonated_by' => auth()->id()]);

        // Login as the target admin
        Auth::loginUsingId($adminToImpersonate->id);

        return redirect()->route('business.dashboard')->with('success', 'You are now impersonating ' . $adminToImpersonate->name);
    }

    public function stop()
    {
        if (session()->has('impersonated_by')) {
            $superAdminId = session('impersonated_by');
            
            // Login back as super admin
            Auth::loginUsingId($superAdminId);
            
            // Remove the session key
            session()->forget('impersonated_by');

            return redirect()->route('superadmin.dashboard')->with('success', 'Returned to your Super Admin account.');
        }

        return redirect('/');
    }
}
