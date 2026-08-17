{{-- @php 
use App\Models\User;

foreach($theft as $t)
{
   
}

@endphp --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>ECG</title>
  
    <!-- Bootstrap CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
   
    <div class="mx-auto mt-10 ">

        <div class="bg-white p-6 rounded shadow-lg  lg:!w-[50%]  mx-auto">
            <div class=" mb-4">
                
            </div>
            <h2 class="text-4xl font-bold mb-4">Generate Data Reports</h2>
            <form id="SubForm">
                <!--@csrf-->
                <div class="mb-4" >
                    <label for="custemail" class="block text-gray-700 font-bold mb-2">Select Request Type  </label>
                    <select id="request" name="request" class="w-full border-2 rounded p-3" style="color:#000;border:1px solid #000;">
                        <option selected value="Select Request">Select Request</option>
                        <option value="Thefts">Thefts</option>
                        <option value="Faults">Fault</option>
                        <option value="Electricity Requests">Electricity Requests</option>
                    </select>
                </div>

                <div class="mb-4" >
                    <label for="custphone" class="block text-gray-700 font-bold mb-2">Period/Mode  </label>
                    <select id="period" name="period" onchange="GetPeriod()" class="w-full border-2 rounded p-3" style="color:#000;border:1px solid #000;">
                        <option selected value="General">General</option>
                        <option value="Between">Between Dates</option>
                    </select>
                </div>
                
                <div class="mb-4" id="betwen">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="custphone" class="block text-gray-700 font-bold mb-2">From</label>
                            <input type="date" id="from" name="from" class="w-full border-2 rounded p-3" style="color:#000;border:1px solid #000;"/>
                        </div>
                        
                        <div class="col-md-6 mt-3">
                            <label for="custphone" class="block text-gray-700 font-bold mb-2">To</label>
                            
                            <input type="date" id="to" name="to" class="w-full   border-2 rounded p-3" style="color:#000;border:1px solid #000;"/>
                        </div>
                    </div>
                    
                    
                </div>
                
               <div class="mb-4" >
                    <button onclick="GetData()" id="sbut" type="button" style="margin-top:40px;background:#41464b;color:#fff;padding:10px;width:auto;float:right;border-radius:5px;">GET DATA</button>
                </div>
                
               
               
                
                
                

               {{-- <input type="text" value="{{$t->lat}}" hidden id="lat"/>
               <input type="text" value="{{$t->lng}}" hidden id="lng"/>
                <input type="text" value="{{$t->id}}" hidden id="tID"/>
                <input name="csrfToken" id="csrf" value="{{ csrf_token() }}" type="hidden"/>     --}}
                
                 
               
            </form>
            
            <div id="TheftTab">
                 <p id="tcount" style="text-align:center;"></p>
                   <table style="border-collapse: collapse; width: 100%; text-align: left;">
                   
                    <thead>
                        <tr style="background-color: #f2f2f2;">
                            <th>Name</th>
                            <th>Location</th>
                            <th>Date & Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="tbody">
                        <!-- Data will be appended here -->
                    </tbody>
                </table>
            </div>
            
            <div id="FaultTab">
                <p id="fcount" style="text-align:center;"></p>
                 <table style="border-collapse: collapse; width: 100%; text-align: left;" >
                
                <thead>
                    <tr style="background-color: #f2f2f2;">
                        <th>Name</th>
                        <th>Location</th>
                        <th>Date & Time</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="fbody">
                    <!-- Data will be appended here -->
                </tbody>
            </table>
            </div>
          
            
           
            <div id="LightTab">
                <p id="lcount" style="text-align:center;"></p>
                <table style="border-collapse: collapse; width: 100%; text-align: left;">
                    
                    <thead>
                        <tr style="background-color: #f2f2f2;">
                            <th>Name</th>
                            <th>Location</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="lbody">
                        <!-- Data will be appended here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
   

    <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
      
       <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
        
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
         <script type='text/javascript' src='https://html2canvas.hertzen.com/dist/html2canvas.js'></script>
        <script type='text/javascript' src= "https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script> 
       <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
         
         
         
         <script>
             $('#betwen').hide();
             $('#TheftTab').hide();
             $('#FaultTab').hide();
             $('#LightTab').hide();
             
                 
               function GetPeriod()
               {
                   const selected = $('#period').val();
                   
                   if(selected=='Between')
                   {
                       $('#betwen').show(500);
                   }
                   else
                   {
                       $('#betwen').hide(500);
                   }
               }
               
               function GetData()
               {
                   $('#sbut').text('Processing...');
                   const type = $('#request').val();
                   const period = $('#period').val();
                   const fdated = $('#from').val();
                   const tdated = $('#to').val();
                   
                   if(period=='General')
                   {
                       if(type=='Select Request')
                       {
                           alert('Request Type Not Selected');
                           
                            $('#sbut').text('GET DATA');
                       }
                       else
                       {
                           //ajax
                            $.ajax({
                                 headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                    'Access-Control-Allow-Origin' : '*'
                                    },
                                type: "POST",
                               // crossDomain: true,
                                url: "https://ecg.codepps.online/api/getReports",
                                data: {type:type,period:period,from:fdated,to:tdated},
                                dataType:'json',
                                success: function (response) {
                                    
                                    //console.log(response);
                                    
                                    if(type=='Thefts')
                                    {
                                        $('#tcount').text('Total Theft Cases Recorded : ' + response.count);
                                        
                                         document.querySelector("#tbody").innerHTML = "";
                                        
                                        
                                        response.resp.forEach(user => {
                                            const row = `
                                                <tr style="background-color: #f2f2f2;">
                                                    <td>${user.fulname}</td>
                                                    <td>${user.location}</td>
                                                    <td>${user.created_at}</td>
                                                    <td>${user.status}</td>
                                                </tr>
                                            `;
                                            $('#tbody').append(row);
                                            
                                            $('#sbut').text('GET DATA');
                                            
                                            
                                        });
                                        
                                         var printContents = document.getElementById('TheftTab').innerHTML;
                                  var originalContents = document.body.innerHTML;
                                
                                  document.body.innerHTML = printContents;
                                  window.print();
                                  document.body.innerHTML = originalContents;
                                    }
                                    
                                    if(type=='Faults')
                                    {
                                         $('#fcount').text('Total Fault Cases Recorded : ' + response.count);
                                         
                                          document.querySelector("#fbody").innerHTML = "";
                                          
                                          
                                        response.resp.forEach(user => {
                                            const row = `
                                                <tr style="background-color: #f2f2f2;">
                                                    <td>${user.fulname}</td>
                                                    <td>${user.location}</td>
                                                    <td>${user.created_at}</td>
                                                    <td>${user.status}</td>
                                                </tr>
                                            `;
                                            $('#fbody').append(row);
                                            
                                             $('#sbut').text('GET DATA');
                                             
                                             
                                        });
                                         var printContents = document.getElementById('FaultTab').innerHTML;
                                  var originalContents = document.body.innerHTML;
                                
                                  document.body.innerHTML = printContents;
                                  window.print();
                                  document.body.innerHTML = originalContents;
                                    }
                                    
                                    if(type=='Electricity Requests')
                                    {
                                        $('#lcount').text('Total Electricity Requests Recorded : ' + response.count);
                                        
                                         document.querySelector("#lbody").innerHTML = "";
                                         
                                        response.resp.forEach(user => {
                                            const row = `
                                                <tr style="background-color: #f2f2f2;">
                                                    <td>${user.fulname}</td>
                                                    <td>${user.location}</td>
                                                    <td>${user.status}</td>
                                                </tr>
                                            `;
                                            $('#lbody').append(row);
                                            
                                             $('#sbut').text('GET DATA');
                                             
                                            
                                        });
                                        
                                          var printContents = document.getElementById('LightTab').innerHTML;
                                  var originalContents = document.body.innerHTML;
                                
                                  document.body.innerHTML = printContents;
                                  window.print();
                                  document.body.innerHTML = originalContents;
                                    }
                                    
                                    
                                }
                            });
                       }
                   }
                   else
                   {
                       if(fdated=='' || tdated=='')
                       {
                           alert('Dates Not Set');
                       }
                       else
                       {
                           //ajax
                           
                            $.ajax({
                                 headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                    'Access-Control-Allow-Origin' : '*'
                                    },
                                type: "POST",
                               // crossDomain: true,
                                url: "https://ecg.codepps.online/api/getReports",
                                data: {type:type,period:period,from:fdated,to:tdated},
                                dataType:'json',
                                success: function (response) {
                                    
                                    console.log(response);
                                    
                                    if(type=='Thefts')
                                    {
                                        $('#tcount').text('Total Theft Cases Recorded : ' + response.count);
                                        
                                         document.querySelector("#tbody").innerHTML = "";
                                         
                                        response.resp.forEach(user => {
                                            const row = `
                                                <tr>
                                                    <td style="border: 1px solid #ddd; padding: 8px;">${user.fulname}</td>
                                                    <td style="border: 1px solid #ddd; padding: 8px;">${user.location}</td>
                                                    <td style="border: 1px solid #ddd; padding: 8px;">${user.created_at}</td>
                                                    <td style="border: 1px solid #ddd; padding: 8px;">${user.status}</td>
                                                </tr>
                                            `;
                                            $('#tbody').append(row);
                                            
                                             $('#sbut').text('GET DATA');
                                        });
                                        
                                          var printContents = document.getElementById('TheftTab').innerHTML;
                                  var originalContents = document.body.innerHTML;
                                
                                  document.body.innerHTML = printContents;
                                  window.print();
                                  document.body.innerHTML = originalContents;
                                    }
                                    
                                    if(type=='Faults')
                                    {
                                         $('#fcount').text('Total Fault Cases Recorded : ' + response.count);
                                         
                                          document.querySelector("#fbody").innerHTML = "";
                                          
                                        response.resp.forEach(user => {
                                            const row = `
                                                <tr>
                                                    <td style="border: 1px solid #ddd; padding: 8px;">${user.fulname}</td>
                                                    <td style="border: 1px solid #ddd; padding: 8px;">${user.location}</td>
                                                    <td style="border: 1px solid #ddd; padding: 8px;">${user.created_at}</td>
                                                    <td style="border: 1px solid #ddd; padding: 8px;">${user.status}</td>
                                                </tr>
                                            `;
                                            $('#fbody').append(row);
                                            
                                             $('#sbut').text('GET DATA');
                                        });
                                        
                                          var printContents = document.getElementById('FaultTab').innerHTML;
                                  var originalContents = document.body.innerHTML;
                                
                                  document.body.innerHTML = printContents;
                                  window.print();
                                  document.body.innerHTML = originalContents;
                                    }
                                    
                                    if(type=='Electricity Requests')
                                    {
                                        $('#lcount').text('Total Electricity Requests Recorded : ' + response.count);
                                        
                                        document.querySelector("#lbody").innerHTML = "";
                                        
                                        response.resp.forEach(user => {
                                            const row = `
                                                <tr>
                                                    <td style="border: 1px solid #ddd; padding: 8px;">${user.fulname}</td>
                                                    <td style="border: 1px solid #ddd; padding: 8px;">${user.location}</td>
                                                    <td style="border: 1px solid #ddd; padding: 8px;">${user.status}</td>
                                                </tr>
                                            `;
                                            $('#lbody').append(row);
                                            
                                             $('#sbut').text('GET DATA');
                                        });
                                          var printContents = document.getElementById('LightTab').innerHTML;
                                  var originalContents = document.body.innerHTML;
                                
                                  document.body.innerHTML = printContents;
                                  window.print();
                                  document.body.innerHTML = originalContents;
                                    }
                                    
                                    
                                }
                            });
                       }
                   }
               }
       
         </script>

       
</body>

</html>
