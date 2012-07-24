<?php

function parse_signed_request($signed_request, $secret) {
  list($encoded_sig, $payload) = explode('.', $signed_request, 2); 

  // decode the data
  $sig = base64_url_decode($encoded_sig);
  $data = json_decode(base64_url_decode($payload), true);

  if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
    error_log('Unknown algorithm. Expected HMAC-SHA256');
    return null;
  }

  // check sig
  $expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
  if ($sig !== $expected_sig) {
    error_log('Bad Signed JSON signature!');
    return null;
  }

  return $data;
}

function base64_url_decode($input) {
  return base64_decode(strtr($input, '-_', '+/'));
}

$liked = false;

if( isset($_POST['signed_request']) )
	{
	$signed = parse_signed_request($_POST['signed_request'], "dab79a06068c4600754fcae06e5b35f9");
	if( $signed && $signed['page']['liked'] == 1 )
		{
		$liked = true;
		}
	}
?>

<html>
<body>
<style>

body
{
background: url("http://d26kxapmp05apn.cloudfront.net/images/polaroid.png") repeat scroll left top #FFFFFF;
font-family:Helvetica,Arial,sans-serif;
}

.productbox.itemcount4 {
    width: 586px;
}
.productbox.related {
    float: left;
}
.productbox {
    background: none repeat scroll 0 0 #F5F5F5;
    border: 0 none;
    float: left;
    margin: 0 0 20px 71px;
    padding: 20px;
}

.productbox i {
    color: #333333;
    display: block;
    font-family: 'Handlee',"HelveticaNeue","Helvetica Neue",Helvetica,Arial,sans-serif;
    font-size: 18px;
    font-style: normal;
    font-weight: normal;
    line-height: 110%;
    margin: 0 0 5px;
}

.productbox.itemcount5 .resultsItem {
    margin: 0 0 0 22px;
}
.productbox .resultsItem.first {
    margin-left: 0 !important;
}
.productbox .resultsItem {
    margin: 0 0 0 25px;
}
.productbox .resultsItem {
    width: 122px;
}
.resultsItem.first {
    clear: both;
    margin-left: 0;
}
.resultsItem {
    float: left;
    margin: 0 0 20px 17px;
    position: relative;
    width: 162px;
}

a {
    color: #4BAC38;
    text-decoration: none;
}

.resultsItem h2 {
    font-size: 12px;
    height: 45px;
    line-height: 120%;
    margin: 0;
    overflow: hidden;
}
h2 {
    color: #333333;
    font-size: 20px;
    line-height: 120%;
    margin: 0 0 10px;
}

.itemprice {
    color: #000000;
}

</style>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js" type="text/javascript"></script>

<script type="text/javascript">
$(document).ready(function(){
$('#share_button').click(function(e){
e.preventDefault();
FB.ui(
{
method: 'feed',
name: 'Special discount voucher for thegiftexperience.co.uk',
link: 'http://www.facebook.com/pages/ODST_co_uk/116457125039404?sk=app_461798647173464',
picture: 'http://www.odst.co.uk/facebook/apps/thegiftexperience/the_gift_experience.png',
caption: 'I have just got my voucher!',
description: 'You can claim yours by visiting the giftexperience.co.uk Facebook page.',
message: ''
});
});
});
</script>



<div id="fb-root"></div>
<script>
window.fbAsyncInit = function() {
FB.init({appId: '461798647173464', status: true, cookie: true,
xfbml: true});
};
(function() {
var e = document.createElement('script'); e.async = true;
e.src = document.location.protocol +
'//connect.facebook.net/en_US/all.js';
document.getElementById('fb-root').appendChild(e);
}());
</script>

<div id="share_button" style="position:absolute;width:180px;height:30px;top:235px;left:140px;cursor:pointer" ></div>

