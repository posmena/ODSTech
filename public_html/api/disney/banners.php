<?php
$id = $_GET['id'];
$aid = $_GET['aid'];
$r = rand();
 
?>

document.write('<iframe height="300px" width="250px" frameborder="0" border="0" marginwidth="0" marginheight="0" scrolling="no" src="http://s.odst.co.uk/api/disney/banner_<?php echo $id?>.php?aid=<?php echo $aid?>"></iframe>');