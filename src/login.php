<?php
include 'DBConnection.php';
include 'UserClass.php';
	//header('Content-Type: application/json; charset=utf-8');
	if(isset($_POST['username']) && isset($_POST['password'])){
		
		$con = new DBConnection();
		$uid = $con->verifyUser($_POST['username'],$_POST['password']);
		if ($uid > 0){
			session_start();
			$_SESSION["userObject"] = new User($uid);
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