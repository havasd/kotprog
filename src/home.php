<?php
	require_once('dal/DaoDb.php');
	$controller = new DaoDb();

	echo '<div class="panel">
    		<div id="categories" class="panel-header">
				<div class="button-set" data-role="button-group">
					<button class="active">Mind</button>
					<button>Absztrakt</button>
			    	<button>Fotó</button>
			    	<button>Rajz</button>
			    	<button>Festmény</button>
				</div>
    		</div>
    		<div id="picture_tiles" class="panel-content">';

            $pics = $controller->getAllPictures();
            foreach ($pics as $val) {
                echo '<div id="pic_' . $val->getId() . '" class="tile double picture">
                        <div class="tile-content image">
                            <img class="tile_image" src="data:image/jpeg;base64,'. $val->getPictureBinary() . '">
                        </div>
                      </div>';
            }
	echo '</div>
			</div>';
?>
