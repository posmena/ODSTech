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
    
    <link rel="stylesheet" type="text/css" href="livingsocial_banner_250x250.css" />   

</head>
<body style="margin:0px; padding:0px; background-color:#262523;">
        
          


<div style="width:248px; height:248px; position:relative; border:1px solid white;">

    <div style="position:absolute; top:0px; right:0px; z-index:100;">
        <a class="PreviousDealLink" style="cursor:pointer;"><img class="previous" src="250x250/previous.gif" /></a><a class="NextDealLink" style="cursor:pointer;"><img class="next" src="250x250/next.gif" /></a>
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
    
            <div class="widget" style="border:0px;">
                <div class="leftcol">
                    <div class="daysleft">
                        <p><a style="text-decoration:none; color:black;" href="<?php echo(GetLink($product['deeplink']));?>"><?php echo(GetTimeLeft(new DateTime($product['Offers_ends_at'])))?> Left</a></p>
                    </div>
                    <div class="mainImageContainer">
                        <a href="<?php echo(GetLink($product['deeplink']));?>"><img class="mainimg" style="min-width:60px" src="<?php echo($product['image_thumbnail'])?>" alt="Samsi" /></a>
                    </div>
                    <div style="display:none" class="socialemail">
                        <a class="socialemail2" href="/widgetshare/facebook?merchantName=Samsi&amp;dealTitle=Four-Course%20Japanese%20Tasting%20Menu%20for%20Two%20(%C2%A329)%2C%20Four%20(%C2%A356)%20or%20Six%20(%C2%A382)%20Including%20Jug%20of%20Sake&amp;shareLink=http%3A%2F%2Fwww.livingsocial.com%2Fcities%2F73-manchester%2Fdeals%2F634324-four-course-japanese-meal-for-two-with-jug-of-sake&amp;sidebarImageUrl=http%3A%2F%2Fa1.lscdn.net%2Fimgs%2F1a5f77c4-42d4-4e2d-b55f-ad8cc8dc682d%2F100_q60_.jpg"><img src="250x250/fb_logo.png" alt="Facebook logo" width="18" height="19" /></a>
                        <a class="socialemail2" href="/widgetshare/twitter?merchantName=Samsi&amp;dealTitle=Four-Course%20Japanese%20Tasting%20Menu%20for%20Two%20(%C2%A329)%2C%20Four%20(%C2%A356)%20or%20Six%20(%C2%A382)%20Including%20Jug%20of%20Sake&amp;shareLink=http%3A%2F%2Fwww.livingsocial.com%2Fcities%2F73-manchester%2Fdeals%2F634324-four-course-japanese-meal-for-two-with-jug-of-sake"><img src="250x250/twitter_logo.png" alt="Twitter logo" width="18" height="19" /></a>
                        <a class="socialemail2" href="/widgetshare/email?merchantName=Samsi&amp;dealTitle=Four-Course%20Japanese%20Tasting%20Menu%20for%20Two%20(%C2%A329)%2C%20Four%20(%C2%A356)%20or%20Six%20(%C2%A382)%20Including%20Jug%20of%20Sake&amp;shareLink=http%3A%2F%2Fwww.livingsocial.com%2Fcities%2F73-manchester%2Fdeals%2F634324-four-course-japanese-meal-for-two-with-jug-of-sake"><img class="emailicon" src="250x250/email_icon.png" alt="Email icon" width="18" height="19" /></a>
                    </div>
                </div>
                <div class="rightcol" style="border:0px solid red; padding-top:7px;">
                    <h3>
                        <span class="discount">
                            <a style="text-decoration:none; color:#E7398E;" href="<?php echo(GetLink($product['deeplink']));?>"><?php echo($product['Savings'])?> off</a>
                        </span>
                    </h3>
                    <div class="textoverflow" style="margin-left:5px; margin-right:5px; margin-top:5px; font-size:15px; font-weight:bold; max-height:60px; overflow:hidden;">
                        <a style="text-decoration:none; color:white;" href="<?php echo(GetLink($product['deeplink']));?>"><?php echo($product['offer_company'])?></a>
                    </div>
                    <p class="textoverflow" style="display:block; max-height:75px; border:0px solid orange; overflow:hidden;">
                        <a style="text-decoration:none; color:white;" href="<?php echo(GetLink($product['deeplink']));?>"><?php echo($product['offer_subtitle'])?></a>
                    </p>
                    <div class="cta" style="border:0px solid red; bottom:35px;">
                        <a href="<?php echo(GetLink($product['deeplink']));?>"><img src="250x250/view-deal.jpg" /></a>
                    </div>
                    <div class="logo" style="bottom:5px;">
                        <a href="<?php echo(GetLink($product['deeplink']));?>"><img src="250x250/LivingSocial_logo.png" alt="LivingSocial_logo" width="51" height="20" /></a>
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
