<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Designation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SettingController extends Controller
{
    /**
     * Show all existing settings.
     */
    public function index()
    {
        $designations = Designation::orderBy('id')->get();

        $settings = Setting::orderBy('group')
            ->orderBy('id')
            ->paginate(20);

        return view('admin.settings.index', compact('settings', 'designations'));
    }

    /**
     * Update an existing setting.
     */
    public function update(Request $request, Setting $setting)
    {
        $validated = $request->validate([
            'value' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['0', '1', 0, 1, true, false])],
        ]);

        $setting->value = $validated['value'] ?? null;
        $setting->status = (bool) $validated['status'];
        $setting->save();

        return redirect()
            ->route('settings.index')
            ->with('success', 'Setting updated successfully.');
    }
}
