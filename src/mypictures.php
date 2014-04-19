<?php
    require_once("UserClass.php");
    session_start();
    $usr = $_SESSION['userObject'];
    if (isset($usr)) {
        $albid = 0;
        if (isset($_GET["alb"]))
            $albid = explode("_", $_GET["alb"])[1];

        if ($_GET["header"] == 1) {
            generateHeader($albid);
         } else {
            generateContent($albid);
        }
    }

    function generateHeader($albumid){
        global $usr;
        if (!$albumid) {
            echo 'Képeim
                    <a class="dropdown-toggle place-right" style="color: black;" href="#">Új<i class="icon-new on-right" style="color: black;"></i></a>
                    <ul class="dropdown-menu place-right" data-role="dropdown">
                        <li><a id="btn_new_album" href="#">Album</a></li>
                        <li><a id="btn_new_picture" href="#">Kép</a></li>
                    </ul>
                  </div>';
        } else {
            echo '<a id="btn_album_back" style="color: black;" href="#"><i class="icon-arrow-left-3 on-left"></i></a>'
                    . $usr->getAlbumById($albumid)->getName() . 
                    '<a class="dropdown-toggle place-right" style="color: black;" href="#">Új<i class="icon-new on-right" style="color: black;"></i></a>
                    <ul class="dropdown-menu place-right" data-role="dropdown">
                        <li><a id="btn_new_picture" href="#">Kép</a></li>
                    </ul>
                  </div>';
        }

    }

    function generateContent($albumid){
        global $usr;
        // main page
        if (!$albumid) {
            $albums = $usr->getAlbums();
            // TODO: get pictures without album
            if (count($albums)) {
                echo '<div class="grid" style="margin-left: 30px; margin-right: 30px">
                        <div class="row">';
                foreach ($albums as $val) {  
                    echo '<div id="alb_' . $val->getId() . '" class="tile double album" data-hint="' . $val->getCreateDate() . '|' . $val->getDescription(). '"data-hint-position="bottom">
                            <div class="tile-content image">
                                <img class="tile_image" src="">
                            </div>
                            <div class="brand bg-dark">
                                <span class="label fg-grey">' . $val->getName() . '</span>
                                <span class="badge">' . $val->getNumOfPics() . ' </span>
                            </div>
                          </div>';
                }
                echo '</div></div>';
            } else {
                echo '<div class="notice marker-on-top bg-dark fg-white" style="width:30em; text-align:center">
                        Üres a fényképalbum.
                      </div>';
            }
        } else { // load pictures from albums
            $album = $usr->getAlbumById($albumid);
            if ($album->getNumOfPics()) { // load pictures from album
                /*echo '<div class="grid" style="margin-left: 30px; margin-right: 30px">
                        <div class="row">';
                foreach ($album as $val) {  
                    echo '<div id="alb_' . $val->getId() . '" class="tile double album" data-hint="' . $val->getCreateDate() . '|' . $val->getDescription(). '"data-hint-position="bottom">
                            <div class="tile-content image">
                                <img class="tile_image" src="apache_pb.png">
                            </div>
                            <div class="brand bg-dark">
                                <span class="label fg-grey">' . $val->getName() . '</span>
                                <span class="badge">' . $val->getNumOfPics() . ' </span>
                            </div>
                          </div>';
                }
                echo '</div></div>';*/
            } else { // empty album
                echo '<div class="notice marker-on-top bg-dark fg-white" style="width:30em; text-align:center">
                        Nincsenek képek ebben az albumban.
                      </div>';
            }
        }
    }
?>
