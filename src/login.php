<?php
include 'DBConnection.php';
	//header('Content-Type: application/json; charset=utf-8');
	if(isset($_POST['username']) && isset($_POST['password'])){
		session_start();
		$con = new DBConnection();
		$uid = $con->verifyUser($_POST['username'],$_POST['password']);
		if ($uid > 0){
			$_SESSION['uid']=$uid;
			echo(json_encode(array('login' => 'true')));
		}
		elseif(isset($_SESSION['error_counter'])){
			$_SESSION['error_counter'] = $_SESSION['error_counter'] + 1;
			echo json_encode(array('login' => 'false'));
		} 
		else {
			$_SESSION['error_counter'] = 1;
			echo json_encode(array('login' => 'false'));
		}
	}
	exit();
?>