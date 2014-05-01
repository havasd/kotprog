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
				<form id="login2" style="width:30em">
                    <fieldset>
    				<label for="login2_username">Felhasználónév</label>
                    <div class="input-control text">
    				<input id="login2_username" name="username" type="text" autofocus>
                    <button class="btn-clear"></button>
                    </div>

				    <label for="login2_pwd">Jelszó</label>
                    <div class="input-control password">
    				    <input id="login2_pwd" name="password" type="password" required>
                    <button class="btn-reveal"></button></div>
    				<button id="submit_login">Bejelentkezés</button>
                    </fieldset>
                    </div>
				</form>';
	}
?>
