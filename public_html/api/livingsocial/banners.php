<?php
$aid = $_GET['aid'];
$w = $_GET['w'];
$h = $_GET['h'];

echo("<iframe src='http://s.odst.co.uk/api/livingsocial/banner_$w"."x"."$h.php?id=$aid' width='$w' height='$h' frameBorder='0' scrolling='no'></iframe>");

?>