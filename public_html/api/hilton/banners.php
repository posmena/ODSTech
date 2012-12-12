
<?php

$width = $_GET['w'];
$height = $_GET['h']; 
$id = $_GET['id'];
$aid = $_GET['aid'];
$cid = $_GET['cid'];
$r = rand();
?>

document.write('<div style="z-index: 0; position: relative;width:<?php echo $width;?>px;height:<?php echo $height;?>px;"><iframe id="odst_52652" allowtransparency="true" width="<?php echo $width;?>" height="<?php echo $height;?>" frameborder="0" border="0" marginwidth="0" marginheight="0" scrolling="no" src="http://s.odst.co.uk/api/hilton/banner_<?php echo $width?>x<?php echo $height?>.php?aid=<?php echo $aid?>&cid=<?php echo $cid?>"></iframe></div>');
	
