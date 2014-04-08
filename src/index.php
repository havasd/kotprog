<?php
include 'UserClass.php';
?>
<!DOCTYPE html>
<meta charset="utf-8">
<html>
    <head>
        <link rel="stylesheet" href="../ext/metroui/css/metro-bootstrap.css">
        <script src="../ext/metroui/js/jquery/jquery.min.js"></script>
        <script src="../ext/metroui/js/jquery/jquery.widget.min.js"></script>
        <script src="../ext/metroui/min/metro.min.js"></script>
        <script src="controller.js"></script>
    </head>
    <body class="metro">
        <nav class="navigation-bar dark">
		    <nav class="navigation-bar-content">
		    	<button id="home" class="element">Képnézegető</button>
		    	<item class="element-divider"></item>
		    	<button id="cities" class="element">Városok arcai</button>
		    	<button id="favourite_destinations" class="element">Legnépszerűbb úticélok</button>
		    	<button id="stats" class="element">Statisztikák</button>
		    	<div class="element input-element">
		            <form>
		                <div class="input-control text">
		                    <input id="search" type="text" placeholder="Keresés...">
		                    <button class="btn-search"></button>
		                </div>
		            </form>
		        </div>
		    	<?php
		    	session_start();
		    	if (isset($_SESSION['userObject'])){
		    		echo "	<button id=\"logout\" class=\"element place-right\">Kijelentkezés</button>
				    		<button id=\"userdata\" class=\"element place-right\">Személyes adatok</button>
				    		<button id=\"mypictures\" class=\"element place-right\">Saját fotók</button>";
		    		
		    	} else {
				    echo "	<button id=\"login\" class=\"element place-right\">Bejelentkezés</button>
		    			 	<button id=\"register\" class=\"element place-right\">Regisztráció</button>";
		    	}
		    	?>
		    </nav>
		</nav>


		<div class="panel">
		    <div id="content-header"class="panel-header">
		        <?php
		        if(isset($_SESSION['userObject'])){
		        	echo "Üdv ".$_SESSION['userObject']->getName()." !\n";
		        }
		        ?>
		    </div>
		    <div id="content" class="panel-content">
		        
		    </div>
		</div>
    </body>
</html>