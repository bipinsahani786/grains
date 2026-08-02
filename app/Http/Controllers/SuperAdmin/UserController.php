<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Core\User;
use App\Models\System\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        // For super admin, showing only admins and super_admins (platform users)
        $data = User::with('company')
                    ->whereIn('role', ['admin', 'super_admin'])
                    ->latest()
                    ->paginate(10);
        return view('superadmin.users.index', compact('data'));
    }

    public function create()
    {
        return view('superadmin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8',
            'address' => 'nullable|string',
            'aadhar_no' => 'nullable|string|max:20',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['login_enabled'] = $request->has('login_enabled');
        $validated['is_active'] = $request->has('is_active');
        $validated['created_by'] = auth()->id();
        $validated['role'] = 'admin';

        // Auto-create a company for this new admin
        $company = \App\Models\System\Company::create([
            'name' => $validated['name'] . ' Business',
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'status' => 'active',
        ]);

        $validated['company_id'] = $company->id;

        User::create($validated);

        return redirect()->route('superadmin.users.index')->with('success', 'User created successfully!');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('superadmin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8',
            'address' => 'nullable|string',
            'aadhar_no' => 'nullable|string|max:20',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['login_enabled'] = $request->has('login_enabled');
        $validated['is_active'] = $request->has('is_active');

        $user->update($validated);

        return redirect()->route('superadmin.users.index')->with('success', 'User updated successfully!');
    }

    public function destroy($id)
    {
        // Prevent self-deletion
        if (auth()->id() == $id) {
            return redirect()->route('superadmin.users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $user = User::findOrFail($id);

        // Prevent deleting other super admins
        if ($user->role === 'super_admin') {
            return redirect()->route('superadmin.users.index')
                ->with('error', 'Cannot delete a Super Admin account.');
        }

        $user->delete();
        return redirect()->route('superadmin.users.index')->with('success', 'User deleted successfully!');
    }
}