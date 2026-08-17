<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TaskReportController extends Controller
{
    /**
     * Get all reports (filtered by role)
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = TaskReport::with(['task', 'employee']);

        if ($user->isEmployee()) {
            $query->byEmployee($user->id);
        }

        $reports = $query->latest()->paginate(20);

        return $this->successResponse($reports, 'Reports retrieved successfully');
    }

    /**
     * Create a new task report with image
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'task_id' => 'required|exists:tasks,id',
            'report_content' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // Max 5MB
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $user = $request->user();

        // Verify task belongs to employee
        $task = Task::findOrFail($request->task_id);

        if ($user->isEmployee() && $task->employee_id !== $user->id) {
            return $this->errorResponse('You can only report on your own tasks', 403);
        }

        $reportData = [
            'task_id' => $request->task_id,
            'employee_id' => $user->id,
            'report_content' => $request->report_content,
            'status' => 'pending',
        ];

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = 'report_' . $task->id . '_' . time() . '.' . $image->getClientOriginalExtension();
            
            // Store in public disk under task-reports folder
            $path = $image->storeAs('task-reports', $filename, 'public');
            $reportData['image_path'] = $path;
        }

        $report = TaskReport::create($reportData);

        // Update task status if report is submitted
        if ($task->status === Task::STATUS_PENDING) {
            $task->update(['status' => Task::STATUS_IN_PROGRESS]);
        }

        $report->load(['task', 'employee']);

        return $this->successResponse($report, 'Report submitted successfully', 201);
    }

    /**
     * Show a single report
     */
    public function show(Request $request, TaskReport $report)
    {
        $user = $request->user();

        // Check authorization
        if ($user->isEmployee() && $report->employee_id !== $user->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $report->load(['task', 'employee']);

        return $this->successResponse($report, 'Report retrieved successfully');
    }

    /**
     * Get reports by task
     */
    public function getByTask(Request $request, $taskId)
    {
        $user = $request->user();
        $task = Task::findOrFail($taskId);

        // Check authorization
        if ($user->isEmployee() && $task->employee_id !== $user->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $reports = TaskReport::where('task_id', $taskId)
            ->with(['employee'])
            ->latest()
            ->get();

        return $this->successResponse($reports, 'Reports retrieved successfully');
    }

    /**
     * Delete a report
     */
    public function destroy(Request $request, TaskReport $report)
    {
        $user = $request->user();

        if ($report->employee_id !== $user->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        // Delete image file if exists
        if ($report->image_path && Storage::disk('public')->exists($report->image_path)) {
            Storage::disk('public')->delete($report->image_path);
        }

        $report->delete();

        return $this->successResponse(null, 'Report deleted successfully');
    }
}
