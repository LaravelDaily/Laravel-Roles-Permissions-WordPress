<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSettingsRequest;
use App\Models\Setting;
use Illuminate\Support\Facades\Gate;

class SettingsController extends Controller
{
    public function edit()
    {
        Gate::authorize('view', Setting::class);

        $setting = Setting::first();

        if (! $setting) {
            $setting = Setting::create([
                'title' => config('app.name'),
                'maintenance_mode' => false,
            ]);
        }

        return view('pageSettings.edit', [
            'setting' => $setting,
        ]);
    }

    public function update(UpdateSettingsRequest $request)
    {
        Gate::authorize('update', Setting::class);

        $setting = Setting::first();
        $setting->update($request->validated());

        return redirect()->back()->with('status', 'Settings updated successfully');
    }
}
