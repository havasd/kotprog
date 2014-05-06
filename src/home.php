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
		  echo '<div id="categories" class="button-set" data-role="button-group">
              <button id="cat_all" class="active">Mind</button>';
  	  foreach ($categories as $key => $value) {
  					echo '<button id="cat_'.$key.'">'.$value.'</button>';
  		}
      echo '</div>';
      echo '<div id="order_by" class="button-set" data-role="button-group">';
      echo '  <button id="order_time_desc" class="active"> Idő szerint csökkenő </button>
              <button id="order_time_asc" > Idő szerint növekvő </button>';
      echo '  <button id="order_rating_desc"> Értékelés szerint csökkenő</button>
              <button id="order_rating_asc"> Értékelés szerint növekvő</button>';
      echo '</div>';
  	}

	function generateContent(){
		global $controller;
    
    echo '	<div id="picture_tiles" class="grid" style="margin-left: 1%; margin-right: 1%">
              <div class="row" style=" display: inline-block;">';
   	if (isset($_POST['category']) && isset($_POST['orderby'])){
   		$pics = $controller->getPictures(0,999,$_POST['category'],null,$_POST['orderby']);
      $i=0;
      while (isset($pics[$i])){
        echo '<div id="pic_' . $pics[$i]->getId() . '" class="tile double picture">
                    <div class="tile-content image">
                        <img class="tile_image" src="'/*.$pics[$i]->getPictureTileBinary() */.'">
                        '.$pics[$i]->getUploadTime().'    '.$pics[$i]->getRating().'
                    </div>
                  </div>';
        $i++;
      }
      echo '</div>
            </div>';
   	} 
  } 
?>
