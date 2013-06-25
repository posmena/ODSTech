<?php

$conn = new Mongo('localhost');
$db = $conn->odstech;
$jtdb = $db->jtSpas_PJH;
$jtdb->drop();

function get_content($url)  
{

    $ch = curl_init();  
    curl_setopt($ch, CURLOPT_URL, $url);  
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; MSIE 7.0; Windows NT 6.0; en-US)');
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");
    curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt"); //saved cookies
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
       
    $response  = curl_exec ($ch);  
    
 
		$header_size = curl_getinfo($ch,CURLINFO_HEADER_SIZE);
        $string = substr( $response, $header_size );
	 curl_close ($ch); 	
    return $string;      
}


function post_content($url,$fields_string, $fields_count)  
{
//foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
//rtrim($fields_string, '&');

    $ch = curl_init();  
    curl_setopt($ch, CURLOPT_URL, $url);  
	curl_setopt($ch,CURLOPT_POST, $fields_count);
	curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; MSIE 7.0; Windows NT 6.0; en-US)');
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");
    curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt"); //saved cookies
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
       
	    $response  = curl_exec ($ch);  
   $header_size = curl_getinfo($ch,CURLINFO_HEADER_SIZE);
        $string = substr( $response, $header_size );
	 curl_close ($ch); 	
    return $string;      
}


$res = post_content('https://retail.pjhgroup.com/webapp/wcs/stores/servlet/rc701-rc701-sales-catalog/Logon','storeId=10001&catalogId=10051&langId=-1&reLogonURL=GuestHomePageView&errorViewName=GuestHomePageView&myAcctMain=&URL=UserAccountView%3FcatalogId%3D10051%26fromIndex%3D1%26myAcctMain%3D1%26langId%3D-1%26storeId%3D10001%26page%3D&logonId=21687&logonPassword=vallymoney12%3F',9);
$res = post_content('https://retail.pjhgroup.com/webapp/wcs/stores/servlet/rc701-rc701-sales-catalog/Logon','storeId=10001&catalogId=10051&langId=-1&reLogonURL=GuestHomePageView&errorViewName=GuestHomePageView&myAcctMain=&URL=UserAccountView%3FcatalogId%3D10051%26fromIndex%3D1%26myAcctMain%3D1%26langId%3D-1%26storeId%3D10001%26page%3D&logonId=21687&logonPassword=vallymoney12%3F',9);
$res = get_content('https://retail.pjhgroup.com/webapp/wcs/stores/servlet/rc701-rc701-sales-catalog/guest-products');
$res = get_content('https://retail.pjhgroup.com/webapp/wcs/stores/servlet/rc701-rc701-sales-catalog/guest-products');

// use regexp to get all the urls in <li> navigation

$regexp = "/<a href=\"(http:\/\/retail.pjhgroup.com\/webapp\/wcs\/stores\/servlet\/rc701-rc701-sales-catalog\/search\/.*)\">(.*)<\/a>/siU";

if (preg_match_all($regexp, $res, $matches)) {

						foreach($matches[1] as $key =>  $product_url) {
							//if ($key % 2) {
								//print($matches[2][$key] . ":::" . $product_url . "<br>");
								    //echo("Found " . $product_url . "<br>");
									ProcessCategoryPage(htmlspecialchars_decode($product_url), FALSE);
									
							//}
						}						
					}
 

 
 //download URL
 
 //regexp:
