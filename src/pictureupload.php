<?php
    require_once('model/User.php');
    session_start();
    $usr = $_SESSION['userObject'];
    if (isset($usr)) {
        if (isset($_FILES[0])) {
            $blob = base64_encode(file_get_contents($_FILES[0]['tmp_name']));
            if ($usr->uploadPicture($blob))
                echo json_encode(array('create' => 'true'));
            else
                echo json_encode(array('create' => 'false'));
        } else {
            echo '<form id="f_new_pictures" style="margin: 5px 5px 5px 5px">
                    <div class="grid fluid show-grid">
                        <div class="row">
                            <input type="file" name="file_picture" id="in_file_picture"/><br>
                        </div>
                        <div class="row">
                            <input type="submit" id="btn_upload_picture" value="Feltöltés">
                        </div>
                    </div>
                </form>';
        }
    }
?>