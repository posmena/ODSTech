<?php
error_reporting(E_ALL);
include('phpQuery/phpQuery.php');

mb_internal_encoding("UTF-8");
	

$sites = array(
	'de' => array(  'baseUrl' => 'http://www.benchstore.de',
					'homeUrl' => 'http://www.benchstore.de/?bench_b2c_ignoregeoip=1',
					'saleUrl' => 'http://www.benchstore.de/sale/?bench_b2c_ignoregeoip=1',
					'currency' => 'EUR',
					'free_delivery' => 40,
					'delivery_charge' => '2.90'),
					
	'uk' => array( 'baseUrl' => 'http://www.bench.co.uk',
					'homeUrl' => 'http://www.bench.co.uk/', 
					'saleUrl' => 'http://www.bench.co.uk/sale',
					'currency' => 'GBP',
					'free_delivery' => 30,
					'delivery_charge' => '1.99')
);

$conn = new Mongo('localhost');
$db = $conn->odstech;
$db->dump_bench->drop();
$db->dump_google_bench->drop();
$db->dump_bench_idealio->drop();

$site = "uk";
if( isset($argv[1]) )
	{
	$site = $argv[1];
	}
	
$theSite = $sites[$site];


$base_page = phpQuery::newDocumentFileHTML($theSite['homeUrl']);
$data = pq('ul#nav li.level0', $base_page);
$result = array();
$_final = array();

foreach ($data as  $key1 => $li) { // men and women
	//if ($key1 > 1) break;
	$li = pq($li);
	$cat_name = $li->find('a span')->text();
	$_final[$cat_name] = array();
    echo($cat_name."\n");
	$subcats = array();
	foreach ($li->find('.level0 > div') as $key2 => $subcat) { // subcats
		if ($key2 > 1) continue;
		$subcat = pq($subcat);
		$subcat_name = $subcat->find('span:first.subtitle')->text();
		echo("\t".$subcat_name."\n");
		$_final[$cat_name][$subcat_name] = array();

		$clothes_types = array();
		foreach ($subcat->find('ul:first li a') as $clothes_type) {
			$clothes_type = pq($clothes_type);
			echo("\t\t".$clothes_type->text()."\n");
				
			$_clothes_page = phpQuery::newDocumentFileHTML(CheckURL($clothes_type->attr('href') . '?bench_b2c_ignoregeoip=1',$theSite));
			
			foreach (pq('ul.filter-dropdown',$_clothes_page) as $k => $color_filter) {
				//if (!$k) continue;
				$color_filter = pq($color_filter);
				foreach ($color_filter->find('li') as $_k => $color_item) {
					
					//if (!$_k) continue;
					$color_item = pq($color_item);
					$color_name = trim($color_item->text());
				
					if( strpos($color_item->attr('location'),'color' ) )
					{					
 //print('<p>Color: '.trim($color_item->text()).'</p>');
 	
					GetProducts($color_item->attr('location'),$cat_name,$subcat_name,$clothes_type->text(),$color_name,$theSite);
				}
			}
		  }
		}
// die('<pre>'.var_export($clothes_types,true).'</pre>');
		
		//break; // @debug, skip all the rest until we make this functional
	}
	

//	break; // @debug, skip all the rest until we make this functional

}

GetProducts($theSite['saleUrl'],'Sale','','','',$theSite);


//print('<pre>');print_r($_final); die;
// die('<pre>'.var_export($result, true).'</pre>');
//mongoexport -d odstech -c dump_bench --csv -f '_id','name','price','category','description','sizes','image1','image2','color','url' -o bench.csv

function CheckURL($url, $theSite)
{
if( strpos( $url,"http" ) === false )
	{
	$theUrl =  $theSite['baseUrl'] . $url;
	}
else
	{
	$theUrl = $url;
	}

return $theUrl;

}


