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
       
    $string = curl_exec ($ch);  
	
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
					
					
function DownloadProductURL($url, $price, $code)
{
//$url = "http://retail.pjhgroup.com/webapp/wcs/stores/servlet/ProductDisplay?urlRequestType=Base&catalogId=10051&categoryId=12590&productId=48211&errorViewName=ProductDisplayErrorView&urlLangId=-1&langId=-1&top_category=12551&parent_category_rn=12551&storeId=10001";
$product = get_content($url);

$item['product_code'] = $code;

if ( $code == "" ) 
{

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
								$item['nav'] = $item['nav'] . ($crumb) . " | ";
							//}
						}						
					}

					
									}
									
$regexp = "/<h4 class=\"generalHC\">(.*)<\/h4>/siU" ;
								
if( preg_match($regexp, $product, $arr) ) {								
	$item['title'] = (trim($arr[1]));
	//print($item['title']);
	}
	
	$regexp = "/<img id=\"productMainImage\".* src=\"(.*)\"/siU";
	
if( preg_match($regexp, $product, $arr) ) {								
	$item['image'] = (trim($arr[1]));
	//print($item['image']);
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
	
	$regexp = "/<div class=\"jquery-panes\">(.*)<\/div>/siU";
	
	if( preg_match($regexp, $product, $arr) ) {
			$desc = strip_tags(trim($arr[1]));
			$desc = str_replace("\t","",$desc);
			$desc = str_replace("\r\n\r\n","\r\n",$desc);
			$desc = str_replace("\r\n\r\n","\r\n",$desc);
			$desc = str_replace("\r\n\r\n","\r\n",$desc);
			$desc = str_replace("\r\n\r\n","\r\n",$desc);
			$desc = str_replace("\r\n\r\n","\r\n",$desc);
			$desc = str_replace("\r\n\r\n","\r\n",$desc);
			$desc = str_replace("\r\n\r\n","\r\n",$desc);
			$desc = html_entity_decode($desc);
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
$item['specification'] .= $att['name'] . ": " . $att['value'] . "\r\n";
}

	// get download links
	$regexp = "/(?<=BEGIN CatalogAttachmentAssetsDisplay.jsp -->)(.*)(?=END CatalogAttachmentAssetsDisplay)/siU";
	if( preg_match_all($regexp, $product, $matches) ) {
			
			foreach($matches[1] as $key =>  $val) {
			$downloads = trim($val);
			
			}		
					
			}
			
	$download = "";
			
	$regexp = "/<a\s[^>]*href=(\"??)([^\" >]*?)[^>]*>(.*)<\/a>/siU";
		if (preg_match_all($regexp, $downloads, $matches)) {
				foreach($matches[2] as $key =>  $val) {
			$download[$key]['url'] = trim($val);
			$download[$key]['title'] = trim($matches[3][$key]);
			$item[$download[$key]['title']] = $download[$key]['url'];
			}	
		
		}
		
		
		print($item['title'] . " " . $item['product_code'] . " " . $item['nav'] . "\r\n");
		print( "\r\n\r\n" .  html_entity_decode($item['specification']) . "\r\n\r\n" );	
		
		$conn = new Mongo('localhost');
		$db = $conn->odstech;
		$jtdb = $db->jtSpas_PJH;
		$jtdb->save($item);

}
					
					
					
					
					