
<?php

$url "http://s.odst.co.uk";

if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
    || $_SERVER['SERVER_PORT'] == 443) {

    $url = "https://www.odst.co.uk";
}

$width = $_GET['w'];
$height = $_GET['h']; 
$id = $_GET['id'];
$aid = $_GET['aid'];
$cid = $_GET['cid'];
$r = rand();

$fw = $_GET['w'];
$fh = $_GET['h']; 

if( $width == 120 || $width == 160)
	{
		$fw = 220;
	}

	if( $height == 125 || $height == 90)
	{
		$fh = 274;
	}

	if( $width == 300 )
	{
		$fw = 322;
	}

?>

document.write('<!--[if lt IE 9]><style>.odst_main_div {overflow:hidden;}</style><![endif]-->');
document.write('<div class="odst_main_div" onmouseover="document.getElementById(\'odst_52652_<?php echo($r)?>\').width=<?php echo($fw)?>;document.getElementById(\'odst_52652_<?php echo($r)?>\').height=<?php echo($fh)?>" onmouseout="document.getElementById(\'odst_52652_<?php echo($r)?>\').width=\'100%\';document.getElementById(\'odst_52652_<?php echo($r)?>\').height=\'100%\'" style="z-index: 1; position: relative;width:<?php echo $width;?>px;height:<?php echo $height;?>px;"><iframe id="odst_52652_<?php echo($r)?>" allowtransparency="true" height="100%" width="100%" frameborder="0" border="0" marginwidth="0" marginheight="0" scrolling="no" src="<?php echo($url)?>/api/hilton/banner_<?php echo $width?>x<?php echo $height?>.php?aid=<?php echo $aid?>&cid=<?php echo $cid?>"></iframe></div>');
