<?php
//user data modification
	require_once('model/User.php');
	require_once('dal/DaoDB.php');
	session_start();
	if (!isset($_SESSION['userObject']))
		exit();

	$dal = new DaoDB();
	$mode = isset($_POST['mode']) ? $_POST['mode'] : '';
	if ($mode != '') {
		$res = array();
		$res['result'] = 'true';
		if ($mode == 'avatar'){
			if (isset($_FILES[0])){
				$blob = base64_encode(file_get_contents($_FILES[0]['tmp_name']));
				if ($dal->updateAvatar($blob)){
					$res['avatar'] = '<img src="data:image/jpeg;base64,' . $_SESSION['userObject']->getAvatar() . '" width="250" height="250" class="shadow"/>';
				} else {
					$res['result'] = 'false';
					$res['avatar'] = '';
				}
			}
		} else if ($mode == 'pwd') {
			$password_pattern = '/^[a-zA-ZáéiíoóöőuúüűÁÉIÍOÓÖŐUÚÜŰ0-9_]{8,20}$/';
			if ($dal->getUserPassword() != $_POST['password_old']) {
				$res['password'] = 4;
			} else if ($_POST['password_new'] != $_POST['password_new2']){
				$res['password'] = 1;
			} else if (!preg_match($password_pattern, $_POST['password_new'])){
				$res['password'] = 2;
			} else if (!$dal->updateUserPassword($_POST['password_new'])){
				$res['password'] = 3;
			}

			if (isset($res['password'])) {
				$res['result'] = 'false';
			}
		} else if ($mode == 'chg') {
			if (!$dal->updateUser($_POST)) {
				$res['result'] = 'false';
			}
		}
		echo json_encode($res);
		exit();
	}

	echo '<div class="grid">
                    <div class="span12">
                        <div class="row">
                            <div class="span3">
                            	<form id="f_password_change">
									<fieldset>
										<legend>Jelszó módosítás</legend>
										<div class="row input-control password">
											<input id="password_old" name="password_old" type="password" placeholder="Jelenlegi jelszó" required><button class="btn-reveal"></button></div>
										<div class="row input-control password">
											<input id="password_new" name="password_new" type="password" placeholder="Új jelszó" required><button class="btn-reveal"></button></div>
										<div class="row input-control password">
											<input id="password_new2" name="password_new2" type="password" placeholder="Új jelszó megerősítése" required><button class="btn-reveal"></button></div>
										<div class="row"><input type="submit" id="password_change_submit" value="Jelszó módosítása"></div>
									</fieldset>
								</form>
							</div>
                            <div class="span4 offset1">
                            	<form id="f_personaldata_change">
								    <fieldset>
								    <legend>Személyes adatok</legend>
								    <div class="row">
								    	<label for="name_new">Név</label>
										<div class="input-control text">
										<input id="name_new" name="name_new" required type="text" value="' . $_SESSION['userObject']->getName() . 
										'" data-orig="' . $_SESSION['userObject']->getName() . '">
										<button class="btn-clear"></button>
										</div>
									</div>
									<div class="row">
										<label for="email_new">E-mail</label>
									    <div class="input-control text">
									    <input id="email_new" name="email_new" required type="email" value="' . $_SESSION['userObject']->getEmail() . 
									    '" data-orig="' . $_SESSION['userObject']->getEmail() . '">
									    <button class="btn-clear"></button>
									    </div>
									</div>
									<div class="row">
										<label for="country_new">Ország</label>
										<div class="input-control text">
										<input id="country_new" name="country_new" required type="text" value="' . $_SESSION['userObject']->getCountry() . 
										'" data-orig="' . $_SESSION['userObject']->getCountry() . '">
										<button class="btn-clear"></button>
										</div>
									</div>
									<div class="row">
										<label for="city_new">Város</label>
									    <div class="input-control text">
									    <input id="city_new" name="city_new" type="text" required value="' . $_SESSION['userObject']->getCity() . 
									    '" data-orig="' . $_SESSION['userObject']->getCity() . '">
									    <button class="btn-clear"></button>
									    </div>
									</div>
									<div class="row">
									    <input type="submit" id="personaldata_change_submit" value="Adatok módosítása">
									</div>
									</fieldset>
								</form>
                            </div>
                            <div class="span3 offset1">
                            	<form id="f_avatar">
                            		<fieldset>
                            			<legend>Avatar</legend>
                            			<div id="d_avatar" class="row">';
                            			if (is_null($_SESSION['userObject']->getAvatar()))
                            				echo '<i class="icon-user shadow" style="width:250px; height:250px; font-size: 250px;"></i>';
                            			else
                            				echo '<img src="data:image/jpeg;base64,' . $_SESSION['userObject']->getAvatar() . '" width="250" height="250" class="shadow"/>';
										echo '</div>
										<div class="row">
											<div class="input-control file">
												<input type="file" name="avatar_file" id="avatar_file" accept="image/*"/>
												<button class="btn-file"></button>
											</div>
										</div>
										<div class="row">
											<input type="submit" id="avatar_submit_btn" value="Feltöltés">
										</div>
									</fieldset>
								</form>
                            </div>
                        </div>
                    </div>
            </div>
		</div>';
?>
