<?php

namespace Modules\CIMSTyreDash\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\CIMSTyreDash\Models\TyreDashSetting;

class SettingsController extends Controller
{
    /**
     * Show all settings grouped by category.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $settingsGrouped = TyreDashSetting::getAllGrouped();

        // Define group labels for display
        $groupLabels = [
            TyreDashSetting::GROUP_GENERAL  => 'General Settings',
            TyreDashSetting::GROUP_PRICING  => 'Pricing Settings',
            TyreDashSetting::GROUP_QUOTES   => 'Quote Settings',
            TyreDashSetting::GROUP_JOBCARDS => 'Job Card Settings',
            TyreDashSetting::GROUP_STOCK    => 'Stock Settings',
        ];

        return view('cimstyredash::settings.index', compact('settingsGrouped', 'groupLabels'));
    }

    /**
     * Save settings submitted from the settings form.
     *
     * Accepts a flat array of setting_key => setting_value pairs.
     *
     * @param  Request $request
     * @return RedirectResponse
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'settings'   => 'required|array',
            'settings.*' => 'nullable|string|max:500',
        ]);

        $settings = $validated['settings'];

        foreach ($settings as $key => $value) {
            // Only update existing settings; do not create arbitrary new ones from form
            $existing = TyreDashSetting::where('setting_key', $key)->first();
            if ($existing) {
                $existing->update(['setting_value' => $value]);
            }
        }

        // Clear the cache so fresh values are loaded
        TyreDashSetting::clearCache();

        return redirect()
            ->route('cimstyredash.settings.index')
            ->with('success', 'Settings updated successfully.');
    }
}
