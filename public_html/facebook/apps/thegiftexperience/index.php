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

$signed = parse_signed_request($_POST['signed_request'], "dab79a06068c4600754fcae06e5b35f9");
if( $signed && $signed['page']['liked'] == 1 )
	{
	$liked = true;
	}
?>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js" type="text/javascript"></script>

<script type="text/javascript">
$(document).ready(function(){
$('#share_button').click(function(e){
e.preventDefault();
FB.ui(
{
method: 'feed',
name: 'This is the content of the "name" field.',
link: ' http://www.hyperarts.com/',
picture: 'http://www.hyperarts.com/external-xfbml/share-image.gif',
caption: 'This is the content of the "caption" field.',
description: 'This is the content of the "description" field, below the caption.',
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