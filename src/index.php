<?php
require_once 'model/User.php';
?>
<!DOCTYPE html>
<meta charset="utf-8">
<html>
    <head>
        <link href="css/metro-bootstrap.css" rel="stylesheet" >
        <link href="css/metro-bootstrap-responsive.css" rel="stylesheet">
	    <link href="css/iconFont.css" rel="stylesheet">

	    <link rel="stylesheet" href="css/ui-lightness/jquery-ui-1.10.4.custom.css">
  		<script src="js/jquery-1.10.2.js"></script>
  		<script src="js/jquery-ui-1.10.4.custom.js"></script>
        
	    <script src="js/jquery/jquery.widget.min.js"></script>
	    <script src="js/jquery/jquery.mousewheel.js"></script>
    	<script src="js/prettify/prettify.js"></script>

		<script src="js/metro.min.js"></script>
        <script src="controller.js"></script>

    </head>
    <body class="metro">
        <nav class="navigation-bar dark">
		    <nav class="navigation-bar-content">
		    	<button id="home_btn" class="element">Képnézegető</button>
		    	<item class="element-divider"></item>
		    	<button id="cities-btn" class="element">Városok arcai</button>
		    	<button id="favourite_destinations_btn" class="element">Legnépszerűbb úticélok</button>
		    	<button id="stats_btn" class="element">Statisztikák</button>
		    	<div class="element input-element">
		            <form>
		                <div class="input-control text">
		                	<div class="ui-widget">
		                    <input id="search" type="text" placeholder="Keresés...">
		                	</div>
		                    <button id="search-btn"class="btn-search"></button>
		                </div>
		            </form>
		        </div>
		    	<?php
		    	session_start();
		    	if (isset($_SESSION['userObject'])){
		    		echo "	<button id=\"logout_btn\" class=\"element place-right\">Kijelentkezés</button>
				    		<button id=\"userdata_btn\" class=\"element place-right\">Személyes adatok</button>
				    		<button id=\"mypictures_btn\" class=\"element place-right\">Saját fotók</button>";
		    		
		    	} else {
				    echo "	<button id=\"login_btn\" class=\"element place-right\">Bejelentkezés</button>
		    			 	<button id=\"register_btn\" class=\"element place-right\">Regisztráció</button>";
		    	}
		    	?>
		    </nav>
		</nav>


		<div class="panel">
		    <div id="content-header" class="panel-header">
		        <?php
		        if(isset($_SESSION['userObject'])){
		        	echo "Üdv ".$_SESSION['userObject']->getName()." !\n";
		        }
		        ?>
		    </div>
		    <div id="content" class="panel-content">
		        <?php
		        if(isset($_SESSION['userObject'])){
		        	echo $_SESSION['userObject']->toString();
		        }
		        ?>
		    </div>
		</div>
    </body>
</html>