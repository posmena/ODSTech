<?php
/**
 * For a running Search Demo see: http://amazonecs.pixel-web.org
 */


if (is_file('sampleSettings.php'))
{
  include 'sampleSettings.php';
}

defined('AWS_API_KEY') or define('AWS_API_KEY', 'API KEY');
defined('AWS_API_SECRET_KEY') or define('AWS_API_SECRET_KEY', 'SECRET KEY');
defined('AWS_ASSOCIATE_TAG') or define('AWS_ASSOCIATE_TAG', 'ASSOCIATE TAG');

require 'AmazonECS.class.php';

try
{
    $amazonEcs = new AmazonECS(AWS_API_KEY, AWS_API_SECRET_KEY, 'co.uk', AWS_ASSOCIATE_TAG);

    // for the new version of the wsdl its required to provide a associate Tag
    // @see https://affiliate-program.amazon.com/gp/advertising/api/detail/api-changes.html?ie=UTF8&pf_rd_t=501&ref_=amb_link_83957571_2&pf_rd_m=ATVPDKIKX0DER&pf_rd_p=&pf_rd_s=assoc-center-1&pf_rd_r=&pf_rd_i=assoc-api-detail-2-v2
    // you can set it with the setter function or as the fourth paramameter of ther constructor above
    $amazonEcs->associateTag(AWS_ASSOCIATE_TAG);

    // Looking up multiple items
    $response = $amazonEcs->category('Books')->responseGroup('ItemAttributes,Images,Reviews,EditorialReview')->optionalParameters(array('SearchIndex' => 'Books', 'IdType' => 'ISBN'))->search($_GET['keyword']);
   // $response = $amazonEcs->category('Books')->responseGroup('Large,EditorialReview')->lookup($_GET['search']);

	
	$itm['title'] = $response->Items->Item->ItemAttributes->Title;
	$itm['image'] = $response->Items->Item->MediumImage->URL;
	$itm['price'] = $response->Items->Item->ItemAttributes->ListPrice->FormattedPrice;
	$itm['author'] = $response->Items->Item->ItemAttributes->Author;
	$itm['author'] = $response->Items->Item->EditorialReviews->EditorialReview->Content;
	
	if( $response->Items->Item->CustomerReviews->HasReviews )
		{
		$itm['reviews'] =  $response->Items->Item->CustomerReviews->IFrameURL;
		}

	echo(json_encode($itm));
    //$response = $amazonEcs->responseGroup('Images')->lookup('B0017TZY5Y');
    //var_dump($response);

}
catch(Exception $e)
{
  echo $e->getMessage();
}
