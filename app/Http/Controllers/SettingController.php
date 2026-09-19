<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $setting = Setting::firstOrCreate(['id' => 1]);
        return view('settings.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $setting = Setting::firstOrCreate(['id' => 1]);

        $request->validate([
            'shop_name' => 'required|string|max:255',
            'shop_address' => 'required|string|max:255',
            'shop_phone' => 'required|string|max:255',
            'shop_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'owner_name' => 'nullable|string|max:255',
            'owner_signature' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'bank_account' => 'nullable|string'
        ]);

        $data = $request->only(['shop_name', 'shop_address', 'shop_phone', 'owner_name', 'bank_account']);

        if ($request->hasFile('shop_logo')) {
            // Delete old logo
            if ($setting->shop_logo && Storage::disk('public')->exists($setting->shop_logo)) {
                Storage::disk('public')->delete($setting->shop_logo);
            }
            $data['shop_logo'] = $request->file('shop_logo')->store('logos', 'public');
        }

        if ($request->hasFile('owner_signature')) {
            // Delete old signature
            if ($setting->owner_signature && Storage::disk('public')->exists($setting->owner_signature)) {
                Storage::disk('public')->delete($setting->owner_signature);
            }
            $data['owner_signature'] = $request->file('owner_signature')->store('signatures', 'public');
        }

        $setting->update($data);

        return back()->with('success', 'Pengaturan toko berhasil diperbarui!');
    }
}
