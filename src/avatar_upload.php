<?php
require_once 'UserClass.php';
header('Content-Type: application/json; charset=utf-8');
session_start();
if(isset($_FILES[0]) && $_SESSION['userObject']){
	$blob =base64_encode(file_get_contents($_FILES[0]['tmp_name']));
	if($_SESSION['userObject']->setAvatar($blob)){
		echo json_encode(array("blob" => "true"));
	} else {
		echo json_encode(array("blob" => "false"));
	}
	
}

exit()
?>