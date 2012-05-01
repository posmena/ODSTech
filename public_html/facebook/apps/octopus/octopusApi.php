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
             $responseXml = $this->performRequest(simplexml_load_string(
                     '<RequestDetails>
    <SearchCityRequest CountryCode="GB">
      <CityName/>
      <CityCode/>
    </SearchCityRequest>
  </RequestDetails>'
                     ));
                         
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
                    '<Request>
                        <Source>
                            <RequestorID Client="' . $this->apiBrand . '" EMailAddress="' . $this->apiKey . '" Password="' . $this->apiPass . '"/>
                            <RequestorPreferences Language="en" Currency="GBP" Country="GB">
                                <RequestMode>SYNCHRONOUS</RequestMode>
                            </RequestorPreferences>
                        </Source>
                    </Request>';
             $requestXml = simplexml_load_string($requestXmlString);
             $requestXml->addChild($subRequestXml);
            
             // make the actual request
             $curl = curl_init($this->apiUrl);
             curl_setopt($curl, CURLOPT_POST, 1);
             curl_setopt($curl, CURLOPT_HTTPHEADER, Array("Content-Type: application/xml")); 
             curl_setopt($curl, CURLOPT_POSTFIELDS, 'data=' . urlencode($requestXml->asXML()));
             $curlResult = curl_exec($curl);
            
             // process the response
             if (empty($curlResult)) {

                 // get the response XML
                 throw new Exception('not working');

             } else {

                 // give back an exception
                 return simplexml_load_string($curlResult);
             }
             
             // close the handle
             curl_close($curl);
         }
     }

?>
