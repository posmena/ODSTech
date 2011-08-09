<?php


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
		  					header('Content-type: text/xml');		  					
		  				}
		  				
			  			$sxe = new SimpleXMLElement('<properties></properties>');
						$sxe->addAttribute('type', 'properties');
						if(is_array($nwProperties) && count($nwProperties) > 0)
		  				foreach ($nwProperties as $property) {
		  					$xmlProp = $sxe->addChild('property');
		  					foreach($property as $fieldname => $fieldvalue) {
		  						$xmlProp->addChild($fieldname, htmlspecialchars($fieldvalue));

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