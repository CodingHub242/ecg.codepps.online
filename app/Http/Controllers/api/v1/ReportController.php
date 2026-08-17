<?php

namespace App\Http\Controllers\api\v1;
//namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Theft;
use App\Models\Fault;
use App\Models\ElectricityRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    //
    public function theftCase(Request $request)
    {
        
        //dd($request->all());

        $request->validate([
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePaths = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                // Store each image in the 'public/images' directory
                $path = $image->store('thefts', 'storage');
                $imagePaths[] = $path;
            }
        }

        // Save the paths as a JSON array in the database
        Theft::create([
            'fulname'=>$request->name,
            'location'=>$request->location,
            'items'=>$request->items,
            'lat'=>$request->lat,
            'lng'=>$request->lng,
            'requestID'=>$request->reqId,
            'status'=>'pending',
            'date_stolen'=>$request->tdate,
            'time_stolen'=>$request->ttime,
            'images' => json_encode($imagePaths),
        ]);
        
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
                'message' => "Receiving this alert because ".$request->name." has reported a theft at".$request->location.". Login to the admin panel to view full details",
                'recipients' => ['0556564192','0508763026'],
                // When sending SMS to Nigerian recipients, specify the use_case field
                // 'use_case' => 'transactional'
            ]),
        ]);

        $response = curl_exec($curl);
        curl_close($curl);
        //echo $response;
        // dd($response);

       //  return back()->with('success', 'Theft Successfully Reported!');

          return "<script type='text/javascript'>
        alert('Theft Successfully Reported!');
        window.history.back();
        window.location.reload();
      </script>";

//          $images = Image::find(1); // Example: Fetch the first record

// foreach ($images->image_paths as $path) {
//     echo "<img src='" . asset('storage/' . $path) . "' alt='Image'>";
// }
    }

    public function FaultCase(Request $request)
    {
       // dd($request);
        
         $request->validate([
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePaths = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                // Store each image in the 'public/images' directory
                $path = $image->store('faults', 'storage');
                $imagePaths[] = $path;
            }
        }

        // Save the paths as a JSON array in the database
        Fault::create([
            'fulname'=>$request->name,
            'location'=>$request->locations,
            'status'=>'pending',
            'lat'=>$request->lats,
            'lng'=>$request->lngs,
            'requestID'=>$request->mreqId,
            'fault'=>$request->items,
            'images' => json_encode($imagePaths),
        ]);
        
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
                'message' => "Receiving this alert because ".$request->name." has reported a fault at".$request->locations.". Login to the admin panel to view full details",
                'recipients' => ['0556564192','0508763026'],
                // When sending SMS to Nigerian recipients, specify the use_case field
                // 'use_case' => 'transactional'
            ]),
        ]);

        $response = curl_exec($curl);
        curl_close($curl);
        //echo $response;
        // dd($response);
         

        return "<script type='text/javascript'>
        alert('Fault Reported');
        window.history.back();
        window.location.reload();
      </script>";

        // return back()->with('success', 'Fault Reported!');
    }
    
    public function GetThefts(Request $request)
    {
        return $response = [
            ['id' => 1,'event'=>'Report Theft'],
            ['id'=>2,'event'=>'Report Fault']
        ];
    }

    public function Light(Request $request)
    {
      // dd($request);
        

        // Save the paths as a JSON array in the database
        ElectricityRequest::create([
            'fulname'=>$request->lname,
            'location'=>$request->llocation,
            'status'=>'pending',
            'lat'=>$request->llat,
            'lng'=>$request->llng,
            'requestID'=>$request->lreqId
        ]);
        
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
                'message' => "Receiving this alert because ".$request->lname." has placed a request for electricity at".$request->llocation.". Login to the admin panel to view full details",
                'recipients' => ['0556564192','0508763026'],
                // When sending SMS to Nigerian recipients, specify the use_case field
                // 'use_case' => 'transactional'
            ]),
        ]);

        $response = curl_exec($curl);
        curl_close($curl);
        //echo $response;
        // dd($response);
         

        return "<script type='text/javascript'>
        alert('Request Successfully Received');
        window.history.back();
        window.location.reload();
      </script>";

        // return back()->with('success', 'Fault Reported!');
    }
}
