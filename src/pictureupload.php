<?php
    require_once('model/User.php');
    require_once('model/Album.php');
    require_once('dal/DaoDB.php');
    session_start();
    $usr = $_SESSION['userObject'];
    if (isset($usr)) {
        $controller = new DaoDB();
        $albumok = $controller->getAlbumsByUser();
        $categories = $controller->getCategories();
        if (isset($_FILES[0]) && isset($_POST)) {
            $controller = new DaoDB();
            $place = $_POST['file_place'];
            $desc = $_POST['file_desc'];
            $album_id = $_POST['file_album'];
            $category = $_POST['file_category'];
            $blob = base64_encode(file_get_contents($_FILES[0]['tmp_name']));
            //uploadPicture($blob,$title,$place,$desc,$albid)
            if ($controller->uploadPicture($blob,$place,$desc,$album_id,$category))
                echo json_encode(array('create' => 'true'));
            else
                echo json_encode(array('create' => 'false'));
        } else {
            
            echo '<form id="f_new_pictures" style="margin: 5px 5px 5px 5px">
                    <div class="grid fluid show-grid">
                        <div class="row">
                            <label>Helyszín</label>
                            <input type="text" name="file_place" id="in_file_place"/><br>
                        </div>
                        <div class="row">
                            <label>Leírás</label>
                            <input type="text" name="file_desc" id="in_file_desc"/><br>
                        </div>
                        <div class="row">
                            <label>Képfájl</label>
                            <input type="file" name="file_picture" id="in_file_picture"/><br>
                        </div>';
            echo '      <div class="row">
                            <label>Kategória</label>
                            <select name="file_category" id="in_file_category">';
                        foreach ($categories as $key => $value) {
                            echo '<option value="'.$key.'">'.$value.'</option>';
                        }
            echo '          </select>
                        </div>';
            echo '      <div class="row">
                            <label>Album</label>
                            <select name="file_album" id="in_file_album">
                            <option value="null">Főalbum</option>';
                        foreach ($albumok as $key => $value) {
                            echo '<option value="'.$value->getId().'" ' . ($_POST['curr_album'] == $key ? 'selected' : '') . '>' . $value->getName() . '</option>';             
                        };
            echo            '</select>
                        </div>
                        <div class="row">
                            <input type="submit" id="btn_upload_picture" value="Feltöltés">
                        </div>
                    </div>
                </form>';
        }
    }
?>