function GetProducts($url,$cat_name,$subcat_name,$clothes_type,$color_name,$theSite)
{
echo("\n\nProducts at:" . $url . "\n");
$conn = new Mongo('localhost');
$db = $conn->odstech;
$products = $db->dump_bench;
$products_idealio = $db->dump_bench_idealio;
	
	$_product_page = phpQuery::newDocumentFileHTML($url);
	//echo($color_item->attr('location'));
					// get products
					$product_boxes = $_product_page->find('div.category-products ul.products-grid li.item');
					if ($product_boxes->count()) {
						foreach ($product_boxes as $product_box) {
							$product_box = pq($product_box);
							$product_url = $product_box->find('h2.product-name a')->attr('href');
							//$product_url = 'http://www.bench.co.uk/men/polo-shirts/expolo-b-white';
							$_product_detail_page = phpQuery::newDocumentFileHTML($product_url . "/?bench_b2c_ignoregeoip=1",'UTF-8');
// die('die_here;'.$_product_detail_page->trigger('dom:loaded')->html());

							$product_name = $_product_detail_page->find('li.product strong')->text();
							$product_name = html_entity_decode($product_name);
							
							$product_images = array();
							foreach ($_product_detail_page->find('div.thumbnails ul') as $ul) {
								$ul = pq($ul);
								foreach ($ul->find('li a div.image img') as $img) {
									$img = pq($img);
									$product_images[] = $img->attr('src');
								}
							}
							
							/*if (isset($_final[$cat_name][$subcat_name][$product_name])) {
								// add color
								$_final[$cat_name][$subcat_name][$product_name]['colors'][] = array(
									'name' => $color_name,
									'url' => $product_url
								);
								// add images by color
								$_final[$cat_name][$subcat_name][$product_name]['images'][$color_name] = $product_images;
								
							} else {*/
								$product_price = "";
								$old_price = "";
								
								foreach($_product_detail_page->find('div.product-main-info div.price-box span.price') as $price) {
									$price = pq($price);
									$old_price = $product_price;
																		
									$product_price = trim($price->text());
									$product_price  = str_replace(chr(0xC2).chr(0xA0),"",$product_price); // no break space
									$product_price  = str_replace(chr(0xE2).chr(0x82).chr(0xAC),"",$product_price ); //euro
									
									$product_price = str_replace(',','.',$product_price);									
									
									$product_price = utf8_decode($product_price);
									$product_price = str_replace('£','',$product_price);									
									$product_price = str_replace('€','',$product_price);	
									$product_price = str_replace('?','',$product_price);
																			
								}
								
								$product_sku = $_product_detail_page->find('div.product-main-info p.product-ids')->html();
								$product_sku = str_replace('SKU# ','',$product_sku);
								
								// get sizes  from json
								$scripts = $_product_detail_page->find('script');
								$script = pq($scripts->get(48));
								$script_text = $script->text();
								if (strpos($script_text, 'spConfig') === false) { // look for it
									$found = false;
									foreach ($scripts as $k => $script) {
										$script = pq($script);
										$script_text = $script->text();
										if (strpos($script->text(), 'spConfig') !== false) { // this is it
											$found = true;
											break;
										}
									}
								} else {$found = true;}

								if ($found) { // we've got the right script
									$product_sizes = array();
									$json_text = str_replace(');', '', substr($script_text, 43));
									$json_data = json_decode($json_text, true);
									foreach ($json_data['attributes'] as $attr) {
										foreach ($attr['options'] as $option) 
											if( $option['label'] != "." && $option['label'] != "" && $option['label'] != "----" )
											    $product_sizes[] = $option['label'];											
									}
								// print('<pre>'); print_r($product_sizes); die;
								} else {$product_sizes = false;}
								
								$product_images = array();
								foreach ($_product_detail_page->find('div.thumbnails ul') as $ul) {
									$ul = pq($ul);
									foreach ($ul->find('li a div.image img') as $img) {
										$img = pq($img);
										$product_images[] = $img->attr('src');
									}
								}
								$desc = $_product_detail_page->find('div.shortdescpr')->html();
								
								$desc = str_replace("<br/>",' ',$desc);
								$desc = str_replace("<br>",' ',$desc);
								$desc = strip_tags($desc);
								$desc = str_replace("\r",' ',$desc);
								$desc = str_replace("\n",' ',$desc);
								$desc = str_replace('Kurz' . chr(0xC3) . chr(0xBC) . 'bersicht','',$desc);
								$desc = str_replace('Quick Overview','',$desc);
								$desc = str_replace('    ',' ',$desc);
								$desc = str_replace('_','',$desc);
								$desc = str_replace($product_sku,'',$desc);
								$pos = strpos($desc, 'Item Code');
								
								if( $pos !== false )
									{
									$desc = substr($desc,0,$pos);
									}
								$desc = trim($desc);
								
								$gender = '';
								
								if ( $cat_name == "Men" || $cat_name == "MENS" || $cat_name == "HERREN" || $subcat_name == "Men")
									$gender = 'Male';										
							
						
								if( strpos( $product_url, 'boys' ) || strpos( $product_url, 'jungen' ))									
									$gender = 'Male';									
								
								if($cat_name == 'Sale' && strpos($product_box->parent()->parent()->parent()->text(),'MEN'))
									$gender = 'Male';
																	
								
								if ( $cat_name == "Women" || $cat_name == "WOMENS" || $cat_name == "DAMEN" || $subcat_name == "Women")
									$gender = 'Female';	
									
								if( strpos( $product_url, 'girls') || strpos( $product_url, 'maedchen'))
									$gender = 'Female';
								
								if($cat_name == 'Sale' && strpos($product_box->parent()->parent()->parent()->text(),'WOMEN'))
									$gender = 'Female';
								
								
// get current products from db and append color if not in list of colors
// add each color / size variation as mew row to google_bench
								$product_images_1 = "";
								$product_images_2 = "";
								
								if( count($product_images) )
									$product_images_1 = $product_images[0];
									
								if( count($product_images) > 1 )
									$product_images_2 = $product_images[1];
									
								$product_sizes_str	= "";
								if( is_array($product_sizes) )
									$product_sizes_str = implode(",",$product_sizes);
								
								$shipping = $theSite['delivery_charge'];
								
								if( $product_price > $theSite['free_delivery'] )
									{
									$shipping = 0;
									}
									
								$product = array(
									'category' => $cat_name . ' > ' . $subcat_name . ' > ' . $clothes_type,
									'_id' => $product_sku, // plus size and color for sep rows
									'id' => $product_sku, // plus size and color for sep rows
									'sku' => $product_sku, // plus size and color for sep rows
									'title' => $product_name,
									'item_group_id' => '$product_sku',
									'old_price' => $old_price,
									'link' => $product_url,
									'price' => $product_price,
									'description' => $desc,
									'images' => $product_images,
									'sizes' => $product_sizes_str,
									'image_link' => $product_images_1,
									'additional_image_link' => $product_images_2,
									'shipping_cost_uk' => $shipping,
									'shipping' => $shipping,
									'condition' => 'new',
									'availability' => 'in stock',
									'brand' => 'Bench',
									'gender' => $gender,
									'currency' => $theSite['currency']
								);
								
								if( $color_name != "" )
									{
									$product['color'] = $color_name;													
									}
									
								if($cat_name == 'Sale' )
									{
									$curCat = GetCategory($product_name);
									if( $curCat != "" )
										{
										$product['category'] = $curCat;
										}
									else
										{
										$product['category'] = 'Sale';
										}
									}
										
								$product['availability'] = 'in stock';
								CreateGoogleProducts($product, $product_sizes);
								
								$product['link'] = $product['link'].'?utm_source=Feed&utm_campaign=ODST&utm_medium=NSC_Affiliates';
								if( $product['title'] != "" )
									{
									$products->save($product);																					
									$product['title'] = trim($product['title'] . ' ' . $product['color']);
									
									$existingProduct = $products_idealio->findOne(array('title' => $product['title']));

									if( $existingProduct )
										{									
										$product['_id'] = $existingProduct['_id'];		
										}
									
									$products_idealio->save($product);
									
									}
									
								echo($product_name."\n");
								
								}
						//}
					}
					
				
}

