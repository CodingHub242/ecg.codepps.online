<?php
namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EmployeeController extends Controller
{
    /**
     * Display a listing of all employees.
     * GET /api/employees
     *
     * Replaces Firebase: getEmployees()
     */
    public function index(): JsonResponse
    {
        try {
            $employees = Employee::all();

            return response()->json([
                'success' => true,
                'data' => $employees,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching employees: ' . $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    /**
     * Store a newly created employee.
     * POST /api/employees
     *
     * Replaces Firebase: addEmployee()
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:employees,code',
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $employee = Employee::create($validated);

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
                'sender' => 'AlertNote',
                'message' => "This is your personal employee code to checkin always ".$request->code,
                'recipients' => [$request->email],
                // When sending SMS to Nigerian recipients, specify the use_case field
                // 'use_case' => 'transactional'
            ]),
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        return response()->json([
            'success' => true,
            'message' => 'Employee created successfully',
            'data' => $employee,
        ], 201);
    }

    /**
     * Display the specified employee.
     * GET /api/employees/{id}
     */
    public function show(string $id): JsonResponse
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $employee,
        ]);
    }

    /**
     * Update the specified employee.
     * PUT /api/employees/{id}
     *
     * Replaces Firebase: updateEmployee()
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found',
            ], 404);
        }

        $validated = $request->validate([
            'code' => 'sometimes|string|max:255|unique:employees,code,' . $id,
            'name' => 'sometimes|string|max:255',
            'email' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $employee->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Employee updated successfully',
            'data' => $employee,
        ]);
    }

    /**
     * Remove the specified employee.
     * DELETE /api/employees/{id}
     *
     * Replaces Firebase: deleteEmployee()
     */
    public function destroy(string $id): JsonResponse
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found',
            ], 404);
        }

        \DB::table('deleted_employees')->insert([
            'employee_id' => $employee->code,
        ]);
        
        $employee->delete();

        return response()->json([
            'success' => true,
            'message' => 'Employee deleted successfully',
        ]);
    }

    /**
     * Get employee by code (used for check-in lookup).
     * GET /api/employees/code/{code}
     */
    public function getByCode(string $code): JsonResponse
    {
        $employee = Employee::where('code', $code)
            ->where('is_active', true)
            ->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found or inactive',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $employee,
        ]);
    }
}
