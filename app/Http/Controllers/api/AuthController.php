<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Admin login - validates password against settings.
     * POST /api/login
     *
     * Replaces the local storage password check in admin-login.page.ts
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'password' => 'required|string',
        ]);

        $settings = Settings::getMain();

        if (!$settings) {
            return response()->json([
                'success' => false,
                'message' => 'Settings not configured',
            ], 500);
        }

        // Check if password matches (plain text comparison to match existing app behavior)
        if ($validated['password'] !== $settings->admin_password) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid password. Please try again.',
            ], 401);
        }

        // Generate a simple API token for the admin session
        $token = bin2hex(random_bytes(32));

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'token' => $token,
            'settings' => $settings,
        ]);
    }

    /**
     * Get current settings (for authenticated admin).
     * GET /api/me
     */
    public function me(): JsonResponse
    {
        $settings = Settings::getMain();

        if (!$settings) {
            return response()->json([
                'success' => false,
                'message' => 'Settings not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $settings,
        ]);
    }
}
