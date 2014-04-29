<?php
    require_once('model/Picture.php');
    require_once('dal/DaoDB.php');
    $controller = new DaoDB();
    if (isset($_POST['header']) && $_POST['header'] == 1) {
        generateHeader();
     } else {
        generateContent();
    }
    

    function generateHeader(){
    	global $controller;
    	$categories = $controller->getCategories();
		echo '<div id="categories" class="button-set" data-role="button-group">';
		if (isset($_POST['category'])){
			echo '<button id="cat_all">Mind</button>';
			foreach ($categories as $key => $value) {
				if ($key == $_POST['category']){
					echo '	<button id="cat_'.$key.'" class="active">'.$value.'</button>';
				} else{
					echo '	<button id="cat_'.$key.'">'.$value.'</button>';
				}
			}
		} else {
			echo '<button id="cat_all" class="active">Mind</button>';
			foreach ($categories as $key => $value) {
				echo '	<button id="cat_'.$key.'">'.$value.'</button>';
			}
		}
		
		echo '</div>';
	}

	function generateContent(){
		global $controller;
		echo '	<div id="picture_tiles" class="grid" style="margin-left: 30px; margin-right: 30px">';
   	if (isset($_POST['category']) && $_POST['category'] != "all"){
   		$pics = $controller->getPicturesByCategory($_POST['category']);
        foreach ($pics as $val) {
            echo '<div id="pic_' . $val->getId() . '" class="tile double picture">
                    <div class="tile-content image">
                        <img class="tile_image" src="data:image/jpeg;base64,'. $val->getPictureTileBinary() . '">
                    </div>
                  </div>';
        }
   	} else {
   		$pics = $controller->getAllPictures();
        foreach ($pics as $val) {
            echo '<div id="pic_' . $val->getId() . '" class="tile double picture">
                    <div class="tile-content image">
                        <img class="tile_image" src="data:image/jpeg;base64,'. $val->getPictureTileBinary() . '">
                    </div>
                  </div>';
        }
   	}
            
	echo '</div>';
	}
	
    
?>
