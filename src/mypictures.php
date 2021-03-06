<?php
    require_once('model/User.php');
    require_once('model/Album.php');
    require_once('model/Picture.php');
    require_once('dal/DaoDB.php');
    session_start();
    $controller = null;
    $usr = $_SESSION['userObject'];
    if (isset($usr)) {
        $controller = new DaoDB();
        $albid = 0;
        if (isset($_POST["alb"])){
            $albid = explode("_", $_POST["alb"])[1];
        }
        if (isset($_POST["header"])){
            if ($_POST["header"] == 1) {
                generateHeader($albid);
            } else {
                generateContent($albid);
            }
        }
        
    }

    function generateHeader($albumid){
        global $controller;
        if (!$albumid) {
            echo '<nav class="horizontal-menu">
                    <a style="color: black;"">Képeim</a>
                    <a class="dropdown-toggle place-right" style="color: black;" href="#"><i class="icon-new" style="color: black;"></i>Új</a>
                    <ul class="dropdown-menu place-right" data-role="dropdown">
                        <li><a id="btn_new_album" href="#">Album</a></li>
                        <li><a id="btn_new_picture" href="#">Kép</a></li>
                    </ul>
                    <a class="place-right" id="b_delete" style="color: black;" href="#"><i class="icon-remove on-left" style="color: black;"></i>Törlés</a>
                    <a class="place-right" id="b_edit" style="color: black;" href="#"><i class="icon-wrench" style="color: black;"></i>Szerkesztés</a></nav>';
        } else {
            $current_album = $controller->getAlbumById($albumid);
            echo '<nav class="horizontal-menu">
                    <a id="btn_album_back" style="color: black;" href="#" data-id="' . $albumid . '"><i class="icon-arrow-left-3"></i></a>
                    <a style="color: black;" >' . $current_album->getName() . '</a>
                    <a class="dropdown-toggle place-right" style="color: black;" href="#"><i class="icon-new" style="color: black;"></i>Új</a>
                    <ul class="dropdown-menu place-right" data-role="dropdown">
                        <li><a id="btn_new_picture" href="#">Kép</a></li>
                    </ul>
                    <a class="place-right" id="b_delete" style="color: black;" href="#"><i class="icon-remove" style="color: black;"></i>Törlés</a>
                    <a class="place-right" id="b_edit" style="color: black;" href="#"><i class="icon-wrench" style="color: black;"></i>Szerkesztés</a></nav>';
        }
    }

    function generateContent($albumid){
        global $controller;
        // main page
        //SAJÁT ALBUMOK

        if (!$albumid) {
            echo '<div class="grid" style="margin-left: 30px; margin-right: 30px">
                    <div class="row">';

            $albums = $controller->getAlbumsByUser();
            foreach ($albums as $val) {
                echo '<div id="alb_' . $val->getId() . '" class="tile double live album" data-role="live-tile" data-effect="slideLeftRight" 
                            data-hint="' . $val->getCreateDate() . '|' . $val->getDescription(). '"data-hint-position="bottom">';

                $pics = $controller->getPicturesByUser($val->getId());
                $sides = array('250px', '-250px', '0', '250px');
                $i = 0;

                if (count($pics) > 1) {
                    foreach ($pics as $key => $value) {
                        echo    '<div class="tile-content image" style="left:' . $sides[$i++] . ';">
                                <img  src="data:image/jpeg;base64,' . $value->getPictureTileBinary() . '">
                            </div>';
                        if ($i == 4)
                            break;
                    }
                } else {
                    foreach ($pics as $key => $value) {
                        echo    '<div class="tile-content image">
                                    <img  src="data:image/jpeg;base64,' . $value->getPictureTileBinary() . '">
                                </div>';
                    }
                }
                echo    '<div class="brand bg-dark opacity">
                            <span class="label fg-grey">' . $val->getName() . '</span>
                            <span class="badge">' . $val->getNumOfPics() . ' </span>
                        </div>
                      </div>';
            }
            /*echo '<div class="notice marker-on-top bg-dark fg-white" style="width:30em; text-align:center">
                        Üres a fényképalbum.
                      </div>';*/
            echo '</div>
                  <div class="row">';

            $pics = $controller->getPicturesByUser(null);
            foreach ($pics as $val) {
                echo '<div id="pic_' . $val->getId() . '" class="tile double picture">
                        <div class="tile-content image">
                            <img class="tile_image" src="">
                        </div>
                      </div>';
            }
            echo '</div>
                  </div>';
        } else { // load pictures from albums
            $pics = $controller->getPicturesByUser($albumid);
            if (count($pics)) {
                echo '<div class="grid" style="margin-left: 30px; margin-right: 30px">
                    <div class="row">';
                foreach ($pics as $key => $val) {
                    
                    echo '<div id="pic_' . $val->getId() . '" class="tile double picture">
                            <div class="tile-content image">
                                <img class="tile_image" src="">
                            </div>
                          </div>';
                }
                echo '</div>
                  </div>';
            } else { // empty album
                echo '<div class="notice marker-on-top bg-dark fg-white" style="width:30em; text-align:center">
                        Nincsenek képek ebben az albumban.
                      </div>';
            }
        }
    }
?>
