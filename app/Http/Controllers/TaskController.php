<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TUser;
use App\Services\ArkeselSmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    protected $smsService;

    public function __construct(ArkeselSmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Get all tasks (filtered by role)
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Task::with(['admin', 'employee', 'reports']);

        if ($user->isEmployee()) {
            $query->forEmployee($user->id);
        } else {
            $query->byAdmin($user->id);
        }

        // Filter by status if provided
        if ($request->has('status')) {
            $query->byStatus($request->status);
        }

        // Filter by date if provided
        if ($request->has('date')) {
            $query->byDate($request->date);
        }

        // Filter by employee if admin and employee_id provided
        if ($user->isAdmin() && $request->has('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        $tasks = $query->orderBy('due_date', 'asc')->get();//->paginate(20);

        return $this->successResponse($tasks, 'Tasks retrieved successfully');
    }

    /**
     * Get tasks by specific date
     */
    public function getByDate(Request $request, $date)
    {
        $user = $request->user();
        $query = Task::with(['admin', 'employee', 'reports'])
            ->byDate($date);

        if ($user->isEmployee()) {
            $query->forEmployee($user->id);
        } else {
            $query->byAdmin($user->id);
        }

        $tasks = $query->orderBy('due_date', 'asc')->get();

        return $this->successResponse($tasks, 'Tasks retrieved successfully');
    }

    /**
     * Create a new task
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:tusers,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'dueDate' => 'required|date|after_or_equal:today',
            'priority' => 'nullable|in:low,medium,high',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $user = $request->user();

        if (!$user->isAdmin()) {
            return $this->errorResponse('Only admins can create tasks', 403);
        }

        // Verify employee exists
        $employee = TUser::where('id', $request->employee_id)
            ->where('role', 'employee')
            ->first();

        if (!$employee) {
            return $this->errorResponse('Employee not found', 404);
        }

        $task = Task::create([
            'admin_id' => $user->id,
            'employee_id' => $request->employee_id,
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->dueDate,
            'priority' => $request->priority ?? 'medium',
            'status' => Task::STATUS_PENDING,
        ]);

        $task->load(['admin', 'employee']);

        // Send SMS notification to employee
        if ($employee->phone) {
            $message = "New task assigned to you: {$task->title}. Due date: {$task->due_date->format('Y-m-d')}";
            
             //sms alert
            $curl = curl_init();
    
            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://sms.arkesel.com/api/v2/sms/send',
                CURLOPT_HTTPHEADER => ['api-key: ZFRDQVFUVlZyQ0t1c3NsRllNc1U'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => http_build_query([
                    'sender' => 'TASKMGR',
                    'message' => $message,
                    'recipients' => [$employee->phone],
                ]),
            ]);
    
            $response = curl_exec($curl);
            curl_close($curl);
            
           // $this->smsService->sendSms($employee->phone, $message);
            $task->update(['sms_sent' => true]);
        }

        return $this->successResponse($task, 'Task created successfully', 201);
    }

    /**
     * Show a single task
     */
    public function show(Request $request, Task $task)
    {
        $user = $request->user();

        // Check authorization
        if ($user->isEmployee() && $task->employee_id !== $user->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $task->load(['admin', 'employee', 'reports']);

        return $this->successResponse($task, 'Task retrieved successfully');
    }

    /**
     * Update a task (admin only)
     */
    public function update(Request $request, Task $task)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'sometimes|exists:users,id',
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'sometimes|date',
            'priority' => 'nullable|in:low,medium,high',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $user = $request->user();

        if (!$user->isAdmin()) {
            return $this->errorResponse('Only admins can update tasks', 403);
        }

        // if ($task->admin_id !== $user->id) {
        //     return $this->errorResponse('Unauthorized', 403);
        // }

        $task->update($validator->validated());
        
        // $oldStatus = $task->status;
        // $task->update(['status' => $request->status]);

        // // If completed, send notification to admin
        // if ($request->status === Task::STATUS_COMPLETED && $oldStatus !== Task::STATUS_COMPLETED) {
        //     $admin = $task->admin;
        //     if ($admin->phone) {
        //         $message = "Task '{$task->title}' has been completed by {$user->name}";
                
        //         //sms alert
        //         $curl = curl_init();
        
        //         curl_setopt_array($curl, [
        //             CURLOPT_URL => 'https://sms.arkesel.com/api/v2/sms/send',
        //             CURLOPT_HTTPHEADER => ['api-key: ZFRDQVFUVlZyQ0t1c3NsRllNc1U'],
        //             CURLOPT_RETURNTRANSFER => true,
        //             CURLOPT_ENCODING => '',
        //             CURLOPT_MAXREDIRS => 10,
        //             CURLOPT_TIMEOUT => 0,
        //             CURLOPT_FOLLOWLOCATION => true,
        //             CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //             CURLOPT_CUSTOMREQUEST => 'POST',
        //             CURLOPT_POSTFIELDS => http_build_query([
        //                 'sender' => 'TASKMGR',
        //                 'message' => $message,
        //                 'recipients' => ['0203568566','0538377940'],
        //             ]),
        //         ]);
        
        //         $response = curl_exec($curl);
        //         curl_close($curl);
                
        //         //$this->smsService->sendSms($admin->phone, $message);
        //     }
        // }

        $task->load(['admin', 'employee','reports']);

        return $this->successResponse($task, 'Task updated successfully');
    }

    /**
     * Delete a task (admin only)
     */
    public function destroy(Request $request, Task $task)
    {
        $user = $request->user();

        if (!$user->isAdmin()) {
            return $this->errorResponse('Only admins can delete tasks', 403);
        }

        if ($task->admin_id !== $user->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $task->delete();

        return $this->successResponse(null, 'Task deleted successfully');
    }

    /**
     * Update task status (employee can update their own tasks)
     */
    public function updateStatus(Request $request, Task $task)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,in_progress,completed,cancelled',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $user = $request->user();
        
       // return $user->id;

        // Both admin and employee can update status
        if ($user->isEmployee() && ($task->employee_id != $user->id)) {
            return $this->errorResponse('You can only update your own tasks', 403);
        }

        $oldStatus = $task->status;
        $task->update(['status' => $request->status]);

        // If completed, send notification to admin
        if ($request->status === Task::STATUS_COMPLETED && $oldStatus !== Task::STATUS_COMPLETED) {
            $admin = $task->admin;
            if ($admin->phone) {
                $message = "Task '{$task->title}' has been completed by {$user->name}";
                
                //sms alert
                $curl = curl_init();
        
                curl_setopt_array($curl, [
                    CURLOPT_URL => 'https://sms.arkesel.com/api/v2/sms/send',
                    CURLOPT_HTTPHEADER => ['api-key: ZFRDQVFUVlZyQ0t1c3NsRllNc1U'],
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => http_build_query([
                        'sender' => 'TASKMGR',
                        'message' => $message,
                        'recipients' => ['0277977222','0244841721'],
                    ]),
                ]);
        
                $response = curl_exec($curl);
                curl_close($curl);
                
                //$this->smsService->sendSms($admin->phone, $message);
            }
        }

        return $this->successResponse($task, 'Task status updated successfully');
    }
    
      /**
     * Approve task completion (Admin only)
     */
    public function approve(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        
        // Check if admin
        if (Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.'
            ], 403);
        }
        
        // Update task status
        $task->update([
            'pending_approval' => false,
            'approved_by_admin' => true,
            'completed' => true
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Task approved successfully',
            'data' => $task
        ]);
    }
    
    /**
     * Deny task completion (Admin only)
     */
    public function deny(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        
        // Check if admin
        if (Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.'
            ], 403);
        }
        
        // Get denial reason if provided
        $reason = $request->input('reason');
        
        // Update task status - reset to pending
        $task->update([
            'pending_approval' => false,
            'approved_by_admin' => false,
            'completed' => false,
            'denial_reason' => $reason
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Task denied and sent back to employee',
            'data' => $task
        ]);
    }
    
    /**
     * Get tasks pending approval (Admin only)
     */
    public function pendingApprovals(Request $request)
    {
        // Check if admin
        if (Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.'
            ], 403);
        }
        
        $tasks = Task::where('pending_approval', true)
            ->where('approved_by_admin', false)
            ->with('employee')
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $tasks
        ]);
    }
    
    /**
     * Export analytics (Admin only)
     */
    public function exportAnalytics(Request $request)
    {
        // Check if admin
        if (Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.'
            ], 403);
        }
        
        $period = $request->query('period', 'weekly');
        $tasks = Task::with('employee')->get();
        
        // Build CSV content
        $csv = "Task ID,Title,Description,Priority,Assigned To,Due Date,Completed,Pending Approval,Approved By Admin,Created At\n";
        
        foreach ($tasks as $task) {
            $csv .= sprintf(
                "%s,%s,%s,%s,%s,%s,%s,%s,%s,%s\n",
                $task->id,
                '"' . str_replace('"', '""', $task->title) . '"',
                '"' . str_replace('"', '""', $task->description) . '"',
                $task->priority,
                $task->employee->name ?? 'Unassigned',
                $task->due_date,
                $task->completed ? 'Yes' : 'No',
                $task->pending_approval ? 'Yes' : 'No',
                $task->approved_by_admin ? 'Yes' : 'No',
                $task->created_at
            );
        }
        
        $filename = 'analytics_' . $period . '_' . date('Y-m-d') . '.csv';
        
        return response()->stream(function () use ($csv) {
            echo $csv;
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
    
    /**
     * Export employee report (Admin only)
     */
    public function exportEmployeeReport(Request $request, $employeeId)
    {
        // Check if admin
        if (Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.'
            ], 403);
        }
        
        $period = $request->query('period', 'weekly');
        $employee = \App\Models\TUser::findOrFail($employeeId);
        
        $tasks = Task::where('employee_id', $employeeId)->get();
        
        // Build CSV content
        $csv = "Task ID,Title,Description,Priority,Due Date,Completed,Completed At\n";
        
        foreach ($tasks as $task) {
            $csv .= sprintf(
                "%s,%s,%s,%s,%s,%s,%s\n",
                $task->id,
                '"' . str_replace('"', '""', $task->title) . '"',
                '"' . str_replace('"', '""', $task->description) . '"',
                $task->priority,
                $task->due_date,
                $task->completed ? 'Yes' : 'No',
                $task->completed_at ?? 'N/A'
            );
        }
        
        $filename = 'report_' . str_replace(' ', '_', $employee->name) . '_' . $period . '_' . date('Y-m-d') . '.csv';
        
        return response()->stream(function () use ($csv) {
            echo $csv;
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
