<?php

namespace App\Http\Controllers;

use App\Models\TUser;
use App\Services\ArkeselSmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected $smsService;

    public function __construct(ArkeselSmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:20',
            'role' => 'nullable|in:admin,employee',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $user = TUser::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role' => $request->role ?? 'employee',
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        return $this->successResponse([
            'user' => $user,
            'token' => $token,
        ], 'User registered successfully', 201);
    }

    /**
     * Login user
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $user = TUser::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->errorResponse('Invalid credentials', 401);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return $this->successResponse([
            'user' => $user,
            'token' => $token,
            'role' => $user->role,
        ], 'Login successful');
    }

    /**
     * Get current user
     */
    public function me(Request $request)
    {
        return $this->successResponse($request->user(), 'User retrieved successfully');
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(null, 'Logged out successfully');
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $request->user()->update($validator->validated());

        return $this->successResponse($request->user(), 'Profile updated successfully');
    }

    /**
     * Update device token for push notifications
     */
    public function updateDeviceToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $request->user()->update([
            'device_token' => $request->device_token,
        ]);

        return $this->successResponse(null, 'Device token updated successfully');
    }

    /**
     * Get all employees (for admin)
     */
    public function getEmployees(Request $request)
    {
        $employees = TUser::where('role', 'employee')->get();

        return $this->successResponse($employees, 'Employees retrieved successfully');
    }
    
    public function getEmployee(Request $request)
    {
        $employees = TUser::where('id', $request->user)->get();

        return $this->successResponse($employees, 'Employee retrieved successfully');
    }

    /**
     * Admin dashboard data
     */
    public function adminDashboard(Request $request)
    {
        $admin = $request->user();

        if (!$admin->isAdmin()) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $stats = [
            'total_employees' => TUser::where('role', 'employee')->count(),
            'total_tasks' => Task::count(),
            'pending_tasks' => Task::pending()->count(),
            'completed_tasks' => Task::completed()->count(),
            'today_tasks' => Task::byDate(now()->toDateString())->count(),
        ];

        $recentTasks = Task::byAdmin($admin->id)
            ->with(['employee'])
            ->latest()
            ->take(10)
            ->get();

        $tasksByDate = Task::byAdmin($admin->id)
            ->selectRaw('DATE(due_date) as date, COUNT(*) as count')
            ->groupBy('due_date')
            ->whereBetween('due_date', [now()->startOfWeek(), now()->endOfWeek()])
            ->get();

        return $this->successResponse([
            'stats' => $stats,
            'recent_tasks' => $recentTasks,
            'tasks_by_date' => $tasksByDate,
        ], 'Admin dashboard data retrieved successfully');
    }

    /**
     * Employee dashboard data
     */
    public function employeeDashboard(Request $request)
    {
        $employee = $request->user();

        $stats = [
            'total_tasks' => Task::forEmployee($employee->id)->count(),
            'pending_tasks' => Task::forEmployee($employee->id)->pending()->count(),
            'completed_tasks' => Task::forEmployee($employee->id)->completed()->count(),
            'today_tasks' => Task::forEmployee($employee->id)->byDate(now()->toDateString())->count(),
        ];

        $myTasks = Task::forEmployee($employee->id)
            ->with(['admin'])
            ->latest()
            ->take(10)
            ->get();

        $upcomingTasks = Task::forEmployee($employee->id)
            ->where('status', '!=', 'completed')
            ->whereDate('due_date', '>=', now()->toDateString())
            ->orderBy('due_date')
            ->take(5)
            ->get();

        return $this->successResponse([
            'stats' => $stats,
            'my_tasks' => $myTasks,
            'upcoming_tasks' => $upcomingTasks,
        ], 'Employee dashboard data retrieved successfully');
    }
    
    public function profile()
    {
        $user = Auth::user();
        
        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user
            ]
        ]);
    }
    
     public function uploadAvatar(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'avatar' => 'required|string', // Base64 encoded image
        ]);
        
        $avatarData = $request->avatar;
        
        // Check if it's a valid base64 image
        if (!preg_match('/^data:image\/(jpeg|png|jpg|gif|webp);base64,/', $avatarData)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid image format. Only JPEG, PNG, GIF, and WebP are allowed.'
            ], 422);
        }
        
        // Extract base64 data
        $imageData = explode(',', $avatarData)[1];
        $imageData = base64_decode($imageData);
        
        if ($imageData === false) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid base64 data'
            ], 422);
        }
        
        // Check file size (max 2MB)
        $fileSize = strlen($imageData);
        if ($fileSize > 10 * 1024 * 1024) {
            return response()->json([
                'success' => false,
                'message' => 'Image size must be less than 10MB'
            ], 422);
        }
        
        // Validate image dimensions
        $imageInfo = getimagesizefromstring($imageData);
        if ($imageInfo === false) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid image data'
            ], 422);
        }
        
        // Generate unique filename
        $extension = 'jpg';
        switch ($imageInfo['mime']) {
            case 'image/jpeg':
                $extension = 'jpg';
                break;
            case 'image/png':
                $extension = 'png';
                break;
            case 'image/gif':
                $extension = 'gif';
                break;
            case 'image/webp':
                $extension = 'webp';
                break;
        }
        
        $filename = 'avatars/' . $user->id . '_' . time() . '.' . $extension;
        
        // Save directly to public/storage/avatars
        $storagePath = public_path('storage/' . $filename);
        $directory = dirname($storagePath);
        
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
        
        file_put_contents($storagePath, $imageData);
        
        // Delete old avatar if exists
        if ($user->avatar && file_exists(public_path('storage/' . $user->avatar))) {
            unlink(public_path('storage/' . $user->avatar));
        }
        
        // Update user avatar
        $user->avatar = $filename;
        $user->save();
        
        // Generate full URL
        $avatarUrl = asset('storage/' . $filename);
        
        return response()->json([
            'success' => true,
            'message' => 'Avatar uploaded successfully',
            'data' => [
                'avatar' => $avatarUrl,
                'avatar_path' => $filename
            ]
        ]);
    }

    /**
     * Remove user avatar
     */
    public function removeAvatar(Request $request)
    {
        $user = Auth::user();
        
        // Delete old avatar if exists
        if ($user->avatar && file_exists(public_path('storage/' . $user->avatar))) {
            unlink(public_path('storage/' . $user->avatar));
        }
        
        // Set avatar to null
        $user->avatar = null;
        $user->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Avatar removed successfully'
        ]);
    }

    /**
     * Change user password
     */
    public function changePassword(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);
        
        // Verify current password
        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect'
            ], 422);
        }
        
        // Update password
        $user->password = Hash::make($validated['password']);
        $user->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully'
        ]);
    }
    
    public function update(Request $request, $id)
    {
        $user = TUser::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255|unique:users,email,' . $id,
            'phone' => 'sometimes|nullable|string|max:20',
            'role' => 'sometimes|string|in:admin,employee',
            'department' => 'sometimes|nullable|string|max:100',
        ]);
        
        $user->update($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => [
                'user' => $user
            ]
        ]);
    }
    
    public function destroy($id)
    {
        $user = TUser::findOrFail($id);
        
        // Delete avatar if exists
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }
        
        $user->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }
}
