<?php
require_once('dal/DaoDB.php');
$controller = new DaoDB();
if (isset($_POST['city'])){
	if (isset($_POST['header'])){
		generateCityHeader($_POST['city']);
	}
	else {
		if (isset($_POST['populardests'])){
			generatePicturesOfCity($_POST['city'],true);
		}
		else {
			generatePicturesOfCity($_POST['city']);
		}
	}
}
else {
	if (isset($_POST['favdest'])){
		generateCityAlbums(true);	
	}
	else {
		generateCityAlbums();
	}
	
}

function generateCityAlbums($favdest = false){
	global $controller;
	if ($favdest){
		$cities = $controller->getPopularDestinations();
	} else {
		$cities = $controller->getCityAlbums();
	}
	
	echo '<div class="grid" style="margin-left: 30px; margin-right: 30px">
                    <div class="row">';
	foreach ($cities as $city) {
		echo '<div id="city_' . $city['ID'] . '" class="tile double live cityalbum" data-role="live-tile" data-effect="slideLeftRight">';
                $sides = array('250px', '-250px', '0', '250px');
                $i = 0;
                if (count($city['KEPEK']['ID']) > 0) {
                    foreach ($city['KEPEK']['ID'] as $pic_id) {
                        echo    '<div class="tile-content image" style="left:' . $sides[$i++] . ';">
                                	<img class="picture" id="pic_'.$pic_id.'">
                            	</div>';
                        if ($i == 4)
                            break;
                    }
                }
        echo    '<div class="brand bg-dark opacity">
                            <span class="label fg-grey">' . $city['VAROS'] . '</span>
                            <span class="badge">' . count($city['KEPEK']['ID']) . ' </span>
                        </div>
                      </div>';
		
	}
}  

function generateCityHeader($city_id){
	if (!is_null($city_id)){
		global $controller;
		echo '<nav class="horizontal-menu">
                    <a id="btn_city_back" style="color: black;" href="#"><i class="icon-arrow-left-3"></i></a>
                    <a style="color: black;" >' . $controller->getCityById($city_id) . '</a>';	
	}
	
}

function generatePicturesOfCity($city_id,$populardests = false){
	global $controller;
	echo '<div class="grid" style="margin-left: 30px; margin-right: 30px">
            <div class="row">';
	 		$pics = $controller->getPictures(0, 20, "all", $city_id, "FELTOLTES_IDEJE DESC",($populardests ? true : false));
            foreach ($pics as $val) {
                echo '<div id="pic_' . $val->getId() . '" class="tile double picture">
                        <div class="tile-content image">
                            <img class="tile_image" src="">
                        </div>
                      </div>';
            }
    echo '	</div>';
}   
?>