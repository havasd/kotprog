<style>
#picture_zoom {
}
#picture{
	position: relative;
	top: -40px;
	left: 0px;
	height: 600px;
	width: 700px;
	display:-moz-box;
			-moz-box-pack:center;
			-moz-box-align:center;

	display:-webkit-box;
			-webkit-box-pack:center;
			-webkit-box-align:center;

	display:box; 
			box-pack:center;
			box-align:center;
}
#picture img{
	display: block;
	vertical-align: middle;

  	margin-left: auto;
  	margin-right: auto;
	height: auto;
	width: auto;
	max-width: 700px;
	max-height: 600px;
}
#info{
	position: relative;
	top:-600px;
	left: 710px;
}
</style>
<div id="picture_zoom">
	<div id="picture">
		<?php

			if(isset($_POST['img'])){
				echo "<img src=\"".$_POST['img']."\">";
			}
			else {
				echo "post error";
			}
		?>
	</div>
	<div id="info">
		<div id="picture_data" class="panel">
		    <div id="picture_title" class="panel-header bg-lightBlue fg-white">
		        Eiffel-torony
		    </div>
		    <div id="picture_info" class="panel-content">
		        Készült: 2013.04.20<br>
		        Készítette: x y<br>
		        Helyszín: Párizs, Eiffel-torony<br>
		        Feltöltés ideje: 2014.04.01 22:26
		    </div>
		</div>
		<div class="panel">
			<div class="panel-header bg-lightBlue fg-white">
		        Értékelés
		    </div>
		    <div class="panel-content">
		    	<div class="rating">
				    <ul>
				        <li class="rated"></li>
				        <li class="rated"></li>
				        <li></li>
				        <li></li>
				        <li></li>
				    </ul>
				    <span class="score-hint"></span>
				</div>
			</div>
		<div id="comments_panel" class="panel">
		    <div id="comments_title" class="panel-header bg-lightBlue fg-white">
		        Hozzászólások
		    </div>
		    <div  id="comments" class="panel-content">
			    <div class="listview-outlook" data-role="listview">
	                <a class="list marked" href="#">
	                    <div class="list-content">
	                        <span class="list-title">Felhasználó</span>
	                        <span class="list-remark">Komment</span>
	                    </div>
	                </a>
	                <a class="list" href="#">
	                    <div class="list-content">
	                        <span class="list-title">Felhasználó</span>
	                        <span class="list-remark">Komment</span>
	                    </div>
	                </a>
	                <a class="list" href="#">
	                    <div class="list-content">
	                        <span class="list-title">Felhasználó</span>
	                        <span class="list-remark">Komment</span>
	                    </div>
	                </a>
	                <a class="list" href="#">
	                    <div class="list-content">
	                        <span class="list-title">Felhasználó</span>
	                        <span class="list-remark">Komment</span>
	                    </div>
	                </a>
	            </div>
        	</div>
            <div class="input-control text" data-role="input-control">
                <textarea rows="3" cols="35" id="new_comment" type="text" placeholder="Hozzászólás írásához kattints ide..."></textarea>
                <button class="btn-clear" tabindex="-1"></button>
        	</div></br>
        	<button>Elküldés</button>
		</div>
	</div>
</div>