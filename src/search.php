<?php
require_once('dal/DaoDB.php');
$controller = new DaoDB();
echo '<div class="grid" style="margin-left: 30px; margin-right: 30px">';

function search($keyword){ 
	global $controller;
	$result = $controller->search($keyword);
	$sides = array('0', '-250px', '250px', '500px');
	echo '<div class="row">';
	foreach ($result['albumok'] as $album_id) {
		$album = $controller->getAlbumById($album_id);
		$pictures = $album->getPictureIdList();
		echo '<div id="alb_' . $album_id . '" class="tile double live result_album" data-role="live-tile" data-effect="slideLeftRight">';
                $i = 0;
                if (count($pictures > 0)) {
                    foreach ($pictures as $pic_id) {
                        echo    '<div class="tile-content image" style="left:' . $sides[$i++] . ';">
                                	<img class="result_album_picture" id="pic_'.$pic_id.'">
                            	</div>';
                        if ($i == 4)
                            break;
                    }
                }
        echo    '<div class="brand bg-dark opacity">
                            <span class="label fg-grey">' . $album->getName() . '</span>
                            <span class="badge">' . count($pictures) . ' </span>
      	          </div>
              </div>';
    }
    echo '	</div>
    		<div class="row">';
    foreach ($result['kepek'] as $pic_id) {
    	echo    '<div id="pic_'.$pic_id.'" class="tile double picture">
                    <div class="tile-content image">
                                	<img src="" >
            		</div>
            	</div>';
    }
    echo '	</div>';
}	

function resultAlbum($album_id){	
	global $controller;
	echo '<div class="row">';
	$album = $controller->getAlbumById($album_id);
	foreach ($album->getPictureIdList() as $pic){
                echo '<div id="pic_' . $pic . '" class="tile double picture">
                        <div class="tile-content image">
                            <img class="tile_image" src="">
                        </div>
                      </div>';
    }
	
	echo '</div>';
}

function resultAlbumHeader($album_id){
	global $controller;
	$album = $controller->getAlbumById($album_id);
	echo '<nav class="horizontal-menu">
                    <a id="btn_search_back" style="color: black;" href="#"><i class="icon-arrow-left-3"></i></a>
                    <a style="color: black;" >' . $album->getName() . '      '. $album->getDescription().'</a>';
}


if (isset($_POST['search'])){
	$keyword = $_POST['search'];
	search($keyword);
}	
else if(isset($_POST['result_album'])){
	resultAlbum($_POST['result_album']);
}
else if(isset($_POST['result_album_header'])){
	resultAlbumHeader($_POST['result_album_header']);
}
	

echo '</div>
	</div>';

?>