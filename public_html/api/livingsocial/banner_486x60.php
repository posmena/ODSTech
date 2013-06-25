<!DOCTYPE html>


<?php

function GetLink($lnk)
{
return "http://www.awin1.com/awclick.php?awinmid=3925&awinaffid=" . $_GET['aid'] . "&clickref=&p=" . urlencode($lnk);
}

function GetTimeLeft($dt)
	{
	$now = new DateTime("now");
	
	$interval = $now->diff($dt);
	$days = $interval->format('%a');
	$hours = $interval->format('%h');
	$mins = $interval->format('%i');
	
	if( $days <= 1 )
		{
		if( $hours <= 1 )
			{
			return $mins . " mins";
			}
		return $hours . " hours";
		}
		
	return $days . " days";
	
	}
	
	$conn = new Mongo('localhost');
	// access database
	$db = $conn->odstech;
	// access collection
		
	$coll = $db->p20_products;
	$start = new MongoDate();
	
	$products = $coll->find(array('feed_id' => 'livingsocial', 'Offers_ends' => array('$gte' => $start), 'Savings' => array ('$ne' => '')))->limit(1)->sort(array('Offers_end' => 1));
	
?>

<html>
<head>
    <title>Widget</title>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script type="text/javascript" src="Console.js"></script>
    <script type="text/javascript" src="WidgetRefresh.js"></script>
    <script type="text/javascript" src="jquery.dotdotdot-1.5.1.js"></script>
    
    
    <link rel="stylesheet" type="text/css" href="livingsocial_banner_486x60.css" />   


</head>
<body style="margin:0px; padding:0px; background-color:#262523;">
        




<div style="width:486px; height:60px; position:relative;">
    <div class="arrownav" style="position:absolute; right:0px; top:0px; z-index:100;">
		<a href="#" class="PreviousDealLink"><img class="previous" alt="previous" src="486x60/previous.gif" /></a><a href="#" class="NextDealLink"><img class="next" alt="next" src="486x60/next.gif" /></a>
    </div>



     <?php
	$i = 0;
	
	foreach($products as $product) {
	  $i++;
	?>
	
    <?php if ( $i == 1 ) {?>
    <div class="WidgetAd" style="display:block;">  
   	<?php } else {?>
    <div class="WidgetAd" style="display:none;">  
     	<?php } ?>

        
        <div class="widget">
		

            <div class="leftcol">
	            <div class="daysleft">
                    <p><a style="text-decoration:none; color:black;" href="<?php echo(GetLink($product['deeplink']));?>"><?php echo(GetTimeLeft(new DateTime($product['Offers_ends_at'])))?> left</a></p>
	            </div>
		        <div class="leftimg" style="width:60px; height:56px; border:0px solid red; overflow:hidden;">
			        <a href="<?php echo(GetLink($product['deeplink']));?>"><img title="" src="<?php echo($product['image_thumbnail'])?>" style="min-width:60px;" alt="" /></a>
		        </div>
		        <h3 class="discount" style="border:0px solid red; margin-top:8px;"><a style="text-decoration:none; color:#E7398E;" href="<?php echo(GetLink($product['deeplink']));?>"><?php echo($product['Savings'])?>  off</a></h3>
		        <div style="position:relative; font-weight:bold; left:70px; font-family:Arial; font-size:15px; color:white; display:block; text-overflow:ellipsis; height:17px; overflow:hidden; white-space:nowrap; border:0px solid red; width:220px;">
			        <a style="text-decoration:none; color:white;" href="<?php echo(GetLink($product['deeplink']));?>"><?php echo($product['offer_company'])?></a>
		        </div>
		        <div class="offer textoverflow" style="display:block; margin-top:4px; height:30px;">
			        <a style="text-decoration:none; color:white;" href="<?php echo(GetLink($product['deeplink']));?>"><?php echo($product['offer_subtitle'])?></a>
			    </div>

		        <div class="goArrowButton">
			        <a href="<?php echo(GetLink($product['deeplink']));?>"><img src="486x60/goArrowButton.jpg" alt="goArrowButton" width="27" height="54" /></a>
		        </div>
            </div>
		
				
            <div class="righcol">
	            <div class="logo">
		            <a href="<?php echo(GetLink($product['deeplink']));?>">
		                <img src="486x60/LivingSocial_logo.png" alt="LivingSocial_logo" width="51" height="20">
		            </a>
	            </div>

            </div>

        </div>


    </div>

     	<?php
		}
	?>
	
</div>





</body>
</html>
