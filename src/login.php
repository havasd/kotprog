<?php
	header('Content-Type: application/json; charset=utf-8');
	if(isset($_POST['username']) && isset($_POST['password'])){
		if ($_POST['username']=="admin" && $_POST['password']=="admin"){
			$json=json_encode(array('login' => 'true'));
		} else {
			$json=json_encode(array('login' => 'false'));
		}
	}
	else {
		$json=json_encode(array('login' => 'false'));
	}
	echo $json;
	exit();
?>
