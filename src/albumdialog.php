<?php
    require_once('model/User.php');
    require_once('dal/DaoDB.php');
    session_start();
    $user = $_SESSION['userObject'];
    $controller = new DaoDB();
    if (isset($user)) {
        if (isset($_POST['alb_name'])) {
            $album_id = $_POST['id'];
            $album_name = $_POST['alb_name'];
            $album_desc = $_POST['alb_desc'];
            if ($album_id == 0)
                $res = $controller->createAlbum($album_name, $album_desc);
            else
                $res = $controller->updateAlbum($album_id, $album_name, $album_desc);

            if($res){
                echo json_encode(array('create' => 'true'));    
            } else {
                echo json_encode(array('create' => 'false'));    
            }
            
        } else {
            generateAlbumDialog($_POST['id']);
        }
    }

    function generateAlbumDialog($id = 0){
        global $controller;
        if ($id)
            $album = $controller->getAlbumById($id);
        echo '<form id="f_new_album" style="margin: 5px 5px 5px 5px" data-id="' . $id . '">
                <div class="grid fluid show-grid">
                    <div class="row">
                        <input id="new_alb_name" name="alb_name" ' . (($id == 0) ? 'placeholder="Album neve..."' : 'value="' . $album->getName() . '"') . ' required type="text">
                    </div>
                    <div class="row">
                        <textarea name="alb_desc" ' . (($id == 0) ? 'placeholder="Album leírás..."' : '') .' rows="4" cols="30" style="resize: none" maxlength="256">' . (($id == 0) ? '' : $album->getDescription())  . '</textarea>
                    </div>
                    <div class="row">
                        <input type="submit" id="btn_create_album" value="' . (($id == 0) ? 'Létrehozás' : 'Módosít') . '">
                    </div>
                </div>
            </form>';
    }
?>
