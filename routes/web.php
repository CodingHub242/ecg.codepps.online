<?php

use Illuminate\Support\Facades\Route;
use App\Models\Theft;
use App\Models\Fault;
use App\Models\ElectricityRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/check',function(){
try {
        Artisan::call('storage:link');
        return response()->json(['message' => 'Storage link created successfully!'], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to create storage link: ' . $e->getMessage()], 500);
    }
});

Route::get('viewtheft',function(Request $request){
   $theft =  Theft::where('id',$request->tID)->get();
   
   return view('viewtheft',compact('theft'));
})->name('view.theft');

Route::get('viewfault',function(Request $request){
   $theft =  Fault::where('id',$request->tID)->get();
   
   return view('viewfault',compact('theft'));
})->name('view.fault');

Route::get('viewrequest',function(Request $request){
   $light =  ElectricityRequest::where('id',$request->tID)->get();
   
   return view('viewrequest',compact('light'));
})->name('view.light');


Route::post('updatetheft',function(Request $request){
   $theft =  Theft::where('id',$request->tID)->get();

   if($theft)
   {
       //update
       DB::table('thefts')
        ->where('id',$request->tID)
        ->update([
            'status' => $request->stat,
        ]);
        
        return response()->json(['success' => true], 200);
   }
   else
   {
       return response()->json(['success' => false], 200);
   }
  // return view('viewtheft',compact('theft'));
});
Route::post('updatefault',function(Request $request){
   $theft =  Fault::where('id',$request->tID)->get();

   if($theft)
   {
       //update
       DB::table('faults')
        ->where('id',$request->tID)
        ->update([
            'status' => $request->stat,
        ]);
        
        return response()->json(['success' => true], 200);
   }
   else
   {
       return response()->json(['success' => false], 200);
   }
  // return view('viewtheft',compact('theft'));
});


Route::post('updatelight',function(Request $request){
   $theft =  ElectricityRequest::where('id',$request->tID)->get();

   if($theft)
   {
       //update
       DB::table('electricity_requests')
        ->where('id',$request->tID)
        ->update([
            'status' => $request->stat,
        ]);
        
        return response()->json(['success' => true], 200);
   }
   else
   {
       return response()->json(['success' => false], 200);
   }
  // return view('viewtheft',compact('theft'));
});

Route::get('/reports',function(){
    return view('reports'); 
});
// Route::post('getStatus',function(Request $request){
//    $theft =  Theft::where('requestID',$request->stat)->get();
//    $fault =  Fault::where('requestID',$request->stat)->get();
//    $light =  ElectricityRequest::where('requestID',$request->stat)->get();

//    if($theft)
//    {
//         return response()->json(['success' => true,'msg'=>'The status of your theft report currently is','stat'=>$theft->status], 200);
//    }
//    else if($fault)
//    {
//         return response()->json(['success' => true,'msg'=>'The status of your fault report currently is','stat'=>$fault->status], 200);
//    }
//    else if($light)
//    {
//         return response()->json(['success' => true,'msg'=>'The status of your electricity request currently is','stat'=>$light->status], 200);
//    }
//    else
//    {
//        return response()->json(['success' => false], 200);
//    }
//   // return view('viewtheft',compact('theft'));
// });