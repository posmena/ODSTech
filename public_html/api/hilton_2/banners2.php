
<?php

$width = $_GET['w'];
$height = $_GET['h']; 
$id = $_GET['id'];
$aid = $_GET['aid'];
$cid = $_GET['cid'];
$r = rand();
?>

document.write('<!--[if lt IE 9]><style>.odst_main_div {overflow:hidden;}</style><![endif]-->');
document.write('<script>function odst_closed(){alert(1);}</script><div class="odst_main_div" onmouseover="document.getElementById(\'odst_52652_<?php echo($r)?>\').width=730;document.getElementById(\'odst_52652_<?php echo($r)?>\').height=700" onmouseout="document.getElementById(\'odst_52652_<?php echo($r)?>\').width=\'100%\';document.getElementById(\'odst_52652_<?php echo($r)?>\').height=\'100%\'" style="z-index: 0; position: relative;width:<?php echo $width;?>px;height:<?php echo $height;?>px;"><iframe id="odst_52652_<?php echo($r)?>" allowtransparency="true" height="100%" width="100%" frameborder="0" border="0" marginwidth="0" marginheight="0" scrolling="no" src="http://s.odst.co.uk/api/hilton/banner_<?php echo $width?>x<?php echo $height?>.php?aid=<?php echo $aid?>&cid=<?php echo $cid?>"></iframe></div>');
