<?php
    require_once('model/User.php');
    require_once('dal/DaoDB.php');
    session_start();
    $user = $_SESSION['userObject'];
    if (isset($user)) {
        $controller = new DaoDB();
        if (isset($_POST['alb_name'])) {
            $album_name = $_POST['alb_name'];
            $album_desc = $_POST['alb_desc'];
            if($controller->createAlbum($album_name, $album_desc)){
                echo json_encode(array('create' => 'true'));    
            } else {
                echo json_encode(array('create' => 'false'));    
            }
            
        } else {
            echo '<form id="f_new_album" style="margin: 5px 5px 5px 5px">
                    <div class="grid fluid show-grid">
                        <div class="row">
                            <input id="new_alb_name" name="alb_name" placeholder="Album neve..." required type="text">
                        </div>
                        <div class="row">
                            <textarea name="alb_desc" placeholder="Album leírás..." rows="4" cols="30" style="resize: none" maxlength="256"></textarea>
                        </div>
                        <div class="row">
                            <input type="submit" id="btn_create_album" value="Létrehozás">
                        </div>
                    </div>
                </form>';
        }
    }
?>
