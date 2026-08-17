<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AttendanceController extends Controller
{
    /**
     * Display a listing of all attendance records.
     * GET /api/attendance
     */
    public function index(): JsonResponse
    {
        $attendance = Attendance::with('employee')->get();

        return response()->json([
            'success' => true,
            'data' => $attendance,
        ]);
    }

    /**
     * Get attendance records by date.
     * GET /api/attendance/date?date=YYYY-MM-DD
     *
     * Replaces Firebase: getAttendanceByDate()
     */
    public function getByDate(Request $request): JsonResponse
    {
        $date = $request->query('date');

        if (!$date) {
            return response()->json([
                'success' => false,
                'message' => 'Date parameter is required',
            ], 400);
        }

        $attendance = Attendance::where('date', $date)
            ->orderBy('check_in_time', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $attendance,
        ]);
    }

    /**
     * Get attendance records by employee within a date range.
     * GET /api/attendance/employee/{employeeId}?start=YYYY-MM-DD&end=YYYY-MM-DD
     *
     * Replaces Firebase: getAttendanceByEmployee()
     */
    public function getByEmployee(string $employeeId, Request $request): JsonResponse
    {
        $startDate = $request->query('start');
        $endDate = $request->query('end');

        if (!$startDate || !$endDate) {
            return response()->json([
                'success' => false,
                'message' => 'Start and end date parameters are required',
            ], 400);
        }

        $attendance = Attendance::where('employee_id', $employeeId)
            ->where('date', '>=', $startDate)
            ->where('date', '<=', $endDate)
            ->orderBy('date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $attendance,
        ]);
    }

    /**
     * Store a new attendance record.
     * POST /api/attendance
     *
     * Replaces Firebase: addAttendance()
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'employee_code' => 'nullable|string|max:255',
            'employee_name' => 'nullable|string|max:255',
            'date' => 'required|date',
            'check_in_time' => 'nullable|date',
            'check_out_time' => 'nullable|date',
            'status' => 'in:present,absent,late,early',
            'synced' => 'boolean',
        ]);

        $attendance = Attendance::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Attendance record created successfully',
            'data' => $attendance,
        ], 201);
    }

    /**
     * Update an existing attendance record.
     * PUT /api/attendance/{id}
     *
     * Replaces Firebase: updateAttendance()
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $attendance = Attendance::find($id);

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance record not found',
            ], 404);
        }

        $validated = $request->validate([
            'employee_id' => 'sometimes|exists:employees,id',
            'employee_code' => 'nullable|string|max:255',
            'employee_name' => 'nullable|string|max:255',
            'date' => 'sometimes|date',
            'check_in_time' => 'nullable|date',
            'check_out_time' => 'nullable|date',
            'status' => 'in:present,absent,late,early',
            'synced' => 'boolean',
        ]);

        $attendance->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Attendance record updated successfully',
            'data' => $attendance,
        ]);
    }

    /**
     * Remove the specified attendance record.
     * DELETE /api/attendance/{id}
     */
    public function destroy(string $id): JsonResponse
    {
        $attendance = Attendance::find($id);

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance record not found',
            ], 404);
        }

        $attendance->delete();

        return response()->json([
            'success' => true,
            'message' => 'Attendance record deleted successfully',
        ]);
    }

    /**
     * Get today's attendance summary for admin dashboard.
     * GET /api/attendance/today
     */
    public function today(): JsonResponse
    {
        $today = now()->toDateString();

        $totalCheckIns = Attendance::where('date', $today)->count();
        $activeToday = Attendance::where('date', $today)
            ->whereNull('check_out_time')
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'date' => $today,
                'total_check_ins' => $totalCheckIns,
                'active_today' => $activeToday,
            ],
        ]);
    }
}
