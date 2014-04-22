<?php
	header('Content-Type: application/json; charset=utf-8');
	require_once('dal/DaoDB.php');


	if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' and isset($_POST['username'])){
		$controller = new DaoDB();
		$results = Array();
		// form check
		$username_pattern = '/^[a-zA-ZáéiíoóöőuúüűÁÉIÍOÓÖŐUÚÜŰ0-9_]{5,20}$/';
		$password_pattern = '/^[a-zA-ZáéiíoóöőuúüűÁÉIÍOÓÖŐUÚÜŰ0-9_]{8,20}$/';
		$varchar64_pattern = '/^[a-zA-ZáéiíoóöőuúüűÁÉIÍOÓÖŐUÚÜŰ0-9_ ]{1,64}$/';
		if (!preg_match($username_pattern, $_POST['username'])){
			$results['username'] = "regex mismatch";
		}
		if ($controller->isUsernameTaken($_POST['username'])){
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
		if (isset($_POST['avatar'])){
			$results['avatar'] = "valami post van";
		}

		if (empty($results) and $controller->addUser($_POST)){
			$results['register'] = "true";
		} else {
			$results['register'] = "false";	
		}

		echo (json_encode($results));
	} else {
		echo(  '<style>
					#registration input {
						width: 30em;
					}
			  	</style>
				<form id="registration">
			    	<label>Felhasználónév : (minimum 6 karakter, csak betűk és számok)</label>
			    	<input name="username" type="text" required>
		            
		        	<label>Jelszó :</label>
		        	<input name="password" type="password" required>
			        
		        	<label>Jelszó megerősítése:</label>
		        	<input name="password_2" type="password" required>
			        
		        	<label>Név :</label>
		        	<input name="name" type="text" required>
		        	
		        	<label>E-mail cím :</label>
		        	<input name="email" type="text" required>
		        	
		        	<label>Ország :</label>
		        	<input name="country" type="text" required>
		        	
		        	<label>Város :</label>
		        	<input name="city" type="text" required>
		        	<br>
		        	<button id="submit_reg">Regisztráció</button>
		        	<div id="reg_error"></div>
				</form>');
	} 
?>