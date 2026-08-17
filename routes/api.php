<?php


use Illuminate\Http\Request;
use App\Http\Controllers\api\v1;
use App\Http\Controllers\api\v1\ReportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\SavingsGoalController;
use App\Http\Controllers\api\SavingsEntryController;
use App\Http\Controllers\api\WithdrawalEntryController;
use App\Http\Controllers\api\ReceivedAmountController;
use App\Http\Controllers\api\ExpenseController;
//use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskReportController;


use App\Models\Theft;
use App\Models\Fault;
use App\Models\ElectricityRequest;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/name', function (Request $request) {
    return 'yes';
});


Route::post('/report_theft',[v1\ReportController::class, 'theftCase']);
Route::post('/report_fault',[v1\ReportController::class, 'FaultCase']);
Route::post('/request_light',[v1\ReportController::class, 'Light']);

Route::get('/gettheft',[v1\ReportController::class, 'GetThefts']);



/////////////////APP
Route::post('/register', [AuthController::class, 'register']);
Route::post('/loginUser', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    
    // User routes
    Route::prefix('user')->group(function () {
        Route::get('/profile', [UserController::class, 'profile']);
        Route::put('/profile', [UserController::class, 'updateProfile']);
        Route::put('/password', [UserController::class, 'updatePassword']);
        Route::get('/dashboard', [UserController::class, 'dashboard']);
        Route::get('/history', [UserController::class, 'history']);
        Route::put('/net-income', [UserController::class, 'updateNetIncome']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });

    // Savings Goals routes
     // Savings Goals routes
    Route::prefix('savings-goals')->group(function () {
        Route::get('/', [SavingsGoalController::class, 'index']);
        Route::post('/', [SavingsGoalController::class, 'store']);
        Route::get('/primary', [SavingsGoalController::class, 'getPrimary']);
        Route::get('/{goalId}', [SavingsGoalController::class, 'show']);
        Route::put('/{goalId}', [SavingsGoalController::class, 'update']);
        Route::delete('/{goalId}', [SavingsGoalController::class, 'destroy']);
        Route::put('/{goalId}/set-primary', [SavingsGoalController::class, 'setPrimary']);
    });

    // Savings Entries routes
    Route::prefix('savings-entries')->group(function () {
        Route::get('/', [SavingsEntryController::class, 'index']);
        Route::post('/', [SavingsEntryController::class, 'store']);
        Route::get('/total-savings', [SavingsEntryController::class, 'getTotalSavings']);
        Route::get('/{entryId}', [SavingsEntryController::class, 'show']);
        Route::put('/{entryId}', [SavingsEntryController::class, 'update']);
        Route::delete('/{entryId}', [SavingsEntryController::class, 'destroy']);
    });

    // Withdrawal Entries routes
    Route::prefix('withdrawal-entries')->group(function () {
        Route::get('/', [WithdrawalEntryController::class, 'index']);
        Route::post('/', [WithdrawalEntryController::class, 'store']);
        Route::get('/{entryId}', [WithdrawalEntryController::class, 'show']);
        Route::put('/{entryId}', [WithdrawalEntryController::class, 'update']);
        Route::delete('/{entryId}', [WithdrawalEntryController::class, 'destroy']);
    });

    // Alternative route structure for frontend compatibility
    Route::prefix('savings')->group(function () {
        // Goals
        Route::get('/goals', [SavingsGoalController::class, 'index']);
        Route::post('/goals', [SavingsGoalController::class, 'store']);
        Route::get('/goals/primary', [SavingsGoalController::class, 'getPrimary']);
        Route::put('/goals/{savingsGoal}', [SavingsGoalController::class, 'update']);
        Route::delete('/goals/{savingsGoal}', [SavingsGoalController::class, 'destroy']);
        Route::put('/goals/{savingsGoal}/primary', [SavingsGoalController::class, 'setPrimary']);
        
        // Entries
        Route::get('/entries', [SavingsEntryController::class, 'index']);
        Route::post('/entries', [SavingsEntryController::class, 'store']);
        Route::put('/entries/{savingsEntry}', [SavingsEntryController::class, 'update']);
        Route::delete('/entries/{savingsEntry}', [SavingsEntryController::class, 'destroy']);
        
        // Total
        Route::get('/total', [SavingsEntryController::class, 'getTotalSavings']);
    });

    Route::prefix('withdrawals')->group(function () {
        Route::get('/', [WithdrawalEntryController::class, 'index']);
        Route::post('/', [WithdrawalEntryController::class, 'store']);
        Route::put('/{withdrawalEntry}', [WithdrawalEntryController::class, 'update']);
        Route::delete('/{withdrawalEntry}', [WithdrawalEntryController::class, 'destroy']);
    });
});

