<?php
include 'classes/feed_processing/class.cron_feed_manager.php';

set_time_limit(0);
$conn = new Mongo('localhost');
$db = $conn->odstech;


$feeds = $db->ot_feeds->find( array("active" => true, "classname" => 'goldenfeeds_feed' ) );
$feeds->immortal();

$i=0;
foreach( $feeds as $feed)
{
if( true )//$db->p20_products->findOne(array("feed_id" => $feed['client'])) == null )
	{
	print("Start");
	$count = ODSTech_FeedManager::process($feed['client'], true);
	print("Imported $count products");
	if( $count > 0 )
		{
		//die();
		}		
	echo("\n");
	}
}

return;
 
// TODO - use a merge map reduce after each feed is updated so counts are correct as soon as possible

/*
$db->p20_products->mapReduce( array( 
  'map' => '
    function () {
		emit(this.feed_id, {count:1});
	}' 
,'reduce' => '
function (key, values) {
    var result = {count:0};
    values.forEach(function (value) {result.count += value.count;});
    return result;
}') );
*/

?>