	<?
error_reporting(E_ALL);
include('phpQuery/phpQuery.php');

$base_page = phpQuery::newDocumentFileHTML('http://www.bench-clothing.com/');
$data = pq('ul#nav li.level0', $base_page);
$_final = array();

foreach ($data as  $key1 => $li) { // men and women
	if ($key1 > 1) break;
	$li = pq($li);
	$cat_name = $li->find('a span')->text();
	$_final[$cat_name] = array();

	foreach ($li->find('.level0 > div') as $key2 => $subcat) { // subcats
		if ($key2 > 1) break;
		$subcat = pq($subcat);
		$subcat_name = $subcat->find('span.subtitle')->text();
		
		$_final[$cat_name][$subcat_name] = array();

		foreach ($subcat->find('ul li a') as $clothes_type) {
			$clothes_type = pq($clothes_type);
			$_clothes_page = phpQuery::newDocumentFileHTML($clothes_type->attr('href'));

			foreach (pq('ul.filter-dropdown',$_clothes_page) as $k => $color_filter) {
				if (!$k) continue;
				$color_filter = pq($color_filter);
				foreach ($color_filter->find('li') as $_k => $color_item) {
					if (!$_k) continue;
					$color_item = pq($color_item);
					$color_name = trim($color_item->text());
// print('<p>Color: '.trim($color_item->text()).'</p>');
					$_product_page = phpQuery::newDocumentFileHTML($color_item->attr('location'));

					// get products
					$product_boxes = $_product_page->find('div.category-products ul.products-grid li.item');
					if ($product_boxes->count()) {
						foreach ($product_boxes as $product_box) {
							$product_box = pq($product_box);
							$product_url = $product_box->find('h2.product-name a')->attr('href');
							$_product_detail_page = phpQuery::newDocumentFileHTML($product_url);
// die('die_here;'.$_product_detail_page->trigger('dom:loaded')->html());

							$product_name = $_product_detail_page->find('li.product strong')->text();
							
							$product_images = array();
							foreach ($_product_detail_page->find('div.thumbnails ul') as $ul) {
								$ul = pq($ul);
								foreach ($ul->find('li a div.image img') as $img) {
									$img = pq($img);
									$product_images[] = $img->attr('src');
								}
							}
							
							if (isset($_final[$cat_name][$subcat_name][$product_name])) {
								// add color
								$_final[$cat_name][$subcat_name][$product_name]['colors'][] = array(
									'name' => $color_name,
									'url' => $product_url
								);
								// add images by color
								$_final[$cat_name][$subcat_name][$product_name]['images'][$color_name] = $product_images;
								
							} else {
								foreach($_product_detail_page->find('div.product-main-info div.price-box span.price') as $price) {
									$price = pq($price);
									$product_price = trim($price->text());
									$product_price = substr($product_price,1);
									break;
								}
								
								$product_sku = $_product_detail_page->find('div.product-main-info p.product-ids')->html();
								
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
										foreach ($attr['options'] as $option) $product_sizes[] = $option['label'];
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
								
								$_final[$cat_name][$subcat_name][$product_name] = array(
									'name' => $product_name,
									'url' => $product_url,
									'category' => $cat_name,
									'subcategory' => $subcat_name,
									'colors' => array(
										array(
											'name' => $color_name,
											'url' => $product_url
										)
									),
									'price' => $product_price,
									'description' => $_product_detail_page->find('div.short-description')->html(),
									'images' => array($color_name => $product_images),
									'sizes' => $product_sizes,
								);
// print('<pre>'); print_r($_final); die;
// die('<pre>'.var_export($_final[$cat_name][$subcat_name][$product_name], true).'</pre>');
							}
						}
					}
					
				}
			}
		}
		
		// break; // @debug, skip all the rest until we make this functional
	}
	
	// break; // @debug, skip all the rest until we make this functional
}
print('<pre>');print_r($_final); die;
