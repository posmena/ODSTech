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
	
	$products = $coll->find(array('feed_id' => 'livingsocial', 'Offers_ends' => array('$gte' => $start), 'Savings' => array ('$ne' => '')))->limit(6)->sort(array('Offers_end' => 1));
	
?>

<html>
<head>
    <title>Widget</title>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script type="text/javascript" src="Console.js"></script>
    <script type="text/javascript" src="WidgetRefresh.js"></script>
    <script type="text/javascript" src="jquery.dotdotdot-1.5.1.js"></script>
    
    
    
    <link rel="stylesheet" type="text/css" href="livingsocial_banner_120x600.css" />   



</head>
<body style="margin:0px; padding:0px; background-color:#262523;">
        






<div style="position:relative; width:120px; height:600px;">

    <div class="arrownav" style="position:absolute; top:0px; right:0px; z-index:100;">
        <a style="cursor:pointer;" class="PreviousDealLink"><img class="previous" alt="previous" src="120x600/previous.gif"></a><a style="cursor:pointer;" class="NextDealLink"><img class="next" alt="next" src="120x600/next.gif"></a>
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
        
       
		<div class="widget" style="padding-top:25px;">
		
            <div class="topcol">
                <div class="mainimg" style="width:113px; height:215px; overflow:hidden; position:relative; border:0px solid red;">
                    <a href="<?php echo(GetLink($product['deeplink']));?>" style="display:block;"><img src="<?php echo($product['image_thumbnail'])?>" alt="mainImg" style="min-width:60px; position:absolute; left:-15px;"></a>
                </div>

                <div class="daysleft">
                    <p>
                        <a style="text-decoration:none; color:black;" href="<?php echo(GetLink($product['deeplink']));?>"><?php echo(GetTimeLeft(new DateTime($product['Offers_ends_at'])))?> <?php echo(GetTimeLeft(new DateTime($product['Offers_ends_at'])))?> Left</a>
                    </p>
                </div>

                <div class="offer">
                    <h3 class="discount">
                        <a style="text-decoration:none; color:#E7398E;" href="<?php echo(GetLink($product['deeplink']));?>"><?php echo($product['Savings'])?> off</a>
                    </h3>

                    <h3>
                        <a style="text-decoration:none; color:white;" href="<?php echo(GetLink($product['deeplink']));?>"><?php echo($product['offer_company'])?></a>
                    </h3>

                    <p style="display:block; border:0px solid red;">
                        <a style="text-decoration:none; color:white;" href="<?php echo(GetLink($product['deeplink']));?>"><?php echo($product['offer_subtitle'])?></a>
                    </p>
                </div>
            </div>

            <div class="cta">
                <a href="<?php echo(GetLink($product['deeplink']));?>"><img alt="ViewDeal" src="120x600/view-deal.jpg"></a>
            </div>

            <div class="logo">
                <a href="<?php echo(GetLink($product['deeplink']));?>"><img src="120x600/LivingSocial_logo.png" alt="LivingSocial_logo" width="51" height="20"></a>
            </div>
        </div>
        
        
        
          
    </div>
	<?php
		}
	?>
   

</div>

</body>
</html>
