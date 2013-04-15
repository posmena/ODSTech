<?php
header('Access-Control-Allow-Origin: *');
?>
<?php

// call search.php and print out html



//$client = isset($_GET['params']['feed_id']) ? $_GET['params']['feed_id'] : "";
$type = isset($_GET['type']) ? $_GET['type'] : "grid";
$width = isset($_GET['width']) ? $_GET['width'] : "500px";
$style = isset($_GET['style']) ? $_GET['style'] : "";

global $options;

$options = array();	 
	$options['background_colour'] = isset($_GET['background_colour']) ? $_GET['background_colour'] :'#FFFFFF';
	$options['border_colour'] = isset($_GET['border_colour']) ? $_GET['border_colour'] :'#D9D9D9';
	$options['product_name_bg_colour'] = isset($_GET['product_name_bg_colour']) ? $_GET['product_name_bg_colour'] :'#0F2A3C';
	$options['product_name_colour'] = isset($_GET['product_name_colour']) ? $_GET['product_name_colour'] :'#FFFFFF';
	$options['price_colour'] = isset($_GET['price_colour']) ? $_GET['price_colour'] :'#232221';
	$options['even_row_colour'] = isset($_GET['even_row_colour']) ? $_GET['even_row_colour'] :'#EEEEEE';
	$options['odd_row_colour'] = isset($_GET['odd_row_colour']) ? $_GET['odd_row_colour'] :'#F4EDED';
	$options['link_colour'] = isset($_GET['link_colour']) ? $_GET['link_colour'] :'#232221';
	

	function make_deep_link($network, $affid, $url, $campaign_id)
		{
		switch( $network )
			{
			case "KK":
				return $url . "&addedParams=true&custom1=" . $affid . "&custom2=network_KK";
				break;
			
			case "TD":
				return "http://tracker.tradedoubler.com/pan/TrackerServlet?a=" . $affid . "&g=" . $campaign_id . "&p=3431&url=" . urlencode($url) . "%26addedParams%3Dtrue%26custom1%3D" . $affid . "%26custom2%3Dnetwork_TD";
				break;
				
			case "AWIN":
				if( $_GET['params']['feed_id'] == 'disney' )
					{
					//return "http://www.awin1.com/awclick.php?awinmid=2632&awinaffid=" . $affid . "&p=" . urlencode($url) . "%26addedParams%3Dtrue%26custom1%3D" . $affid . "%26custom2%3Dnetwork_AWIN";				
					return "http://www.awin1.com/cread.php?s=386817&v=2632&q=182451&r=" . $affid . "&p=http%3A%2F%2Fwdtc.disneyinternational.com%2FHotel.asp%3FID%3D5509%26addedParams%3Dtrue%26custom1%3D" . $affid . "%26custom2%3Dnetwork_AWIN";
					
					}
				return "http://www.awin1.com/awclick.php?awinmid=3278&awinaffid=" . $affid . "&p=" . urlencode($url) . "%26addedParams%3Dtrue%26custom1%3D" . $affid . "%26custom2%3Dnetwork_AWIN";
				break;
			}
			
			return $url;
		}
		
//$params = isset($_GET['params']) ? "&" . $_GET['params'] : "";
//$maxproducts = isset($_GET['max']) ? "&" . $_GET['max'] : "10";

// get publisher id from URL
// get affiliate ids


if( $width < 210 ) 
{
	$width = "210px";
}

if( $type == "carousel" )
	{
	if( isset($_GET['ssl']) )
		{
		$content = "<iframe height='250px' margin=0 border=0 scrolling=no width='" . ($width+10) . "px' frameborder=0 src='https://odst.co.uk/api/p20/p20iframe.php?" . http_build_query($_GET) . "'></iframe>";	
		}
	else
		{
		$content = "<iframe height='250px' margin=0 border=0 scrolling=no width='" . ($width+10) . "px' frameborder=0 src='http://odst.co.uk/api/p20/p20iframe.php?" . http_build_query($_GET) . "'></iframe>";
		}
	//$content = "http://odst.co.uk/api/p20/p20iframe.php?user=m&pwd=test&params[feed_id]=" . $client . "&type=" . $type . "&width=" . $width . "'"
	//$content  = "<iframe";
	}
