<?php
require_once('dal/DaoDB.php');
require_once('model/User.php');

	//header('Content-Type: application/json; charset=utf-8');
	if (isset($_POST['username']) && isset($_POST['password'])){
		$controller = new DaoDB();
		$uid = $controller->verifyUser($_POST['username'],$_POST['password']);
		if ($uid > 0){
			session_start();
			$_SESSION['userObject'] = $controller->getUserById($uid);
			echo(json_encode(array('login' => 'true')));
		}
		elseif (isset($_SESSION['error_counter'])){
			$_SESSION['error_counter'] = $_SESSION['error_counter'] + 1;
			echo json_encode(array('login' => 'false'));
		} 
		else {
			$_SESSION['error_counter'] = 1;
			echo json_encode(array('login' => 'false'));
		}
	} else {
		echo '	<style>
    				#login2 input {
        				width: 30em;
    				}
				</style>
				<form id="login2">
    				<label>Felhasználónév :</label>
    				<input id="login2_username" name="username" type="text" autofocus>

				    <label>Jelszó :</label>
    				<input id="login2_pwd" name="password" type="password">
    				<br>
    				<button id="submit_login">Bejelentkezés</button>
    				<div id="login_error"></div>
				</form>';
	}
?>
