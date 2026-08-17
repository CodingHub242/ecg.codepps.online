<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Admin login - validates password against users table.
     * POST /api/login
     *
     * Replaces the local storage password check in admin-login.page.ts
     * Now checks the Laravel users table instead of settings table.
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'password' => 'required|string',
        ]);

        // Check the users table for an admin user with matching password
        $admin = User::where('is_admin', true)->first();

        if (!$admin || !Hash::check($validated['password'], $admin->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid password. Please try again.',
            ], 401);
        }

        // Generate a simple API token for the admin session
        $token = bin2hex(random_bytes(32));

        // Also fetch settings to return to the client
        $settings = Settings::getMain();

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
