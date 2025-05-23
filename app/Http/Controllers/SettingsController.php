<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSettingsRequest;
use App\Models\Setting;

class SettingsController extends Controller
{
    public function edit()
    {
        $setting = Setting::first();

        if (! $setting) {
            $setting = Setting::create([
                'title' => config('app.name'),
                'maintenance_mode' => false,
            ]);
        }

        return view('pageSettings.edit', compact('setting'));
    }

    public function update(UpdateSettingsRequest $request)
    {
        $setting = Setting::first();
        $setting->update($request->validated());

        return redirect()->back()->with('success', 'Settings updated successfully');
    }
}
