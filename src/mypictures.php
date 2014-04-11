<?php
    require_once("UserClass.php");
    session_start();
    $usr = $_SESSION['userObject'];
    if (isset($usr)) {
        if ($_GET["header"] == 1) {
            echo '<i class="icon-arrow-left-3 on-left"></i>
                    Fő album
                    <a class="dropdown-toggle place-right" style="color: black;" href="#">Új<i class="icon-new on-right" style="color: black;"></i></a>
                    <ul class="dropdown-menu place-right" data-role="dropdown">
                        <li><a id="btn_new_album" href="#">Album</a></li>
                        <li><a href="#">Kép</a></li>
                    </ul>
             </div>';
         } else {
             echo '<div id="content" class="panel-content">';

            $albums = $usr->getAlbums();
            if (count($albums) > 0) {
                foreach ($albums as $val) {  
                    echo '<div class="tile double" data-hint="' . $val->getCreateDate() . '|' . $val->getDescription(). '"
                            data-hint-position="bottom">
                        <div class="tile-content image">
                            <i class="icon-rocket"></i>
                        </div>
                        <div class="brand">
                            <span class="label fg-white">' . $val->getName() . '</span>
                            <span class="badge bg-orange">' . $val->getNumOfPics() . ' </span>
                        </div>
                        </div>';
                }
            } else {
                echo '<div class="notice marker-on-top bg-dark fg-white" style="width:30em; text-align:center">
                        Üres a fényképalbum.
                        </div>';
            }
            echo '</div>';
        }
    }
?>