function ProcessTopLevelPage($page)
{
// get data within <ul class=\"categories\"> ul
// extract the links

$regexp = "/(?<=<ul class=\"categories\">)(.*)(?=<\/ul>)/siU";
	if( preg_match_all($regexp, $page, $matches) ) {
			
			foreach($matches[1] as $key =>  $val) {
			$res = trim($val);
			
			}		
					
			}
			
			
$regexp = "/href=\"(http:\/\/retail.pjhgroup.com\/webapp\/wcs\/stores\/servlet\/SearchDisplay?.*)\"/siU";

if (preg_match_all($regexp, $res, $matches)) {

						foreach($matches[1] as $key =>  $product_url) {
							//if ($key % 2) {
								//print($matches[2][$key] . ":::" . $product_url . "<br>");
									//echo("Found on Sub " . $product_url . "<br>");									
									ProcessCategoryPage(htmlspecialchars_decode($product_url), TRUE);
									
								
							//}
						}						
					}
					
}
 
 
 function IsPaged($page)
{
return strpos($page,"<b>1</b>");							
}

 function getAllProductsURL($res)
 {
 $regexp = "/<option value=\"(.*)\">100<\/option>/";
 if( preg_match_all($regexp, $res, $matches) ) {
			
			foreach($matches[1] as $key =>  $val) {
			return htmlspecialchars_decode(trim($val));
			
		}
	}
 }
 
 function ProcessCategoryPage($product_url,$fromSub)
 {
 
	
 //$product_url = "http://retail.pjhgroup.com/webapp/wcs/stores/servlet/SearchDisplay?sType=SimpleSearch&urlRequestType=Base&catalogId=10051&categoryId=13584&pageView=image&showResultsPage=true&urlLangId=-1&beginIndex=0&resultCatEntryType=&langId=-1&storeId=10001";
$res = get_content($product_url);

if ( strpos( $res, "<ul class=\"categories\">" ) != FALSE )
	{
		//echo("CATEGORIES FOUND<br>");
		ProcessTopLevelPage($res);
		return;
	}
	
if( IsPaged($res) )
	{
	$allProdURL = getAllProductsURL($res);
	//echo("All URL:" . $allProdURL);
	ProcessCategoryPage($allProdURL,$fromSub);
	}
	
$regexp = "/(?<=<div class=\"product-details\">)(.*)(?=<div class=\"compare-link\">)/siU";
	if( preg_match_all($regexp, $res, $matches) ) {
			
			foreach($matches[1] as $key =>  $val) {
			$product= trim($val);
			
// get price <div class="price offerprice bold"> XX </div>
$price = 0;

$regexp = "/(?<=<div  class=\"price offerprice bold\">)(.*)(?=<\/div>)/siU";
	if( preg_match_all($regexp, $product, $matches2) ) {
			
			foreach($matches2[1] as $key =>  $val) {
			$price= trim($val);
		}
	}

// get product code
$code = "";

$regexp = "/(?<=<span class=\"code\">)(.*)(?=<\/span>)/siU";
	if( preg_match_all($regexp, $product, $matches2) ) {
			
			foreach($matches2[1] as $key =>  $val) {
			$code= trim($val);
			
		}
	}
	
 $regexp = "/href=\"(http:\/\/retail.pjhgroup.com\/webapp\/wcs\/stores\/servlet\/ProductDisplay?.*)\"/siU";

 if (preg_match_all($regexp, $product, $matches)) {
						foreach($matches[1] as $key =>  $product_url) {
							//if ($key % 2) {
								//print("PRODUCT:" . $product_url . "<br>");
								//echo("Found product " . $product_url . " Price: " . $price . "<br>");
								DownloadProductURL($product_url, $price, $code);
							//}
						}						
					}
					
				}		
					
			}
			
			else
				{
				//echo("NOT FOUND PRODUCTS");
				}
					
}
					
function GetProductDetailsFromAjaxURL($id,&$description, &$offerPrice, &$partNumber)
{
$fields = "storeId=10001&langId=-1&catalogId=10051&productId=$id&onlyCatalogEntryPrice=true";

$productStr = post_content("http://retail.pjhgroup.com/webapp/wcs/stores/servlet/GetCatalogEntryDetailsByID",$fields,5);

$productStr = str_replace("/*","",$productStr);
$productStr = str_replace("*/","",$productStr);
$productStr = trim($productStr);

$product = json_decode($productStr);

$partNumber = $product->catalogEntry->catalogEntryIdentifier->externalIdentifier->partNumber;
$description = $product->catalogEntry->description[0]->name;
$offerPrice = $product->catalogEntry->offerPrice;


}
					