// Fallback route for API
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'API endpoint not found'
    ], 404);
});


Route::post('getStatus',function(Request $request){
  
   $theft =  Theft::where('requestID',$request->stat)->get();
   $fault =  Fault::where('requestID',$request->stat)->get();
   $light =  ElectricityRequest::where('requestID',$request->stat)->get();
   
  // dd($light);
   

   if($theft)
   {
       foreach($theft as $t)
       {
            $date = new DateTime($t->date_stolen);
            $dated = $date->format("l, F j, Y");
            return response()->json(['success' => true,'msg'=>'The status of your theft report placed on '.$dated.' currently is','stat'=>$t->status], 200); 
       }
        
   }
   else
   {
       return response()->json(['success' => false], 200);
   }
   
   
   if($fault)
   {
       foreach($fault as $t)
       {
            $d = date('F j, Y, g:i a', strtotime($t->created_at));
            return response()->json(['success' => true,'msg'=>'The status of your fault report placed on '.$d.' currently is','stat'=>$t->status], 200);
       }
   }
   else
   {
       return response()->json(['success' => false], 200);
   }
   
   
   if($light)
   {
      // dd('yes');
       foreach($light as $t)
       {
           $d = date('F j, Y, g:i a', strtotime($t->created_at));
            return response()->json(['success' => true,'msg'=>'The status of your electricity request placed on '.$d.' currently is','stat'=>$t->status], 200);
       }
   }
   else
   {
       return response()->json(['success' => false], 200);
   }
  // return view('viewtheft',compact('theft'));
});


Route::post('getReports',function(Request $request){
  
    if($request->period=='General')
    {
        if($request->type=='Thefts')
        {
            //get from thefts
            $theft =  Theft::all();
            $tcount = Theft::count();
            
            return response()->json(['success' => true,'count'=>$tcount,'resp'=>$theft], 200); 
        }
        
        if($request->type=='Faults')
        {
            //get from faults
            $theft =  Fault::all();
            $tcount = Fault::count();
            
            return response()->json(['success' => true,'count'=>$tcount,'resp'=>$theft], 200); 
        }
        
        if($request->type=='Electricity Requests')
        {
            //get from electricityreq
            $theft =  ElectricityRequest::all();
            $tcount = ElectricityRequest::count();
            
            return response()->json(['success' => true,'count'=>$tcount,'resp'=>$theft], 200); 
        }
    }
    else///Between Dates
    {
        if($request->type=='Thefts')
        {
            //get from thefts
            $theft =  Theft::whereBetween('created_at', [$request->from, $request->to])->get();
            $tcount = Theft::whereBetween('created_at', [$request->from, $request->to])->count();
            
            return response()->json(['success' => true,'count'=>$tcount,'resp'=>$theft], 200); 
        }
        
        if($request->type=='Faults')
        {
            //get from thefts
            $theft =  Fault::whereBetween('created_at', [$request->from, $request->to])->get();
            $tcount = Fault::whereBetween('created_at', [$request->from, $request->to])->count();
            
            return response()->json(['success' => true,'count'=>$tcount,'resp'=>$theft], 200); 
        }
        
        
        if($request->type=='Electricity Requests')
        {
            //get from thefts
            $theft =  ElectricityRequest::whereBetween('created_at', [$request->from, $request->to])->get();
            $tcount = ElectricityRequest::whereBetween('created_at', [$request->from, $request->to])->count();
            
            return response()->json(['success' => true,'count'=>$tcount,'resp'=>$theft], 200); 
        }
        
        // $results = ModelName::whereBetween('column_name', [start_value, end_value])->get();
    }
    
    
    
    
    
    
    
    

   
});

 Route::post('auth/register', [App\Http\Controllers\AuthController::class, 'register']);
    Route::post('auth/login', [App\Http\Controllers\AuthController::class, 'login']);


    // Protected routes
