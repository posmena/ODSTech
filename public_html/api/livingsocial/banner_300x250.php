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
	
	$products = $coll->find(array('feed_id' => 'livingsocial', 'Savings' => array ('$ne' => '')))->limit(6)->sort(array('Offers_end' => 1));
	
?>

<html>
<head>
    <title>Widget</title>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script type="text/javascript" src="Console.js"></script>
    <script type="text/javascript" src="WidgetRefresh.js"></script>
    <script type="text/javascript" src="jquery.dotdotdot-1.5.1.js"></script>
    
    
    <link rel="stylesheet" type="text/css" href="livingsocial_banner_300x250.css" />   



</head>
<body style="margin:0px; padding:0px; background-color:#262523;">
        





<div style="width:298px; height:248px; position:relative; border:1px solid white;">

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
                        <p><a href="<?php echo(GetLink($product['deeplink']));?>" style="text-decoration:none; color:black;"><?php echo(GetTimeLeft(new DateTime($product['Offers_ends_at'])))?> left</a></p>
                    </div>
                    <div class="leftimg" style="width:166px; height:237px; overflow:hidden;">
                        <a href="<?php echo(GetLink($product['deeplink']));?>"><img src="<?php echo($product['image_thumbnail'])?>" alt="" /></a>
                    </div>

                    <div style="display:none" class="socialemail">
                        <a class="socialemail2" href="/widgetshare/facebook?merchantName=Samsi&amp;dealTitle=Four-Course%20Japanese%20Tasting%20Menu%20for%20Two%20(%C2%A329)%2C%20Four%20(%C2%A356)%20or%20Six%20(%C2%A382)%20Including%20Jug%20of%20Sake&amp;shareLink=http%3A%2F%2Fwww.livingsocial.com%2Fcities%2F73-manchester%2Fdeals%2F634324-four-course-japanese-meal-for-two-with-jug-of-sake&amp;sidebarImageUrl=http%3A%2F%2Fa1.lscdn.net%2Fimgs%2F1a5f77c4-42d4-4e2d-b55f-ad8cc8dc682d%2F100_q60_.jpg"><img src="250x250/fb_logo.png" alt="Facebook logo" width="18" height="19" /></a>
                        <a class="socialemail2" href="/widgetshare/twitter?merchantName=Samsi&amp;dealTitle=Four-Course%20Japanese%20Tasting%20Menu%20for%20Two%20(%C2%A329)%2C%20Four%20(%C2%A356)%20or%20Six%20(%C2%A382)%20Including%20Jug%20of%20Sake&amp;shareLink=http%3A%2F%2Fwww.livingsocial.com%2Fcities%2F73-manchester%2Fdeals%2F634324-four-course-japanese-meal-for-two-with-jug-of-sake"><img src="250x250/twitter_logo.png" alt="Twitter logo" width="18" height="19" /></a>
                        <a class="socialemail2" href="/widgetshare/email?merchantName=Samsi&amp;dealTitle=Four-Course%20Japanese%20Tasting%20Menu%20for%20Two%20(%C2%A329)%2C%20Four%20(%C2%A356)%20or%20Six%20(%C2%A382)%20Including%20Jug%20of%20Sake&amp;shareLink=http%3A%2F%2Fwww.livingsocial.com%2Fcities%2F73-manchester%2Fdeals%2F634324-four-course-japanese-meal-for-two-with-jug-of-sake"><img class="emailicon" src="250x250/email_icon.png" alt="Email icon" width="18" height="19" /></a>
                    </div>
                </div>
                <div class="rightcol">

                    <h3 class="discount" style="margin-top:10px;"><span><a href="<?php echo(GetLink($product['deeplink']));?>" style="text-decoration:none; color:#E7398E;"><?php echo($product['Savings'])?> off</a></span></h3>

                    <h3 class="textoverflow" style="border:0px solid red; display:block; line-height:17px; max-height:51px; overflow:hidden; margin-bottom:0px;">
                        <a href="<?php echo(GetLink($product['deeplink']));?>" style="text-decoration:none; color:white;"><?php echo($product['offer_company'])?></a>
                    </h3>

                    <p class="bodycopy textoverflow" style="display:block; margin-top:5px; margin-left:0px; margin-right:5px; border:0px solid orange; max-height:70px;">
                        <a href="<?php echo(GetLink($product['deeplink']));?>" style="text-decoration:none; color:white;"><?php echo($product['offer_subtitle'])?></a>
                    </p>

                    <div class="cta">
                        <a href="<?php echo(GetLink($product['deeplink']));?>"><img alt="ViewDeal" src="300x250/view-deal.jpg"></a>
                    </div>
                    <div class="logo">
                        <a href="<?php echo(GetLink($product['deeplink']));?>"><img src="300x250/LivingSocial_logo.png" alt="LivingSocial_logo" width="51" height="20"></a>
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