else
	{
	$data = curl_get_file_contents("http://odst.co.uk/api/p20/index.php?" . http_build_query($_GET));
	$products=json_decode($data,true);
	
		
global $publisher_id;
global $network;
global $affid;
global $campaign_id;

$publisher_id = $_GET['user'];
$network = "";
$affid = "";
$campaign_id = "";

// work out which network to use from publisherId;
$conn = new Mongo('localhost');
// access database
$mdb = $conn->odstech;
// access collection
$collection = $mdb->ot_users;
$publisher = $collection->findOne(array('_id' => new MongoId($publisher_id)));

if( $_GET['params']['feed_id'] == 'kelkoo' )
	{
	
	if( isset($publisher['AWIN']) )
		{
		$network = 'AWIN';
		$affid = $publisher['AWIN'];
		}
	elseif( isset($publisher['TD']) )
		{
		$network = 'TD';
		$affid = $publisher['TD'];
		$campaign_id = $publisher['campaign_id'];
		}
	else
		{
		$network = 'KK';
		$affid = $publisher['KK'];
		}	
	}
elseif( $_GET['params']['feed_id'] == 'disney' )
	{
		$network = 'AWIN';
		$affid = $publisher['AWIN'];		
	}
	
	
	$content = display_content_unit($products,"products",$type,$style,$width);
	}

 $callback = '';
    if (isset($_GET['callback']))
    {
        $callback = filter_var($_GET['callback'], FILTER_SANITIZE_STRING);
    }
	
	 if (isset($_GET['index']))
    {
        $index = filter_var($_GET['index'], FILTER_SANITIZE_STRING);
    }
	
echo $callback . '('.json_encode($content).',' . $index . ');';



	function curl_get_file_contents($url)
	{
		$c = curl_init();
		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($c, CURLOPT_URL, $url);
		curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 5000);
		curl_setopt($c, CURLOPT_TIMEOUT, 10000);
		$contents = curl_exec($c);
		curl_close($c);

		return $contents;
	}

