<?php

    //define JSON feed url from Firebase 
   
    $jsonfeed = "YOUR FIREBASE URL GOES HERE";

    //  Initiate curl
    $ch = curl_init();
     // Disable SSL verification
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    // Will return the response, if false it print the response
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // Set the url
    curl_setopt($ch, CURLOPT_URL,$jsonfeed);
   
    // Execute
    $result=curl_exec($ch);
 

    // Closing
    curl_close($ch);
    // Will dump a beauty json 
    $tripsviewer = json_decode($result, true);
    
    
    
    //Open the GeoJSON file and write Firebase contents to it.
   
    
    
    $file = 'biketrips.geojson';
    // Open the file to get existing content
    $current = file_get_contents($file);
    // Append a new person to the file
    // Write the contents back to the file

    $tripsiterator = new RecursiveArrayIterator($tripsviewer);
    

               $tripStart = "{".'"'."type".'":'.'"'."FeatureCollection".'"'.',"'."features".'" : [';
               file_put_contents($file, $tripStart,FILE_APPEND);


              
    while ($tripsiterator->valid()) {
  
	    if ($tripsiterator->hasChildren()) {
	    
	    // print beginning of GeoJSON shape
	    

	        
	    $shapeStart = "{".'"'."type".'"'.":".'"'."Feature".'",'.'"'."geometry".'":'."{".'"'."type".'":'.'"'."MultiPoint".'"'.",";
            file_put_contents($file, $shapeStart,FILE_APPEND);

		
	        foreach ($tripsiterator->getChildren() as $key => $value) {
	        
                    
	        	 if($key == 'purpose'){
	        	 
	        	 $purposeStart = ',"'."properties".'":{'.'"'."purpose".'":'.'"';
                         file_put_contents($file, $purposeStart,FILE_APPEND);
                         
                         $purposeText = ''."$value".'"';
                         file_put_contents($file, $purposeText,FILE_APPEND);
                         
                         $purposeEnd = '}},';
                         file_put_contents($file, $purposeEnd,FILE_APPEND);
	 
               
                     }
                     

	        
	        

	        if ($key == 'coords'){
	        
	                   
		         
	                   $coordStart = '"'."coordinates".'"'.':[';  
	                   file_put_contents($file, $coordStart,FILE_APPEND);  
	                  
                        
		          
		         // preg_match_all("/\"l\":[0-9][0-9].[0-9]+,\"h\":5,\"v\":3,\"r\":\"[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9][\s][0-9][0-9]:[0-9][0-9]:[0-9][0-9]\",\"a\":[0-9]+.[0-9]+,\"n\":-[0-9]{3}.[0-9]+/", $value, $coordmatches, PREG_SET_ORDER);
		         //THIS SECTION USES A REGULAR EXPRESSION TO FIND VALID LOCATION AND TRIP DATA TO MAP
		         preg_match_all("/\"l\":[0-9][0-9].[0-9]+,\"h\":5,\"v\":-1,\"r\":\"[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9][\s][0-9][0-9]:[0-9][0-9]:[0-9][0-9]\",\"a\":[0-9],\"n\":-[0-9]{3}.[0-9]+/", $value, $coordmatches, PREG_SET_ORDER);
		          
		           
		          $coordArray = new RecursiveIteratorIterator(new RecursiveArrayIterator($coordmatches));
		          
	
	 
	             
		             foreach($coordArray as $coord) {
 
			       $thisCoord = $coord;
			        /*   
			       $coordPairStart = '['; 
			       file_put_contents($file, $coordPairStart,FILE_APPEND);
			        */
			     
			       $latForm = "/-[0-9]{3}.[0-9]+/";
			       preg_match($latForm, $thisCoord, $thisLat);
			    	
	                       $longForm = "/[0-9]{2}.[0-9]+/";
			       preg_match($longForm, $thisCoord, $thisLong);
                               
                               $printLat = "[" . implode($thisLat) . ",";
			       $printLong = "" . implode($thisLong) . "],";
                                                 
			       file_put_contents($file, $printLat,FILE_APPEND);
                               file_put_contents($file, $printLong,FILE_APPEND);

                 
 		               $coordArray->next();

	                 
	                           }
	                           

	                           
	                 $coordEnd = ']}';  
	                 
	                 file_put_contents($file, $coordEnd,FILE_APPEND); 
	                 

                    }
                     
                     
               }           
    		
    $tripsiterator->next();
    echo "count<br>";
	                 
          
	}
    }
    	                 //Replace ends of coordinate sets with brackets without commas.
	                 $message = file_get_contents($file);
                         $message =  str_replace("],]", "]]", $message);
                         $tripListEnd = ']}';  
                         file_put_contents($file, $tripListEnd,FILE_APPEND); 
                         

?>
