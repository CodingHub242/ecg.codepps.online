<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\DeletedEmployee;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SyncController extends Controller
{
    /**
     * Get all deleted employee IDs (for multi-device sync).
     * GET /api/sync/deleted-employees
     *
     * Replaces Firebase: getDeletedEmployeeIds()
     */
    public function getDeletedEmployees(): JsonResponse
    {
        try {
            $deletedIds = DeletedEmployee::pluck('employee_id')->toArray();

            return response()->json([
                'success' => true,
                'data' => $deletedIds,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching deleted employees: ' . $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    /**
     * Record a deleted employee ID (for multi-device sync).
     * POST /api/sync/deleted-employees
     *
     * Replaces Firebase: addDeletedEmployeeId()
     */
    public function addDeletedEmployee(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|string',
        ]);

        DeletedEmployee::create([
            'employee_id' => $validated['employee_id'],
            'deleted_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Deleted employee ID recorded',
        ]);
    }

    /**
     * Bulk sync endpoint - receives local changes and returns remote changes.
     * POST /api/sync
     *
     * This endpoint handles the full sync cycle:
     * 1. Receives local employees to add/update
     * 2. Receives local attendance records to add/update
     * 3. Receives local deleted employee IDs
     * 4. Returns all remote employees, attendance, settings, and deleted IDs
     */
    public function sync(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'employees' => 'array',
            'employees.*.code' => 'required|string',
            'employees.*.name' => 'required|string',
            'employees.*.email' => 'nullable|string',
            'employees.*.department' => 'nullable|string',
            'employees.*.position' => 'nullable|string',
            'employees.*.is_active' => 'boolean',
            'attendance' => 'array',
            'attendance.*.employee_id' => 'required|exists:employees,id',
            'attendance.*.employee_code' => 'nullable|string',
            'attendance.*.employee_name' => 'nullable|string',
            'attendance.*.date' => 'required|date',
            'attendance.*.check_in_time' => 'nullable|date',
            'attendance.*.check_out_time' => 'nullable|date',
            'attendance.*.status' => 'in:present,absent,late,early',
            'attendance.*.synced' => 'boolean',
            'deleted_employee_ids' => 'array',
            'deleted_employee_ids.*' => 'string',
        ]);

        DB::beginTransaction();

        try {
            // 1. Process local employees (upsert)
            $localEmployees = $validated['employees'] ?? [];
            foreach ($localEmployees as $emp) {
                Employee::updateOrCreate(
                    ['code' => $emp['code']],
                    [
                        'name' => $emp['name'],
                        'email' => $emp['email'] ?? null,
                        'department' => $emp['department'] ?? null,
                        'position' => $emp['position'] ?? null,
                        'is_active' => $emp['is_active'] ?? true,
                    ]
                );
            }

            // 2. Process local attendance records (upsert by employee_id + date)
            $localAttendance = $validated['attendance'] ?? [];
            foreach ($localAttendance as $att) {
                Attendance::updateOrCreate(
                    [
                        'employee_id' => $att['employee_id'],
                        'date' => $att['date'],
                    ],
                    [
                        'employee_code' => $att['employee_code'] ?? null,
                        'employee_name' => $att['employee_name'] ?? null,
                        'check_in_time' => $att['check_in_time'] ?? null,
                        'check_out_time' => $att['check_out_time'] ?? null,
                        'status' => $att['status'] ?? 'present',
                        'synced' => true,
                    ]
                );
            }

            // 3. Process deleted employee IDs
            $deletedIds = $validated['deleted_employee_ids'] ?? [];
            foreach ($deletedIds as $empId) {
                // Delete the employee if it exists
                Employee::where('id', $empId)->delete();

                // Record in deleted_employees table (avoid duplicates)
                DeletedEmployee::firstOrCreate(
                    ['employee_id' => $empId],
                    ['deleted_at' => now()]
                );
            }

            // 4. Return all remote data
            $remoteEmployees = Employee::all();
            $remoteAttendance = Attendance::all();
            $remoteDeletedIds = DeletedEmployee::pluck('employee_id')->toArray();
            $remoteSettings = \App\Models\Settings::getMain();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sync completed successfully',
                'data' => [
                    'employees' => $remoteEmployees,
                    'attendance' => $remoteAttendance,
                    'deleted_employee_ids' => $remoteDeletedIds,
                    'settings' => $remoteSettings,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Sync failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
