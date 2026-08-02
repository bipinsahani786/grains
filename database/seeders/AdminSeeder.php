<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Core\User;
use App\Models\System\Company;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create a Super Admin
        // Super Admin has company_id = null
        User::create([
            'name' => 'Platform Super Admin',
            'email' => 'superadmin@grainsaas.com',
            'password' => Hash::make('password123'),
            'role' => 'super_admin',
            'login_enabled' => true,
        ]);

        // 2. Create a Dummy Company (Tenant)
        $company = Company::create([
            'name' => 'Acme Grains Traders',
            'email' => 'info@acmegrains.com',
            'is_active' => true,
        ]);

        // 3. Create a Business Admin for this Company
        User::create([
            'company_id' => $company->id,
            'name' => 'Business Admin',
            'email' => 'admin@acmegrains.com',
            'password' => Hash::make('password123'),
            'login_enabled' => true,
        ]);
    }
}
