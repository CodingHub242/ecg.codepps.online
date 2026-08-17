@php 
use App\Models\User;

foreach($theft as $t)
{
   
}

@endphp
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
<style>
    #map,#map2 {
        height: 300px;
        width: 100%;
    }
</style>
<body>
   
    <div class="mx-auto mt-10 ">

        <div class="bg-white p-6 rounded shadow-lg  lg:!w-[50%]  mx-auto">
            <div class=" mb-4">
                
            </div>
            <h2 class="text-4xl font-bold mb-4">Fault Details</h2>
            <form id="SubForm">
                <!--@csrf-->
                <div class="mb-4" >
                    <label for="custemail" class="block text-gray-700 font-bold mb-2">Full Name  </label>
                    <input type="text" id="fulname" disabled name="fulname" class="w-full  border-gray-300 border-2 rounded p-3" value="{{$t->fulname}}" style="color:darkgray;">
                </div>

                <div class="mb-4" >
                    <label for="custphone" class="block text-gray-700 font-bold mb-2">Fault Description  </label>
                    <input type="text" id="items" name="items" disabled class="w-full  border-gray-300 border-2 rounded p-3" value="{{$t->fault}}">
                </div>


                <div class="mb-4" >
                    <label for="custphone" class="block text-gray-700 font-bold mb-2">Date & Time Sent</label>
                    @php 
                         $d = date('F j, Y, g:i a', strtotime($t->created_at));
                    @endphp
                    <input type="text" id="created_at" name="created_at" disabled class="w-full  border-gray-300 border-2 rounded p-3" value="{{$d}}">
                </div>

                <div class="mb-4" >
                    <label for="custphone" class="block text-gray-700 font-bold mb-2">GPS Coordinates  </label>
                    <p>Longitude</p>
                    <input type="text" id="longs" name="longs" disabled class="w-full  border-gray-300 border-2 rounded p-3" value="{{$t->lng}}">

                    <p class="mt-3">Latitude</p>
                    <input type="text" id="lats" name="lats" disabled class="w-full  border-gray-300 border-2 rounded p-3" value="{{$t->lat}}">
                </div>
                
               
                
                <div class="mb-4" >
                    <label for="custphone" class="block text-gray-700 font-bold mb-2">Status  </label>
                   
                    <select  id="stat" onchange="ChangeStat()" name="stat" class="w-full border-gray-300 border-2 rounded p-3" style="cursor:pointer;">
                        <option selected value="{{$t->status}}">{{$t->status}}</option>
                        
                        <option value="pending">pending</option>
                        <option value="completed">completed</option>
                        
                        
                    </select>
                </div>

                <div class="mb-4" >
                    <label for="custphone" class="block text-gray-700 font-bold mb-2">Image : </label>
                    @php 
                         $im = 'https://ecg.codepps.online/storage/'.str_replace(['[', ']', '"'], '', $t->images);
                    @endphp
                    <img src="{{$im}}" style="width: 90%;max-width: 353px;height: auto;"/>
                </div>
                
                <div class="mb-4" >
                    <label for="custphone" class="block text-gray-700 font-bold mb-2">Location : <span><input type="text" class="form-control" disabled id="location" placeholder="Location Selected Apears Here"></span></label>
                        
                    <div id="map"></div>
                           
                </div>

               <input type="text" value="{{$t->lat}}" hidden id="lat"/>
               <input type="text" value="{{$t->lng}}" hidden id="lng"/>
                <input type="text" value="{{$t->id}}" hidden id="tID"/>
                <input name="csrfToken" id="csrf" value="{{ csrf_token() }}" type="hidden"/>    
               
            </form>
        </div>
    </div>
   

    <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
        {{-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCDY-7WZpFnqvRIftjFOFeu2TPk650d-Hg&callback=initMap" async defer></script> --}}
       <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
         <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCDY-7WZpFnqvRIftjFOFeu2TPk650d-Hg&libraries=places&callback=initMap" async></script>
         
         
         <script>
              function ChangeStat()
             {
                 const stat = $('#stat').val();
                 const tID = $('#tID').val();
                 const _token = $('#csrf').val();
                 
                 console.log(stat,tID);
                 
                 $.ajax({
                     headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                    type: "POST",
                    url: "https://ecg.codepps.online/updatefault",
                    data: {stat:stat,tID:tID,token:_token},
                    dataType:'json',
                    success: function (response) {
                        //service.php response
                        console.log(response);
                    }
                });
                 
                //  $.ajax({
                //     url:'https://ecg.codepps.online/updatetheft',
                //     type:'POST',
                //     data: {stat:stat,tID:tID},
                //     submit:function(resp){
                //         if(resp.success)
                //         {
                //             alert('Status Updated');
                //         }
                //         else
                //         {
                            
                //         }
                //     }
                    
                //  });
             }
       
         </script>

        <script>
            let map, marker;
            //let map2, marker2;
           

            function initMap() {
                const lat = Number($('#lat').val());
                const lng = Number($('#lng').val());//event.latLng.lng();
               // console.log(lat,lng);
                const initialPosition = { lat: lat, lng: lng }; 
                map = new google.maps.Map(document.getElementById("map"), {
                    center: initialPosition,
                    zoom: 10,
                });

                  const apiKey = 'AIzaSyCDY-7WZpFnqvRIftjFOFeu2TPk650d-Hg';
                  
                  const url = `https://maps.googleapis.com/maps/api/geocode/json?address=${lat},${lng}&key=AIzaSyCDY-7WZpFnqvRIftjFOFeu2TPk650d-Hg&sensor=true`;
                   fetch(url)
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === "OK") {
                            //console.log(data.status);
                            const placeName = data.results[0].formatted_address;
                            document.getElementById('location').setAttribute("value", placeName);// = `You are in: ${placeName}`;
                            
                            } else {
                            document.getElementById('location').setAttribute("value",  'Unable to fetch location.');// = 'Unable to fetch location.';
                           // console.log(data.status);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            document.getElementById('location').setAttribute("value",  'An error occurred.'); //= 'An error occurred.';
                        });
                        
                          if (marker) marker.setMap(null); // Remove previous marker
                        marker = new google.maps.Marker({
                        position: {lat:lat,lng:lng},
                        map: map,
                    });
                        
                        
                        
                map.addListener("click", (event) => {
                   

                    document.getElementById("latitude").setAttribute("value", lat);//value = lat;
                    document.getElementById("longitude").setAttribute("value", lng);//value = lng;

                    ///////////////////////////////////////////////////////
                        // const latitude = 5.6037;  // Example: Accra, Ghana
                        // const longitude = -0.1870;
                       
                        // Google Maps Geocoding API URL
                        const apiKey = 'AIzaSyCDY-7WZpFnqvRIftjFOFeu2TPk650d-Hg'; // Replace with your API key
                        //const url = `https://maps.googleapis.com/maps/api/geocode/json?latlng=${lat},${lng}&sensor=truekey=${apiKey}`;
                         const url = `https://maps.googleapis.com/maps/api/geocode/json?address=${lat},${lng}&key=AIzaSyCDY-7WZpFnqvRIftjFOFeu2TPk650d-Hg&sensor=true`;
                        // Fetch the place name
                        fetch(url)
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === "OK") {
                            //console.log(data.status);
                            const placeName = data.results[0].formatted_address;
                            document.getElementById('location').setAttribute("value", placeName);// = `You are in: ${placeName}`;
                            document.getElementById('locations').setAttribute("value", placeName);// = `You are in: ${placeName}`;
                            } else {
                            document.getElementById('location').setAttribute("value",  'Unable to fetch location.');// = 'Unable to fetch location.';
                           // console.log(data.status);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            document.getElementById('location').setAttribute("value",  'An error occurred.'); //= 'An error occurred.';
                        });


                    //////////////////////////////////////////////////////

                    if (marker) marker.setMap(null); // Remove previous marker
                    marker = new google.maps.Marker({
                        position: {lat:lat,lng:lng},
                        map: map,
                    });
                });

                //////////////////WHILE SEARCHING - THEFT////////////////////////////////
                // const input = document.getElementById("search-box");
                // const searchBox = new google.maps.places.SearchBox(input);

                // map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

                // // Bias the SearchBox results towards the map's viewport
                // map.addListener("bounds_changed", () => {
                //     searchBox.setBounds(map.getBounds());
                // });

                // searchBox.addListener("places_changed", (ev) => {
                //     const places = searchBox.getPlaces();

                //     // const lat = ev.latLng.lat();
                //     // const lng = ev.latLng.lng();
                //      const urll = `https://maps.googleapis.com/maps/api/geocode/json?address=${places[0].formatted_address}&key=AIzaSyCDY-7WZpFnqvRIftjFOFeu2TPk650d-Hg&sensor=true`;
                //         // Fetch the place name
                //         fetch(urll)
                //         .then(response => response.json())
                //         .then(data => {
                //             if (data.status === "OK") {
                //             //console.log(data.results[0].geometry.location.lat);
                //                 document.getElementById("latitude").setAttribute("value", data.results[0].geometry.location.lat);
                //                 document.getElementById("longitude").setAttribute("value", data.results[0].geometry.location.lng);
                //                 document.getElementById('location').setAttribute("value", data.results[0].formatted_address);
                //                 document.getElementById('locations').setAttribute("value", data.results[0].formatted_address);
                //             } else {
                //             //document.getElementById('location').setAttribute("value",  'Unable to fetch location.');// = 'Unable to fetch location.';
                //             console.log(data.status);
                //             }
                //         })
                //         .catch(error => {
                //             console.error('Error:', error);
                //           // document.getElementById('location').setAttribute("value",  'An error occurred.'); //= 'An error occurred.';
                //         });

                //     if (places.length === 0) return;

                //     // Clear out the old marker
                //     if (marker) marker.setMap(null);

                //     // Get the first place and set a marker
                //     const place = places[0];
                //     if (!place.geometry || !place.geometry.location) return;

                //     marker = new google.maps.Marker({
                //     map,
                //     position: place.geometry.location,
                //     });

                //     map.setCenter(place.geometry.location);
                //     map.setZoom(15);
                //     marker.setPosition(place.geometry.location);
                // });
                
                /////////////////////////////////////////////////////////////////////////////
                ///////////////////////////WHILE SEARCHING - FAULT////////////////////////////////
            //     map2 = new google.maps.Map(document.getElementById("map2"), {
            //         center: initialPosition,
            //         zoom: 10,
            //     });

            //     map2.addListener("click", (event) => {
            //         const lat = event.latLng.lat();
            //         const lng = event.latLng.lng();

            //         document.getElementById("latitudes").setAttribute("value", lat);//value = lat;
            //         document.getElementById("longitudes").setAttribute("value", lng);//value = lng;

            //         ///////////////////////////////////////////////////////
            //             // const latitude = 5.6037;  // Example: Accra, Ghana
            //             // const longitude = -0.1870;
                       
            //             // Google Maps Geocoding API URL
            //             const apiKey = 'AIzaSyCDY-7WZpFnqvRIftjFOFeu2TPk650d-Hg'; // Replace with your API key
            //             //const url = `https://maps.googleapis.com/maps/api/geocode/json?latlng=${lat},${lng}&sensor=truekey=${apiKey}`;
            //              const url = `https://maps.googleapis.com/maps/api/geocode/json?address=${lat},${lng}&key=AIzaSyCDY-7WZpFnqvRIftjFOFeu2TPk650d-Hg&sensor=true`;
            //             // Fetch the place name
            //             fetch(url)
            //             .then(response => response.json())
            //             .then(data => {
            //                 if (data.status === "OK") {
            //                 //console.log(data.status);
            //                 const placeName = data.results[0].formatted_address;
            //                 document.getElementById('mlocation').setAttribute("value", placeName);
            //                  document.getElementById('mlocations').setAttribute("value", placeName);// = `You are in: ${placeName}`;
            //                 } else {
            //                 document.getElementById('location').setAttribute("value",  'Unable to fetch location.');// = 'Unable to fetch location.';
            //               // console.log(data.status);
            //                 }
            //             })
            //             .catch(error => {
            //                 console.error('Error:', error);
            //                 document.getElementById('location').setAttribute("value",  'An error occurred.'); //= 'An error occurred.';
            //             });


            //         //////////////////////////////////////////////////////

            //         if (marker) marker.setMap(null); // Remove previous marker
            //         marker = new google.maps.Marker({
            //             position: event.latLng,
            //             map: map,
            //         });

            //          if (marker2) marker2.setMap(null); // Remove previous marker
            //         marker2 = new google.maps.Marker({
            //             position: event.latLng,
            //             map: map2,
            //         });
            //     });

            //     const finput = document.getElementById("msearch-box");
            //     const fsearchBox = new google.maps.places.SearchBox(finput);

            //     map2.controls[google.maps.ControlPosition.TOP_LEFT].push(finput);

            //     // Bias the SearchBox results towards the map's viewport
            //     map2.addListener("bounds_changed", () => {
            //         fsearchBox.setBounds(map2.getBounds());
            //     });

            //     fsearchBox.addListener("places_changed", (ev) => {
            //         const fplaces = fsearchBox.getPlaces();

            //         // const lat = ev.latLng.lat();
            //         // const lng = ev.latLng.lng();
            //          const furll = `https://maps.googleapis.com/maps/api/geocode/json?address=${fplaces[0].formatted_address}&key=AIzaSyCDY-7WZpFnqvRIftjFOFeu2TPk650d-Hg&sensor=true`;
            //             // Fetch the place name
            //             fetch(furll)
            //             .then(response => response.json())
            //             .then(data => {
            //                 if (data.status === "OK") {
            //                 //console.log(data.results[0].geometry.location.lat);
            //                     document.getElementById("latitudes").setAttribute("value", data.results[0].geometry.location.lat);
            //                     document.getElementById("longitudes").setAttribute("value", data.results[0].geometry.location.lng);
            //                     document.getElementById('mlocation').setAttribute("value", data.results[0].formatted_address);
            //                     document.getElementById('mlocations').setAttribute("value", data.results[0].formatted_address);
            //                 } else {
            //                 //document.getElementById('location').setAttribute("value",  'Unable to fetch location.');// = 'Unable to fetch location.';
            //                 //console.log(data.status);
            //                 }
            //             })
            //             .catch(error => {
            //                 console.error('Error:', error);
            //               // document.getElementById('location').setAttribute("value",  'An error occurred.'); //= 'An error occurred.';
            //             });

            //         if (fplaces.length === 0) return;

            //         // Clear out the old marker
            //         if (marker2) marker2.setMap(null);

            //         // Get the first place and set a marker
            //         const fplace = fplaces[0];
            //         if (!fplace.geometry || !fplace.geometry.location) return;

            //         marker2 = new google.maps.Marker({
            //         map2,
            //         position: fplace.geometry.location,
            //         });

            //         map2.setCenter(fplace.geometry.location);
            //         map2.setZoom(15);
            //         marker2.setPosition(fplace.geometry.location);
            //     });
             }
             
        </script>
</body>

</html>
