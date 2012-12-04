
<?php

$width = $_GET['w'];
$height = $_GET['h']; 
$id = $_GET['id'];
$affiliate_id = $_GET['aid'];
$campaign_id = $_GET['cid'];
$r = rand();
?>


document.write('<div style="z-index: 0; position: relative;width:' + <?php echo $width;?> + 'px;height:' + <?php echo $width;?> + 'px;"><iframe width="460" height="600" frameborder="0" border="0" marginwidth="0" marginheight="0" scrolling="no" src="http://s.odst.co.uk/api/hilton/banner_<?php echo $width?>x<?php echo $height?>.php?aid=' + $affiliate_id + '&cid=' + &campaign_id + '"></iframe></div>');
	