Route::middleware('auth:sanctum')->group(function () {
        // Auth
        Route::post('/received-amounts/addmore/add', [ReceivedAmountController::class, 'addMore']);
      
        Route::get('/user', [App\Http\Controllers\AuthController::class, 'me']);
        Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout']);
        Route::put('/user/profile', [App\Http\Controllers\AuthController::class, 'updateProfile']);
        Route::put('/user/device-token', [App\Http\Controllers\AuthController::class, 'updateDeviceToken']);
        
        
        
         // User profile routes
        Route::get('/profile', [App\Http\Controllers\AuthController::class, 'profile']);
        Route::post('/profile', [App\Http\Controllers\AuthController::class, 'updateProfile']);
        
         Route::post('/users/{user}', [App\Http\Controllers\AuthController::class, 'updateProfile']);
        // Avatar routes
        Route::post('/users/{user}/avatar', [App\Http\Controllers\AuthController::class, 'uploadAvatar']);
        Route::delete('/users/{user}/avatar', [App\Http\Controllers\AuthController::class, 'removeAvatar']);
        // Password routes
        Route::post('/password/change', [App\Http\Controllers\AuthController::class, 'changePassword']);
        
        
    
        // Employee routes (accessible by all authenticated users)
        Route::get('/users', [App\Http\Controllers\AuthController::class, 'getEmployees']);
        Route::get('/users/{user}', [App\Http\Controllers\AuthController::class, 'getEmployee']);
    
        // Dashboard routes
        Route::get('/dashboard/admin', [App\Http\Controllers\AuthController::class, 'adminDashboard']);
        Route::get('/dashboard/employee', [App\Http\Controllers\AuthController::class, 'employeeDashboard']);
        
        // Analytics export endpoints
        Route::get('/analytics/export', [TaskController::class, 'exportAnalytics']);
        Route::get('/reports/employee/{employeeId}', [TaskController::class, 'exportEmployeeReport']);
    
        // Task routes
        Route::prefix('/tasks')->group(function () {
            Route::get('/', [TaskController::class, 'index']);
            Route::post('/', [TaskController::class, 'store']);
            Route::get('/{task}', [TaskController::class, 'show']);
            Route::put('/{task}', [TaskController::class, 'update']);
            Route::delete('/{task}', [TaskController::class, 'destroy']);
            Route::put('/{task}/status', [TaskController::class, 'updateStatus']);
            
             // Task approval endpoints
            Route::post('/{task}/approve', [TaskController::class, 'approve']);
            Route::post('/{task}/deny', [TaskController::class, 'deny']);
            Route::get('/pending-approvals', [TaskController::class, 'pendingApprovals']);
    
            Route::get('/date/{date}', [TaskController::class, 'getByDate']);
        });
    
        // Task Report routes
        Route::prefix('/task-reports')->group(function () {
            Route::get('/', [TaskReportController::class, 'index']);
            Route::post('/', [TaskReportController::class, 'store']);
            Route::get('/{report}', [TaskReportController::class, 'show']);
            Route::get('/task/{taskId}', [TaskReportController::class, 'getByTask']);
        });
        
        // Received Amounts routes
        Route::prefix('received-amounts')->group(function () {
            Route::get('/', [ReceivedAmountController::class, 'index']);
            Route::post('/', [ReceivedAmountController::class, 'store']);
           
            Route::get('/{id}', [ReceivedAmountController::class, 'show']);
            Route::put('/{id}', [ReceivedAmountController::class, 'update']);
            
            Route::delete('/{id}', [ReceivedAmountController::class, 'destroy']);
        });
        
        
    
        // Expenses routes (nested under received amounts)
        Route::prefix('received-amounts/{receivedAmountId}/expenses')->group(function () {
            Route::get('/', [ExpenseController::class, 'index']);
            Route::post('/', [ExpenseController::class, 'store']);
            Route::get('/{id}', [ExpenseController::class, 'show']);
            Route::put('/{id}', [ExpenseController::class, 'update']);
            Route::delete('/{id}', [ExpenseController::class, 'destroy']);
        });
    });