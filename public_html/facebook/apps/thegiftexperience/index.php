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
<img src="liked.jpg">
<? } else { ?>
<img src="notliked.jpg">
<? } ?>

</body>
</html>