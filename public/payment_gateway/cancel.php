<?php

$dateCurrent = date("Y-m-d H:i:s");
$fp = fopen('hdfcLogCancel.txt', 'a+');
fwrite($fp, "============================$dateCurrent=========================\n\n");
fwrite($fp, "Response\n");
fwrite($fp, print_r($_POST, true));
fwrite($fp, "\r\n\r\n");
fclose($fp);
?>