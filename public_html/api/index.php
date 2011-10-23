<?php

if (false === isset($_GET['user']) || false === isset($_GET['pass']) || false === isset($_GET['source'])) {
	print 'You are not authorised to use this service.';
	exit;
}

$email    = stripslashes($_GET['user']);
$password = stripslashes($_GET['pass']);

$conn = new Mongo();
$db   = $conn->odstech;
// access collection

$validUser = (bool) $db->ot_users->find(array('username' => $email, 'password' => md5($password)))->count();


switch ($_GET['source']) {
	default:
	{
		echo 'Please supply a source.';
		break;
	}

	case 'easyjet':
	{
		switch($_GET['type']) {
			default:
			{
				echo 'Please supply a type';
				break;
			}

			case 'properties':
			{
				$conn = new Mongo('localhost');
				// access database
				$mdb = $conn->odstech;
				// access collection
		  		$collection = $mdb->easyjet_properties;
				
				$search = array();
				if(true === array_key_exists('rgion', $_GET)){
					$search = array('region' => $_GET['rgion']);
				}

				if(true === array_key_exists('limit', $_GET)){
					$limit =$_GET['limit'];
					$properties = $collection->find($search)->limit($limit);
				} else {
					$properties = $collection->find($search);
				}

		  		$nwProperties = array();
		  		foreach ($properties as $property) {
		  			unset($property['_id']);

		  			$nwProperties[] = $property;
		  		}

		  		switch ($_GET['format']) {
		  			case 'xml':
		  			{	
		  				if (false === array_key_exists('preview', $_GET)) {
		  					header('Content-type: application/xml');		  					
		  				}
		  				
			  			$sxe = new SimpleXMLElement('<properties></properties>');
						$sxe->addAttribute('type', 'properties');
						if(is_array($nwProperties) && count($nwProperties) > 0)
		  				foreach ($nwProperties as $property) {
		  					$xmlProp = $sxe->addChild('property');
		  					foreach($property as $fieldname => $fieldvalue) {
		  						$innit = mb_convert_encoding($fieldvalue, 'UTF-8', mb_detect_encoding($fieldvalue));
		  						$xmlProp->addChild($fieldname, htmlspecialchars($innit, ENT_NOQUOTES, 'UTF-8'));

		  					}
		  				}
		  				print $sxe->asXML();
		  				break;
		  			}
		  			default:
		  			{
		  				$stuff = json_encode($nwProperties);
		  				print $stuff;
		  			}
		  		}
			}
		}
	}	
}

function umlaute($text){ 
    $returnvalue=""; 
    for($i=0;$i<strlen($text);$i++){ 
        $teil=hexdec(rawurlencode(substr($text, $i, 1))); 
        if($teil<32||$teil>1114111){ 
            $returnvalue.=substr($text, $i, 1); 
        }else{ 
            $returnvalue.="&#".$teil.";"; 
        } 
    } 
    return $returnvalue; 
} 