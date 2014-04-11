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
	<?php
			
			$tag = '<img src="data:image/jpeg;base64,' . $_SESSION['userObject']->getAvatar() .
	          '" width="250" height="250" /><br>';
	   		echo $tag;
		?>
	<form id="avatar" action="avatar_upload.php" method="POST">
      	<input type="file" name="avatar_file" id="avatar_file"/><br>
      	<input type="submit" id="avatar_submit_btn">
    </form>
</div>