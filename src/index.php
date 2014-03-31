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
		    	<div class="element">
		            <a class="dropdown-toggle">Fényképalbum</a>
		            <ul class="dropdown-menu" data-role="dropdown">
		                <li><a id="search">Keresés</a></li>
		                <li class="divider"></li>
		                <li><a id="dests">Legnépszerűbb úticélok</a></li>
		                <li class="divider"></li>
		                <li><a id="cities">Városok arcai</a></li>		                
		            </ul>
		        </div>
		    	<item class="element-divider"></item>
		    	
		    	<?php
		    	session_start();
		    	if (isset($_SESSION['sid']) && $_SESSION['sid'] == session_id()){
		    		echo "<div id=\"pics\" class=\"element\">
				            <a class=\"dropdown-toggle\">Képek</a>
				            <ul class=\"dropdown-menu\" data-role=\"dropdown\">
				                <li><a>Feltöltés</a></li>
				                <li class=\"divider\"></li>
				                <li><a target=\"change\">Módosítás</a></li>	                
				            </ul>
			        	</div>
				    	<item id=\"albums\" class=\"element\">Albumok</item>
				    	<item id=\"logout\" class=\"element place-right\">Kijelentkezés</item>
				    	<item id=\"userdata\" class=\"element place-right\">Személyes adatok</item>";
		    		
		    	} else {
				    echo "<item id=\"stats\" class=\"element\">Statisztika</item>
		    			 <item id=\"login\" class=\"element place-right\">Bejelentkezés</item>
		    			 <item id=\"register\" class=\"element place-right\">Regisztráció</item>";
		    	}
		    	?>
		    </nav>
		</nav>


		<div class="panel">
		    <div id="content-header"class="panel-header">
		        
		    </div>
		    <div id="content" class="panel-content">
		        
		    </div>
		</div>
    </body>
</html>