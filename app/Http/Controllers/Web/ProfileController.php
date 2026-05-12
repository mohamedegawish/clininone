<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit', [
            'user' => auth()->user()
        ]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return back()->with('success', 'تم تحديث الملف الشخصي بنجاح');
    }

    public function settings()
    {
        return view('profile.settings', [
            'system_name' => Setting::get('system_name', 'كلينيك وان'),
            'system_logo' => Setting::get('system_logo'),
            'public_logo' => Setting::get('public_logo'),
            'landing_bg'  => Setting::get('landing_bg'),
        ]);
    }

    public function updateSystemSettings(Request $request)
    {
        $request->validate([
            'system_name' => 'required|string|max:255',
            'system_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'public_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'landing_bg'  => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
        ]);

        Setting::set('system_name', $request->system_name);

        // Handle System Logo
        if ($request->hasFile('system_logo')) {
            $this->uploadSettingFile($request->file('system_logo'), 'system_logo', 'logo_');
        }

        // Handle Public Logo
        if ($request->hasFile('public_logo')) {
            $this->uploadSettingFile($request->file('public_logo'), 'public_logo', 'public_logo_');
        }

        // Handle Landing Background
        if ($request->hasFile('landing_bg')) {
            $this->uploadSettingFile($request->file('landing_bg'), 'landing_bg', 'bg_');
        }

        return back()->with('success', 'تم تحديث إعدادات النظام بنجاح');
    }

    private function uploadSettingFile($file, $key, $prefix)
    {
        $filename = $prefix . time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads/settings'), $filename);

        // Delete old file if exists
        $oldFile = Setting::get($key);
        if ($oldFile && file_exists(public_path('uploads/settings/' . $oldFile))) {
            @unlink(public_path('uploads/settings/' . $oldFile));
        }

        Setting::set($key, $filename);
    }
}
