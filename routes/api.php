<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\SettingsController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SyncController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| These routes provide a REST API replacement for the Firebase Firestore
| backend. Each route maps to a method in the corresponding controller.
|
| Base URL: https://attendance.myartsonline.com/api
|
*/

// ---------------------------------------------------------------------------
// Authentication
// ---------------------------------------------------------------------------

// Admin login - validates password against settings
// Replaces: admin-login.page.ts password check against localStorage
Route::post('/login', [AuthController::class, 'login']);

// Get current admin/settings info
Route::get('/me', [AuthController::class, 'me']);

// ---------------------------------------------------------------------------
// Settings
// ---------------------------------------------------------------------------

// Get the main settings document
// Replaces Firebase: getSettings() -> doc(firestore, 'settings', 'main')
Route::get('/settings', [SettingsController::class, 'index']);

// Update the main settings document (merge)
// Replaces Firebase: updateSettings() -> setDoc(docRef, data, { merge: true })
Route::put('/settings', [SettingsController::class, 'update']);

// ---------------------------------------------------------------------------
// Employees
// ---------------------------------------------------------------------------

// List all employees
// Replaces Firebase: getEmployees() -> getDocs(collection(fs, 'employees'))
Route::get('/employees', [EmployeeController::class, 'index']);

// Create a new employee
// Replaces Firebase: addEmployee() -> addDoc(collection(fs, 'employees'), data)
Route::post('/employees', [EmployeeController::class, 'store']);

// Get a single employee by ID
Route::get('/employees/{id}', [EmployeeController::class, 'show']);

// Update an employee by ID
// Replaces Firebase: updateEmployee() -> updateDoc(docRef, data)
Route::put('/employees/{id}', [EmployeeController::class, 'update']);

// Delete an employee by ID
// Replaces Firebase: deleteEmployee() -> deleteDoc(docRef)
Route::delete('/employees/{id}', [EmployeeController::class, 'destroy']);

// Get employee by code (for check-in lookup)
// Replaces: storageService.getEmployeeByCode()
Route::get('/employees/code/{code}', [EmployeeController::class, 'getByCode']);

// ---------------------------------------------------------------------------
// Attendance
// ---------------------------------------------------------------------------

// List all attendance records
Route::get('/attendance', [AttendanceController::class, 'index']);

// Get attendance by date
// Replaces Firebase: getAttendanceByDate() -> query where('date', '==', date) orderBy('checkInTime', 'desc')
Route::get('/attendance/date', [AttendanceController::class, 'getByDate']);

// Get attendance by employee within a date range
// Replaces Firebase: getAttendanceByEmployee() -> query where('employeeId', '==') where('date', '>=') where('date', '<=') orderBy('date', 'desc')
Route::get('/attendance/employee/{employeeId}', [AttendanceController::class, 'getByEmployee']);

// Create a new attendance record
// Replaces Firebase: addAttendance() -> addDoc(collection(fs, 'attendance'), data)
Route::post('/attendance', [AttendanceController::class, 'store']);

// Update an attendance record by ID
// Replaces Firebase: updateAttendance() -> updateDoc(docRef, data)
Route::put('/attendance/{id}', [AttendanceController::class, 'update']);

// Delete an attendance record by ID
Route::delete('/attendance/{id}', [AttendanceController::class, 'destroy']);

// Get today's attendance summary (for admin dashboard stats)
Route::get('/attendance/today', [AttendanceController::class, 'today']);

// ---------------------------------------------------------------------------
// Sync (Multi-device synchronization)
// ---------------------------------------------------------------------------

// Get all deleted employee IDs
// Replaces Firebase: getDeletedEmployeeIds() -> getDocs(collection(fs, 'deleted_employees'))
Route::get('/sync/deleted-employees', [SyncController::class, 'getDeletedEmployees']);

// Record a deleted employee ID
// Replaces Firebase: addDeletedEmployeeId() -> addDoc(collection(fs, 'deleted_employees'), data)
Route::post('/sync/deleted-employees', [SyncController::class, 'addDeletedEmployee']);

// Bulk sync endpoint - handles full sync cycle
// Replaces: SyncService.syncAll() which calls multiple Firebase operations
Route::post('/sync', [SyncController::class, 'sync']);
