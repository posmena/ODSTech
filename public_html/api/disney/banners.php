<?php
$id = $_GET['id'];
$aid = $_GET['aid'];
$width = $_GET['w'];
$height = $_GET['h'];
$r = rand();
 
?>

document.write('<iframe width="<?php echo $width?>px" height="<?php echo $height?>px" frameborder="0" border="0" marginwidth="0" marginheight="0" scrolling="no" src="http://s.odst.co.uk/api/disney/<?php echo $id?>/<?php echo $width?>x<?php echo $height?>/banner_<?php echo $id?>_<?php echo $width?>x<?php echo $height?>.php?aid=<?php echo $aid?>"></iframe>');