function GetCategory($name)
{
$conn = new Mongo('localhost');
$db = $conn->odstech;
$products = $db->dump_bench;

$product = $products->findOne(array('title' => $name, 'category' => array('$ne' => 'Sale')));

if( $product )
	{
	return $product['category'];
	}
	
return "";
}

function AddAllColours($name, $thisColour)
{
$conn = new Mongo('localhost');
$db = $conn->odstech;
$products = $db->dump_bench;

$product = $products->findOne(array('title' => $name));
$exists = false;

if( $product )
	{
	$colours = explode(",",$product['colors']);
	foreach( $colours as $colour )
		{
		if( $colour == $thisColour )
			{
			$exists = true;
			}
		}
	}
else
	{
	return $thisColour;
	}
	
	if( $exists )
		{
		return implode(",",$colours);
		}
	else
		{
		return implode(",",$colours) . "," . $thisColour;
		}
		
}

function CreateGoogleProducts($product, $product_sizes)
{

$conn = new Mongo('localhost');
$db = $conn->odstech;
$google_products = $db->dump_google_bench;
$url = $product['link'];
$product['link'] = $url .'?utm_source=Feed&utm_campaign=ODST&utm_medium=GoogleShopping';
								
if( $product['old_price'] != "" )
	{
	$product['sale_price'] = $product['price'];
	$product['price'] = $product['old_price'];
	if( $product['price'] == $product['sale_price'] ) 
		{
		unset($product['sale_price']);
		}
	}
	
// if product with name already exists then get the group_id
$existingGroup = $google_products->findOne(array('title' => $product['title']));

if( $existingGroup )
	{
	$product['item_group_id'] = $existingGroup['item_group_id'];
	}
else
	{
	$product['item_group_id'] = $product['_id'];		
	}

if( is_array($product_sizes) && count($product_sizes) )
	{
	foreach ( $product_sizes as $size ) 
		{
		$product['id'] = $product['sku'] . $size;
		$product['_id'] = $product['id'];
		$product['mpn'] = $product['id'];
		$product['size'] = $size;
		$product['google_product_category'] = "Clothing &amp; Accessories > Clothing";
		
		if( $product['title'] != "" )
		  $google_products->save($product);	
		}
	}
else
	{
	if( $product['title'] != "" )
     	$google_products->save($product);								
	}
	
$product['link'] = $url;
}
