<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SettingsController extends Controller
{
    /**
     * Get the main settings.
     * If settings don't exist, create default settings.
     * GET /api/settings
     *
     * Replaces Firebase: getSettings()
     */
    public function index(): JsonResponse
    {
        $settings = Settings::getMain();

        // If settings don't exist, create default settings
        if (!$settings) {
            $settings = Settings::create([
                'key' => 'main',
                'work_start_time' => '09:00',
                'work_end_time' => '17:00',
                'admin_password' => 'admin123',
                'company_name' => 'SiobhanHub',
                'updated_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $settings,
        ]);
    }

    /**
     * Update the main settings.
     * If settings don't exist, create them.
     * PUT /api/settings
     *
     * Replaces Firebase: updateSettings()
     */
    public function update(Request $request): JsonResponse
    {
        $settings = Settings::getMain();

        if (!$settings) {
            // Create if doesn't exist
            $settings = new Settings();
            $settings->key = 'main';
        }

        $validated = $request->validate([
            'work_start_time' => 'nullable|string|max:255',
            'work_end_time' => 'nullable|string|max:255',
            'admin_password' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
        ]);

        $settings->fill($validated);
        $settings->updated_at = now();
        $settings->save();

        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully',
            'data' => $settings,
        ]);
    }
}
