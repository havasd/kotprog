<?php
header('Content-Type: application/json; charset=utf-8');
require_once 'DBConnection.php';
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&       strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
{
	$dbconn = new DBConnection();
	$results = Array();
	// form check
	$username_pattern = '/^[a-zA-ZáéiíoóöőuúüűÁÉIÍOÓÖŐUÚÜŰ0-9_]{5,20}$/';
	$password_pattern = '/^[a-zA-ZáéiíoóöőuúüűÁÉIÍOÓÖŐUÚÜŰ0-9_]{8,20}$/';
	$varchar64_pattern = '/^[a-zA-ZáéiíoóöőuúüűÁÉIÍOÓÖŐUÚÜŰ0-9_ ]{1,64}$/';
	if (!preg_match($username_pattern, $_POST['username'])){
		$results['username'] = "regex mismatch";
	}
	if ($dbconn->isUsernameTaken($_POST['username'])){
		$results['username'] = "taken username";
	}
	if ($_POST['password'] != $_POST['password_2']){
		$results['password'] = "different passwords";
	} 
	else if (!preg_match($password_pattern, $_POST['password'])){
		$results['password'] = "regex mismatch";
	}
	if (!preg_match($varchar64_pattern, $_POST['name'])){
		$results['name'] = "regex mismatch";
	}
	if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    	$results['email'] = "regex mismatch";
	}
	if (!preg_match($varchar64_pattern, $_POST['country'])){
		$results['country'] = "regex_mismatch";
	}
	if (!preg_match($varchar64_pattern, $_POST['city'])){
		$results['city'] = "regex_mismatch";
	}
	if(isset($_POST['avatar'])){$results['avatar'] = "valami post van";}

	if (empty($results)){
		$results['register'] = "true";
		$dbconn->register($_POST);
	}
	else {
		$results['register'] = "false";	
	}
	echo (json_encode($results));
}
else {
	echo (json_encode(array("ajax" =>"false")));
}
exit();
?>