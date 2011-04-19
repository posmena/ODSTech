<?php

class network_base implements network
{
	function __construct($local_file = null)
	{
	}
	
	function getFields()
	{
	}
	
	function getName()
	{
	}
	
	function getPrefix()
	{
	}
	
	function updateFeedList($network_id)
	{
	}
	
	function addFeed($feed)
	{
	}
	
	function parse_xml($file, $feed_id)
	{
	}
	
	function insert_products($products)
	{
		global $db;
		
		$query = 'INSERT INTO pm_products (id,feed_id,ProductName,ProductPrice,ProductDescription,SummaryDescription, Gender,BrandName,RRP,ProductID,AffiliateURL,ImageURL,Category,SmallImageURL,LargeImageURL) VALUES ';
		$sql = $query.$products;
		$sql = substr_replace($sql,";",-1);
		if ($db->changeQuery($sql)) {
			return true;
			//print $product['product_name']."<br />";
		}
		else
		{
			print "Sorry. There was an error. Please try again\n";
			return false;
		}
	}
}