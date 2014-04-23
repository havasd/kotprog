<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
$_SESSION=Array();
session_destroy();
$json=json_encode(array('logout' => 'true'));
echo $json;
exit();
?>