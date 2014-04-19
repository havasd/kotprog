<div class="panel">
    <div id="categories" class="panel-header">
			<div class="button-set" data-role="button-group">
				<button class="active">Mind</button>
				<button>Absztrakt</button>
			    <button>Fotó</button>
			    <button>Rajz</button>
			    <button>Festmény</button>
			</div>
    </div>
    <div id="picture_tiles" class="panel-content">
		        
		<?php
			$directory = '../images/';
			$extension = '.jpg';

			if ( file_exists($directory) ) {
			   	$i=0;
			   	foreach(glob($directory."*".$extension) as $file){
			   		if ( $i >= 15){
			   			break;
			   		}
					echo "<div class=\"tile double picture\">
							    <div class=\"tile-content image\">
							        <img class=\"tile_image\" src=\"".$file."\">
							    </div>
							    <div class=\"brand bg-dark opacity\">
							        <span class=\"text\">
							            ".$file."
							        </span>
							    </div>
							</div>";
			   		$i++;
			   	}
			}
			else {
				echo "directory not avaiable";
			}
		?>    
    </div>
</div>