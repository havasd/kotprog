<?php
	header('Content-Type: application/json; charset=utf-8');
	require_once('dal/DaoDB.php');


	if (isset($_POST['username'])){
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
	} 
	if(isset($_POST['getform'])) {
		echo  '<style>
					#registration input {
						width: 30em;
					}
			  	</style>
			  	
				<form id="registration" style="width:30em">
				<fieldset>
					<label for="t_user">Felhasználónév (minimum 6 karakter, csak betűk és számok)</label>
					<div class="input-control text">
			    		<input id="t_user" name="username" type="text" required>
		            <button class="btn-clear"></button>
                    </div>

                    <label for="t_pass">Jelszó</label>
		            <div class="input-control password">
		        		<input id="t_pass" name="password" type="password" required>
					<button class="btn-reveal"></button>
                    </div>

                    <label for="t_pass_2">Jelszó megerősítése</label>
			        <div class="input-control password">
		        		<input id="t_pass_2" name="password_2" type="password" required>
			        <button class="btn-reveal"></button>
                    </div>

					<label for="t_name">Név</label>
			        <div class="input-control text">
		        		<input id="t_name" name="name" type="text" required>
		        	<button class="btn-clear"></button>
                    </div>

                    <label for="t_email">E-mail cím</label>
		        	<div class="input-control text">
		        		<input id="t_email" name="email" type="text" required>
		        	<button class="btn-clear"></button>
                    </div>

                    <label for="t_country">Ország</label>
		        	<div class="input-control text">
		        		<div class="ui-widget">
		        		<input id="t_country" name="country" type="text" required>
		        		</div>
		        	<button class="btn-clear"></button>
                    </div>

                    <label for="t_city">Város</label>
		        	<div class="input-control text">
		        		<div class="ui-widget">
		        		<input id="t_city" name="city" type="text" required>
		        		</div>
					<button class="btn-clear"></button>
                    </div>

		        	<button id="submit_reg">Regisztráció</button>
		        	
		        	</fieldset>
				</form>';
				
	}
	

?>