<?php if($liked) { ?>
<div id="main"><img src="likednew.jpg"></div>
<div id="code" style="position:absolute;top:197px;left:470px;color:#ffffff;letter-spacing:1.5px;font-size:24px;font-weight:bold;font-family:Arial">FB15842</div>
<div style="position: absolute; top: 240px; font-family: Arial; font-size: 19px; left: 430px;" id="activate"><a style="text-decoration: none; color: rgb(107, 142, 130);" href="http://track.webgains.com/click.html?wgcampaignid=106558&amp;wgprogramid=151&amp;wgtarget=http://www.thegiftexperience.co.uk/home/index:registercoupon?coupon_code=FB15842&amp;returnview=1">Click here to activate it!</a></div>
<div id="recentItemsContainer">        	                       
        <div class="productbox related itemcount4">
            <i>Our Favourite Gifts</i>
                                                <div class="resultsItem first">
                            
        <a class="imgthumb" title="Pair Of Engraved Cut Crystal Whisky Tumblers" href="http://track.webgains.com/click.html?wgcampaignid=106558&wgprogramid=151&wgtarget=http://www.thegiftexperience.co.uk/catalogue/1829/index.html?utm_source=website&amp;utm_medium=website&amp;utm_content=Home&amp;utm_campaign=related_items">
                            <img border="0" width="120" height="120" alt="Pair Of Engraved Cut Crystal Whisky Tumblers" src="http://d26kxapmp05apn.cloudfront.net/cms_media/images/120x120_fitbox-cystal_whisky_tumblers_a2.jpg">                        </a>
    <h2><a title="Pair Of Engraved Cut Crystal Whisky Tumblers" href="http://track.webgains.com/click.html?wgcampaignid=106558&wgprogramid=151&wgtarget=http://www.thegiftexperience.co.uk/catalogue/1829/index.html?utm_source=website&amp;utm_medium=website&amp;utm_content=Home&amp;utm_campaign=related_items">Pair Of Engraved Cut Crystal Whisky Tumblers</a></h2>   
    
				
					<strong class="itemprice"> &pound;34.90</strong>
					
			
                                            </div>
                
                                                <div class="resultsItem">
                            
        <a class="imgthumb" title="Shiny Silver Engraved Photo Frame" href="http://track.webgains.com/click.html?wgcampaignid=106558&wgprogramid=151&wgtarget=http://www.thegiftexperience.co.uk/catalogue/shiny_silver_engraved_photo_frame/index.html?utm_source=website&amp;utm_medium=website&amp;utm_content=Home&amp;utm_campaign=related_items">
                            <img border="0" width="120" height="120" alt="Shiny Silver Engraved Photo Frame" src="http://d26kxapmp05apn.cloudfront.net/cms_media/images/120x120_fitbox-silver_photo_frame_a6.jpg">                        </a>
    <h2><a title="Shiny Silver Engraved Photo Frame" href="http://track.webgains.com/click.html?wgcampaignid=106558&wgprogramid=151&wgtarget=http://www.thegiftexperience.co.uk/catalogue/shiny_silver_engraved_photo_frame/index.html?utm_source=website&amp;utm_medium=website&amp;utm_content=Home&amp;utm_campaign=related_items">Shiny Silver Engraved Photo Frame</a></h2>   
    
				
					<strong class="itemprice">from &pound;19.99</strong>
					
			
                                            </div>
                
                                                <div class="resultsItem">
                            
        <a class="imgthumb" title="The Day You Were Born" href="/catalogue/729/index.html?utm_source=website&amp;utm_medium=website&amp;utm_content=Home&amp;utm_campaign=related_items">
                            <img border="0" width="120" height="120" alt="The Day You Were Born" src="http://d26kxapmp05apn.cloudfront.net/cms_media/images/120x120_fitbox-the_day_you_were_born_aa.jpg">                        </a>
    <h2><a title="The Day You Were Born" href="/catalogue/729/index.html?utm_source=website&amp;utm_medium=website&amp;utm_content=Home&amp;utm_campaign=related_items">The Day You Were Born</a></h2>   
    
				
					<strong class="itemprice"> &pound;15.95</strong>
					
			
                                            </div>
                
                                                <div class="resultsItem">
                            
        <a class="imgthumb" title="Silver Plated Cufflinks" href="/catalogue/1521/index.html?utm_source=website&amp;utm_medium=website&amp;utm_content=Home&amp;utm_campaign=related_items">
                            <img border="0" width="120" height="120" alt="Silver Plated Cufflinks" src="http://d26kxapmp05apn.cloudfront.net/cms_media/images/120x120_fitbox-silver_plated_cufflinks_a.jpg">                        </a>
    <h2><a title="Silver Plated Cufflinks" href="/catalogue/1521/index.html?utm_source=website&amp;utm_medium=website&amp;utm_content=Home&amp;utm_campaign=related_items">Silver Plated Cufflinks</a></h2>   
    
				
					<strong class="itemprice"> &pound;19.99</strong>
					
			
                                            </div>
                                                               
                
           
                	</div>     

</div>

<? } else { ?>
<div id="main"><img src="notlikednew.jpg"></div>
<? } ?>



</body>
</html>