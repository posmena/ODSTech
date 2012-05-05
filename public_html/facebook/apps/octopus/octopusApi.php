<?php

    /*
     * 
     * This wraps stuff
     * 
     */

     // city suggestions
     class OctopusCity {
         public $id;
         public $name;
         
         function OctopusCity($id, $name) {
             $this->id = $id;
             $this->name = $name;
         }
     }
     
     // hotel search
     class OctopusSearch {
         public $cityCode;
         public $checkInDate;
         public $checkOutDate;
         public $numberOfRooms;
     }
     
     // hotel suggestions
     class OctopusHotel {
         public $id;
         public $name;
     }
    
     class OctopusFeed {
         
         /* interface bits and pieces */
         private $apiUrl = 'http://interface1.demo.octopustravel.com/XMLService';
         private $apiKey = 'xml@octopus.com';
         private $apiBrand = 'LON_FBUK01_XML';
         private $apiPass = 'FBProd1@xml';
          
         /* Gets city / hotel suggestions */
         public function getCitySuggestions($text) {
               
             // pick up response
             $responseXml = $this->performRequest('<SearchCityRequest CountryCode="GB">
      <CityName><![CDATA[' . $text . ']]></CityName>
    </SearchCityRequest>');

                         
             // return dummy results
             return array(
                 new OctopusCity(123, 'Malaga, Spain'),
                 new OctopusCity(234, 'Somwhere, England')
             ); 
         }
 
         /* performs a request */
         private function performRequest($subRequestXml) {
             
             // build the request XML
             $requestXmlString =
                    '<xml version="1.0" encoding="UTF-8"?><Request>
                        <Source>
                            <RequestorID Client="' . $this->apiBrand . '" EMailAddress="' . $this->apiKey . '" Password="' . $this->apiPass . '"/>
                            <RequestorPreferences Language="en" Currency="GBP" Country="GB">
                                <RequestMode>SYNCHRONOUS</RequestMode>
                            </RequestorPreferences>
                        </Source>
						<RequestDetails>' . $subRequestXml . '
						</RequestDetails>
                    </Request>';
           
            
            echo( $requestXmlString );
			
             // make the actual request
             $curl = curl_init($this->apiUrl);
             curl_setopt($curl, CURLOPT_POST, 1);
             curl_setopt($curl, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml")); 
             curl_setopt($curl, CURLOPT_POSTFIELDS, $requestXmlString);
			 curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			 curl_setopt($curl, CURLOPT_MUTE, 1);
             $curlResult = curl_exec($curl);
            
			echo($curlResult);
			
             // process the response
             if (empty($curlResult)) {

                 // get the response XML
                 throw new Exception('not working');

             } else {

                 // give back an exception
                 return ($curlResult);
             }
             
             // close the handle
             curl_close($curl);
         }
     }

?>
