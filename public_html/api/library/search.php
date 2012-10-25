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
	$books = null;

    // for the new version of the wsdl its required to provide a associate Tag
    // @see https://affiliate-program.amazon.com/gp/advertising/api/detail/api-changes.html?ie=UTF8&pf_rd_t=501&ref_=amb_link_83957571_2&pf_rd_m=ATVPDKIKX0DER&pf_rd_p=&pf_rd_s=assoc-center-1&pf_rd_r=&pf_rd_i=assoc-api-detail-2-v2
    // you can set it with the setter function or as the fourth paramameter of ther constructor above
    $amazonEcs->associateTag(AWS_ASSOCIATE_TAG);

    // Looking up multiple items
    $response = $amazonEcs->category('Books')->responseGroup('ItemAttributes,Images,Reviews,EditorialReview')->search($_GET['keyword']);
   // $response = $amazonEcs->category('Books')->responseGroup('Large,EditorialReview')->lookup($_GET['search']);

	//var_dump($response);
	//die();
	
	if( !isset($response->Items->Item) )
		{
		echo "";
		die();
		}
		
    $results = count($response->Items->Item);
		
	for( $i=0; $i < $results; $i++ )
		{
	
			
		$theitem = null;
		if( $results == 1 )
			{
			$theitem = $response->Items->Item;
			}
		else
			{
			$theitem = $response->Items->Item[$i];
			}
			
		$itm['title'] = $theitem->ItemAttributes->Title;
		
		if( isset($theitem->MediumImage) )
			{
			$itm['image'] = $theitem->MediumImage->URL;
			}
		else
			{
			$itm['image'] = "";
			}
			
		if( isset($theitem->SmallImage) )
			{
			$itm['thumbnail'] = $theitem->SmallImage->URL;
			}
		else
			{
			$itm['thumbnail'] = "";
			}
			
		
		$itm['price'] = $theitem->ItemAttributes->ListPrice->FormattedPrice;
		
		if( !isset($theitem->ItemAttributes->ISBN) )
		{
		continue;
		}
		
		$itm['ISBN'] = $theitem->ItemAttributes->ISBN;
		
		if( isset($theitem->ItemAttributes->Author) )
			{
		if( count($theitem->ItemAttributes->Author) > 1 )
			{
				$itm['author'] = $theitem->ItemAttributes->Author[0];
			}
			else
			{
				$itm['author'] = $theitem->ItemAttributes->Author;
			}
			}
		else
			{
			continue;
			$itm['author'] = "";
			}
			
		try
		{
		if( isset($theitem->EditorialReviews) )
			{
			$itm['description'] = $theitem->EditorialReviews->EditorialReview->Content;
			}
		}
		catch(Exception $e)
		{
		$itm['description'] = "";
		}
		
		if( $theitem->CustomerReviews->HasReviews )
			{
			$itm['reviews'] =  $theitem->CustomerReviews->IFrameURL;
			}

		$books[] = $itm;
		}
		
	echo(json_encode($books));
    //$response = $amazonEcs->responseGroup('Images')->lookup('B0017TZY5Y');
    //var_dump($response);

}
catch(Exception $e)
{
  echo $e->getMessage();
}