function display_content_unit($products,$type,$display,$style,$width)
	{		
	global $publisher_id;
	global $network;
	global $affid;
	global $campaign_id;

	switch ($type) {
       
		case 'products':{				
				
			if ( $display == 'directory' ){
					$hotel = '<div ';				
					if( $width != '')
						{
						$hotel .= ' style="width:' . format_width($width) . '"';
						}						

					$hotel .= ' class="odst_hotel_table odst_hotels odst_reset odst_content_unit_table';

					if( $style != 'default' and $style != "")
					{
					$hotel .= ' ' . strtolower($style);
					}
				
					$hotel .= '"><!--div class="odst_hol_logo"></div-->';					

					$i = 1;
					$lastproduct = "";
					if(count($products) > 0) {
						foreach ($products as $product) {		
												   
							$product=  ((object)$product);
							$deeplink = $product->deeplink;
							if( $network == 'TD' && $_GET['params']['feed_id'] == 'kelkoo' )
								{
								$deeplink = $product->deeplink2;
								}
								
							if( $network == 'AWIN' && $_GET['params']['feed_id'] == 'kelkoo' )
								{
								$deeplink = $product->deeplink3;
								}
															
							$merchant = isset($product->merchant) ? $product->merchant : $product->program_name;
													
							//$merchant = $product->program_name;
							$hotel = $hotel . '<div class="row ';

							if ( $i%2 == false ) { $hotel = $hotel. ' even'; }

							$hotel = $hotel . '"';
							
							if ( $i%2 == false ) {	$hotel .= format_style(apply_style($style,'odd_row_colour')); }

									else { $hotel .= format_style(apply_style($style,'even_row_colour')); } 
								
							$hotel .= '>';

							$hotel .= '<div class="photo"><a target="_blank" rel="nofollow" href="' . make_deep_link($network, $affid, $deeplink, $campaign_id) . '"><img class="photo" src="'. $product->image_thumbnail . '"/></a></div>';

							$hotel .= '<div class="name"><a ' . format_style(apply_style($style,'product_name_colour') . apply_style($style,'product_name_bg_colour')) . ' class="name" target="_blank" rel="nofollow" href="' . make_deep_link($network, $affid, $deeplink, $campaign_id) . '">' . $product->product_name . '</a>';			
							$hotel .= '</div>';

							$hotel .= '<div class="location" ' . format_style(apply_style($style,'link_colour')) . '>' . odst_truncate($product->description,70) . '</div>';	
						
							$hotel .= '<div class="price"><a ' . format_style(apply_style($style,'price_colour')) . ' target="_blank" rel="nofollow" href="' . make_deep_link($network, $affid, $deeplink, $campaign_id) . '">&pound;' . format_price($product->price) . '</a>';
							$hotel .= '<div class="clear"></div><div class="merchant"><a ' . format_style(apply_style($style,'link_colour')) . ' target="_blank" rel="nofollow" href="' . make_deep_link($network, $affid, $deeplink, $campaign_id) . '">' . $merchant . '</a></div></div>';
							$hotel .= '</div>';
											
							$i+=1;
							
						}
					}
					
					$hotel = $hotel . '</div>';

					return $hotel;				
				}
			elseif ( $display == "grid"){

				if( $width != '')
						{
						$widthstyle = 'width:' . format_width($width) . ';';
						}
						
				$hotel = '<div ' . format_style($widthstyle . apply_style($style,'background_colour') . apply_style($style,'border_colour'));
				
				$hotel .= ' class="container odst_hol_logo odst_reset ';

				if( $style != 'default' and $style != "")
					{
					$hotel .= ' ' . strtolower($style);
					}

				$hotel .= '"><div class="odst_scontainer content_units">';
						
				$hotel .= '<div class="grid" name="">';

				$hotel .= '<ul class="slidingparts">';
				$lastproduct = "";
					if(count($products) > 0) {
						foreach ($products as $product) {
						
							$product=  ((object)$product);
							$deeplink = $product->deeplink;
							if( $network == 'TD' && $_GET['params']['feed_id'] == 'kelkoo' )
								{
								$deeplink = $product->deeplink2;
								}
								
							if( $network == 'AWIN' && $_GET['params']['feed_id'] == 'kelkoo' )
								{
								$deeplink = $product->deeplink3;
								}
								
							$merchant = isset($product->merchant) ? $product->merchant : $product->program_name;
							//$merchant = $product->program_name;
							$hotel .= '<li>';

							$hotel .='<table class="thumb_content" style="margin:auto">

				<tbody><tr>									

							<td class="thumb_heading">

							<a ' . format_style(apply_style($style,'product_name_colour') . apply_style($style,'product_name_bg_colour')) . ' target="_blank" rel="nofollow" class="name" href="' . make_deep_link($network, $affid, $deeplink, $campaign_id) . '">' . $product->product_name . '</a>

							</td>
							</tr>							
								
								
							<tr>

							<td><a target="_blank" rel="nofollow" href="' . make_deep_link($network, $affid, $deeplink, $campaign_id) . '"><img class="photo" src="' . $product->image_thumbnail . '"/></a>

							</td>

							</tr>
							<tr>

							<td class="merchant"><a ' . format_style(apply_style($style,'link_colour')) . ' target="_blank" rel="nofollow" href="' . make_deep_link($network, $affid, $deeplink, $campaign_id) . '">' . $merchant . '</a>

							</td>

							</tr>

									<tr><td class="thumb_price"' . format_style(apply_style($style,'price_colour')) . '>

							&pound;' . format_price($product->price) .'

						</td></tr>								
						</tbody></table>';																		
							 
							 }
						 }
					$hotel .= '</ul></div></div><div class="odst_hol_logo_padding"></div></div>';

					return $hotel;
									
				}
			else{

				if( $width != '')
						{
						$widthstyle = 'width:' . format_width($width) . ';';
						}
						
				$hotel = '<div ' . format_style($widthstyle . apply_style($style,'background_colour') . apply_style($style,'border_colour'));
				
				$hotel .= ' class="container odst_hol_logo odst_reset ';

				if( $style != 'default' and $style != "")
					{
					$hotel .= ' ' . strtolower($style);
					}

				$hotel .= '"><div class="odst_scontainer content_units">';
						
				$hotel .= '<div class="slider">';

				$hotel .= '<ul class="slidingparts">';
				$lastproduct = "";
					if(count($products) > 0) {
						foreach ($products as $product) {
								$product=  ((object)$product);
								$deeplink = $product->deeplink;
							if( $network == 'TD' && $_GET['params']['feed_id'] == 'kelkoo' )
								{
								$deeplink = $product->deeplink2;
								}
								
							if( $network == 'AWIN' && $_GET['params']['feed_id'] == 'kelkoo' )
								{
								$deeplink = $product->deeplink3;
								}
						    $merchant = isset($product->merchant) ? $product->merchant : $product->program_name;
							//$merchant = $product->program_name;
							$hotel .= '<li>';

							$hotel .='<table class="thumb_content">

				<tbody><tr>									

							<td class="thumb_heading">

							<a ' . format_style(apply_style($style,'product_name_colour') . apply_style($style,'product_name_bg_colour')) . ' target="_blank" rel="nofollow" class="name" href="' . make_deep_link($network, $affid, $deeplink, $campaign_id) . '">' . $product->product_name . '</a>

							</td>
							</tr>							
								
								
							<tr>

							<td><a target="_blank" rel="nofollow" href="' . make_deep_link($network, $affid, $deeplink, $campaign_id) . '"><img class="thumb_image" src="' . $product->image_thumbnail . '"/></a>

							</td>

							</tr>
							<tr>';
							
							if( $style == "disney" )
							{
							$hotel .= '<td class="merchant">Flight, Hotel + Park Tickets</td></tr>';
							$hotel .= '<tr><td class="merchant">Starting from';							
							}
							else
							{
							$hotel .= '<td class="merchant"><a ' . format_style(apply_style($style,'link_colour')) . ' target="_blank" rel="nofollow" href="' . make_deep_link($network, $affid, $deeplink, $campaign_id) . '">' . $merchant . '</a>';
							}
							
							
							$hotel .= '</td>

							</tr>

									<tr><td class="thumb_price"' . format_style(apply_style($style,'price_colour')) . '>

							&pound;' . format_price($product->price) .'

						</td></tr>								
						</tbody></table>';																		
							 	
							 }
						 }
					$hotel .= '</ul></div></div>';
					
					if($style == 'disney' ) 
						{
						$hotel .= '<div id="disclaimer" style="color: #8C8C8C;font-family: arial;font-size: 10px;margin-top:20px;padding-left:25px;width:250px;float:left">* Prices based on 2 adults and 2 children under 11.</div>';
						}
					$hotel .= '<div class="odst_hol_logo_padding"></div></div>';

					return $hotel;
									
				}			
			
				break;			
			}
		
			case "price":{

					return format_price(PriceMatcherLoader::get_product_price($productid,$feedid));
					
					break;
				}

				
    }
		
	}
	
	
	
	function format_style($style)
	{
	if( $style == '')
		{
			return '';
		}

	return ' style="' . $style . '"';
	}

	
	function format_width($width)
	{
		if ($width == '' ) { return $width; }
		$width = strtolower($width);
		if( strpos($width,"px") || strpos($width,"%") ) { return $width ; }

		return $width . "px";		
	}

	

	function apply_style($stylesheet,$element)
	{
	global $options;
	$style = '';

			switch ( $stylesheet )	{
				case 'default':		
					if( $options[$element] != '' ){
						switch ( $element ){
							case 'background_colour':						
							$style='background-color:' . $options[$element] .';';
							break;
							
							case 'border_colour':
							$style='border-color:' . $options[$element] .';';
							break;
							
							case 'product_name_bg_colour':
							$style='background-color:' . $options[$element] .';';
							break;
							
							case 'product_name_colour':
							$style='color:' . $options[$element] .';';
							break;
							
							case 'price_colour':
							$style='color:' . $options[$element] .';';
							break;
							
							case 'link_colour':
							$style='color:' . $options[$element] .';';
							break;
											
							case 'even_row_colour':
							$style='background-color:' . $options[$element] .';';
							break;
						
							case 'odd_row_colour':
							$style='background-color:' . $options[$element] .';';
							break;
							}
						break;
					}
				}			
			return $style;
	}

	 function format_price($price) { 
	// return with no change if string is shorter than $limit 

	
		return sprintf("%01.2f", $price);
	 }
	 
	 
	 
	function odst_truncate($string, $limit, $break=" ", $pad="...") { 
// return with no change if string is shorter than $limit 

if(strlen($string) <= $limit) return $string; 

// is $break present between $limit and the end of the string?
 if(false !== ($breakpoint = strpos($string, $break, $limit))) { 
		if($breakpoint < strlen($string) - 1) { 
				$string = substr($string, 0, $breakpoint) . $pad; } 				
				} return $string; 
 }

 ?>