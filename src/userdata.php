<?php
//user data modification
include 'UserClass.php';
session_start();

?>
<div id="#userdata">
	<form id="password_change">
		<label>Jelenlegi jelszó :</label>
		<input name="password_old" type="password">
		    
		<label>Új jelszó :</label>
		<input name="password_new" type="password">
		    
		<label>Új jelszó megerősítése:</label>
		<input name="password_new2" type="password">
		<br>
		<button id="password_change_submit">Jelszó módosítása</button>
	</form>
    <form id="personaldata_change"> 
	    <label>Név :</label>
	    <input name="name_new" type="text" placeholder="<?php echo $_SESSION['userObject']->getName() ;?>">
	    	
	    <label>E-mail cím :</label>
	    <input name="email_new" type="text" placeholder="<?php echo $_SESSION['userObject']->getEmail() ;?>">
	    	
	    <label>Ország :</label>
	    <input name="country_new" type="text" placeholder="<?php echo $_SESSION['userObject']->getCountry() ;?>">
	    	
	    <label>Város :</label>
	    <input name="city_new" type="text" placeholder="<?php echo $_SESSION['userObject']->getCity() ;?>">
	    <br>
	    <button id="personaldata_change_submit">Adatok módosítása</button>
	</form>
	    <div id="avatar">
	    	<label>Avatar :</label>
	    	<input type="file" id="avatar_upload">
	    	<br>
	    	<button id="avatar_upload_btn">Avatar feltöltése/módosítása</button>
	    </div>
</div>