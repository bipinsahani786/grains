<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\System\PlatformSetting;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $brandName = PlatformSetting::where('key', 'brand_name')->value('value') ?? 'Platform Name';
        $logoPath = PlatformSetting::where('key', 'logo_path')->value('value');
        $faviconPath = PlatformSetting::where('key', 'favicon_path')->value('value');

        return view('superadmin.settings.index', compact('brandName', 'logoPath', 'faviconPath'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'brand_name' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png,jpg|max:1024',
        ]);

        if ($request->has('brand_name')) {
            PlatformSetting::updateOrCreate(
                ['key' => 'brand_name'],
                ['value' => $request->brand_name]
            );
        }

        if ($request->hasFile('logo')) {
            $logoFile = $request->file('logo');
            $logoPath = $logoFile->store('platform/logo', 'public');

            PlatformSetting::updateOrCreate(
                ['key' => 'logo_path'],
                ['value' => $logoPath]
            );
        }

        if ($request->hasFile('favicon')) {
            $faviconFile = $request->file('favicon');
            $faviconPath = $faviconFile->store('platform/favicon', 'public');

            PlatformSetting::updateOrCreate(
                ['key' => 'favicon_path'],
                ['value' => $faviconPath]
            );
        }

        return redirect()->route('superadmin.settings.index')->with('success', 'Settings updated successfully.');
    }
}