function DownloadProductURL($url, $price, $code)
{
//$url = "http://retail.pjhgroup.com/webapp/wcs/stores/servlet/ProductDisplay?urlRequestType=Base&catalogId=10051&categoryId=12590&productId=48211&errorViewName=ProductDisplayErrorView&urlLangId=-1&langId=-1&top_category=12551&parent_category_rn=12551&storeId=10001";
$url = "http://retail.pjhgroup.com/webapp/wcs/stores/servlet/ProductDisplay?urlRequestType=Base&catalogId=10051&categoryId=&productId=49596&errorViewName=ProductDisplayErrorView&urlLangId=-1&langId=-1&top_category=&parent_category_rn=&storeId=10001";

$product = get_content($url);
$multi = false;

$item['product_code'] = $code;
$item['url'] = $url;
	
if ( $code == "" ) 
{
$multi = true;
$codes_found = array();

// code wasn't on prevbious page due to multiple codes, extract and combine
	$regexp = "/(?<=Product code: )\s*(.*)(?=\s)/siU";

	if (preg_match_all($regexp, $product, $matches)) {

		foreach($matches[1] as $key =>  $code) {
		$code = trim($code);
		 if( !in_array($code, $codes_found) )
			{
			$codes_found[] = $code;			
			}
		}						
	}
	
	
	$item['product_code'] = implode($codes_found, ",");

}


// use regexp to extract data and write to mongo

$regexp = "/<div class=\"breadcrumb\">\s*<p>\s*(.*)\s*?<\/p>/siU" ;


																
								if( preg_match($regexp, $product, $arr) ) {		
							
									$breadcrumb = (trim($arr[0]));
									$regexp = "/<a.*>(.*)<\/a>/siU" ;	
									$item['nav'] = "";
									
											if (preg_match_all($regexp, $breadcrumb, $matches)) {
						foreach($matches[1] as $key =>  $crumb) {
							//if ($key % 2) {
								//print("NAV:" . $crumb . "<br>");
								$crumb = html_entity_decode($crumb,ENT_NOQUOTES,'UTF-8');
								$item['nav'] = $item['nav'] . ($crumb) . " | ";
							//}
						}						
					}

					
									}
									
$regexp = "/<h4 class=\"generalHC\">(.*)<\/h4>/siU" ;
								
if( preg_match($regexp, $product, $arr) ) {								
	$item['title'] = (trim($arr[1]));
	$item['title'] = html_entity_decode($item['title'],ENT_NOQUOTES,'UTF-8');
	//print($item['title']);
	}


// if has gallery images  then extract images else
$iImage = 1;
$regexp = "/largeimage: '(.*?)'/iU";
	
	if (preg_match_all($regexp, $product, $matches)) {
	foreach($matches[1] as $key =>  $name) {
	$name = trim($name);
	$name = str_replace("/thumbnail/","/zoom/",$name);
	$item['image_' . $iImage] = $name;
	echo($item['title'] . " " . $item['image_' . $iImage] . "\r\n");
	$iImage += 1;
	}						
}
else
{
	$regexp = "/<a href=\"(.*?)\" class=\"jqueryImageZoom\"/iU";
	
if( preg_match($regexp, $product, $arr) ) {		
						
	$img = trim($arr[1]);
	$img = str_replace("/thumbnail/","/zoom/",$img);
	$item['image_' . $iImage] = $img;
	
	echo($item['title'] . " " . $item['image_' . $iImage] . "\r\n");
	}
	
}

$regexp = "/<img id=\"productMainImage\".* src=\"(.*)\"/siU";
	
if( preg_match($regexp, $product, $arr) ) {								
	$item['image' . $iImage] = (trim($arr[1]));
	}
	
	
	$item['price']  = $price;
/*
	$regexp = "/<span class=\"price\">(.*)<\/span>/siU";
	
if( preg_match($regexp, $product, $arr) ) {								
	$item['price'] = (trim($arr[1]));
	//print($item['price']);
	}
else
		{
		$regexp = "/<h5>Prices from(.*)<\/h5>/siU";
			
		if( preg_match($regexp, $product, $arr) ) {								
			$item['price'] = (trim($arr[1]));
			//print($item['price']);
			}
		}
	*/
	
	$regexp = "/<div class=\"jquery-panes\">(.*?)<\/div>/si";
	
	if( preg_match($regexp, $product, $arr) ) {
			$desc = trim($arr[0]);
			$desc = str_replace("\t","",$desc);
			$desc = str_replace("\r\n\r\n\r\n\r\n\r\n\r\n","\r\n",$desc);
			$desc = str_replace("\r\n\r\n\r\n\r\n","\r\n",$desc);
			$desc = str_replace("\r\n\r\n","\r\n",$desc);
			$desc = str_replace("\r\n\r\n","\r\n",$desc);
			$desc = str_replace("\r\n\r\n","\r\n",$desc);
			$desc = str_replace("\r\n\r\n","\r\n",$desc);
			$desc = str_replace("\r\n\r\n","\r\n",$desc);
			$desc = str_replace("\r\n\r\n","\r\n",$desc);
			$desc = str_replace("\r\n\r\n","\r\n",$desc);
			$desc = str_replace("\r\n\r\n","\r\n",$desc);
			$desc = str_replace("\t"," ",$desc);
			$desc = str_replace("  "," ",$desc);
			$desc = str_replace("    "," ",$desc);
			$desc = str_replace("   "," ",$desc);
			$desc = str_replace("  "," ",$desc);
			
			$desc = str_replace("\r\n","\n",$desc);
			
			$item['html_desc'] = $desc;
			$desc = strip_tags($desc);
			$desc = html_entity_decode($desc,ENT_NOQUOTES,'UTF-8');
			$item['desc'] = $desc;
	//			print((trim($arr[1])));
			}


$regexp = "/\<div class=\"productDisplayPageTabsAttributeName\">\s*<p>\s*(.*):/siU";

$i = 0;
if (preg_match_all($regexp, $product, $matches)) {
	foreach($matches[1] as $key =>  $name) {
	$item['atts'][$i]['name'] = trim($name);
	$i += 1;
	}						
}

$regexp = "/\<div class=\"productDisplayPageTabsAttributeValue\">\s*<p>\s*(.*)<\/p>/siU";

$i = 0;

if (preg_match_all($regexp, $product, $matches)) {
	foreach($matches[1] as $key =>  $val) {
	$item['atts'][$i]['value'] = trim($val);	
	$i += 1;
	}						
}

$item['specification'] = "";
foreach( $item['atts'] as $att )
{
$item['specification'] .= html_entity_decode($att['name'],ENT_NOQUOTES,'UTF-8') . ": " . html_entity_decode($att['value'],ENT_NOQUOTES,'UTF-8') . "\r\n";
}

	// get download links
	$regexp = "/(?<=BEGIN CatalogAttachmentAssetsDisplay.jsp -->)(.*)(?=END CatalogAttachmentAssetsDisplay)/siU";
	if( preg_match_all($regexp, $product, $matches) ) {
			
			foreach($matches[1] as $key =>  $val) {
			$downloads = trim($val);
			
			$download = "";
			
			$regexp = "/<a\s[^>]*href=(\"??)([^\" >]*?)[^>]*>(.*)<\/a>/siU";
				if (preg_match_all($regexp, $downloads, $matches)) {
						foreach($matches[2] as $key =>  $val) {
					
					$download[$key]['url'] = trim($val);
					$download[$key]['title'] = trim($matches[3][$key]);
					if( isset($item[$download[$key]['title']]) )
						{
						$item[$download[$key]['title']] = $item[$download[$key]['title']] . "\n" . $download[$key]['url'] ;	
						}
					else
						{
						$item[$download[$key]['title']] = $download[$key]['url'] ;
						}
					
					}	
				
				}
				
			}		
			
			
	
	
	}	
		
		
		
			//	print( "\r\n\r\n" .  html_entity_decode($item['specification'],ENT_NOQUOTES,'UTF-8') . "\r\n\r\n" );	
		
		$conn = new Mongo('localhost');
		$db = $conn->odstech;
		$jtdb = $db->jtSpas_PJH;
		
		
		// get mutli products
		if ( $multi == true ) 
			{
			$regexp = "/\"catentry_id\" : \"([0-9]+)\"/siU";
			if (preg_match_all($regexp, $product, $matches)) {
				foreach($matches[1] as $key =>  $id) {
					GetProductDetailsFromAjaxURL($id,$description, $offerPrice, $partNumber);
					$item['title'] = $item['title'] = html_entity_decode($description,ENT_NOQUOTES,'UTF-8');
		
					$item['price'] = $offerPrice;
					$item['product_code'] = $partNumber;
					
					print("Sub" . $item['title'] . " " . $item['product_code'] . " " . $item['nav'] . "\r\n");
					
					if( isset($item['_id']) )
						{
						unset($item['_id']);
						}
						
					$jtdb->save($item);
					}
				}
				else	
					{
					print($item['title'] . " " . $item['product_code'] . " " . $item['nav'] . "\r\n");

					$jtdb->save($item);
					}
			}
		else
			{
			print("Main" . $item['title'] . " " . $item['product_code'] . " " . $item['nav'] . "\r\n");

			$jtdb->save($item);
			}
		

}
					
					
					
					
					