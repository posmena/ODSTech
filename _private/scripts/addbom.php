<?php

$file_content = file_get_contents($argv[1]);
$file_content = "\xEF\xBB\xBF".$file_content;

file_put_contents($argv[1], $file_content);

?>