<?php
    require_once('dbstrings.php');
    require_once('/model/User.php');
    require_once('/model/Album.php');
    require_once('/model/Picture.php');

    class DaoDB
    {
        //User($id,$name,$email,$country,$city)
        public function getUserById($id){
            $con  = oci_connect(constant('DB_USER'), constant('DB_PW'), 'localhost/XE','AL32UTF8');
            if (!$con) {
                $e = oci_error();
                trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
            }
            $query = 'SELECT NEV,EMAIL,VAROS,ORSZAG 
                    FROM FELHASZNALOK, VAROSOK 
                    WHERE FELHASZNALOK.VAROS_ID = VAROSOK.ID 
                    AND FELHASZNALOK.ID =' . $id;
            $stmt = oci_parse($con, $query);
            oci_execute($stmt);
            oci_fetch_all($stmt, $result);
            $user = new User($id,$result["NEV"][0],$result["EMAIL"][0],$result["ORSZAG"][0],$result["VAROS"][0]);

            //avatar
            $query = 'SELECT AVATAR FROM FELHASZNALOK WHERE ID = :id';
            $stmt = oci_parse($con, $query);
            oci_bind_by_name($stmt, ':id', $id);
            oci_execute($stmt);
            $row = oci_fetch_array($stmt, OCI_RETURN_NULLS);
            if (is_object($row['AVATAR'])) {
                $user->setAvatar($row['AVATAR']->load());
                $row['AVATAR']->free();
            }
            $stmt = null;
            oci_close($con);
            return $user;
        }

        public function addUser($user){
            $con = oci_connect(constant('DB_USER'), constant('DB_PW'), 'localhost/XE','AL32UTF8');
            $query = 'begin register(:usr,:pwd,:name,:mail,:country,:city); end;';
            $stmt = oci_parse($con, $query);
            oci_bind_by_name($stmt, ':usr', $user['username']);
            oci_bind_by_name($stmt, ':pwd', $user['password']);
            oci_bind_by_name($stmt, ':name', $user['name']);
            oci_bind_by_name($stmt, ':mail', $user['email']);
            oci_bind_by_name($stmt, ':country', $user['country']);
            oci_bind_by_name($stmt, ':city', $user['city']);
            $ok = oci_execute($stmt);
            oci_close($con);
            return $ok;
        }

        public function isUserNameTaken($username){
            $con = oci_connect(constant('DB_USER'), constant('DB_PW'), 'localhost/XE','AL32UTF8');
            $query = 'SELECT COUNT(FELHASZNALONEV) FROM BEJELENTKEZESI_ADATOK WHERE FELHASZNALONEV =:nev';
            $stmt = oci_parse($con, $query);
            oci_bind_by_name($stmt, ":nev", $username);
            oci_define_by_name($stmt, 'COUNT(FELHASZNALONEV)', $count);
            oci_execute($stmt);
            oci_fetch($stmt);
            oci_close($con);
            if ($count > 0){
                return true;
            } else {
                return false;
            }
        }

        //kimeneti változókra a 4. paramétert rátegyed
        public function verifyUser($username, $password){
            $con = oci_connect(constant('DB_USER'), constant('DB_PW'), 'localhost/XE', 'AL32UTF8');
            $query = 'begin verifyUser(:usr, :pwd, :uid); end;';
            $stmt = oci_parse($con, $query);
            oci_bind_by_name($stmt, ":usr", $username);
            oci_bind_by_name($stmt, ":pwd", $password);
            oci_bind_by_name($stmt, ":uid", $user_id, 32);
            oci_execute($stmt);
            oci_close($con);
            return $user_id;
        }

        public function uploadPicture($blob,$place,$desc,$albid){
            if ($albid == "null"){
                $albid = null;
            }
            $cid = null;
            $con = oci_connect(constant('DB_USER'), constant('DB_PW'), 'localhost/XE','AL32UTF8');
            $query = "INSERT INTO KEPEK (ID,  LEIRAS, FELTOLTES_IDEJE, HELYSZIN, KEPFAJL, ALBUM_ID, FELH_ID, KAT_ID)
                      VALUES (image_seq.nextval, :descr, SYSDATE, :place, EMPTY_BLOB(), :albid, :user_id, :cid)
                      RETURNING KEPFAJL INTO :myblob";

            $stmt = oci_parse($con, $query);
            $dlob = oci_new_descriptor($con, OCI_D_LOB);
            oci_bind_by_name($stmt, ':myblob', $dlob, -1, OCI_B_BLOB);
            oci_bind_by_name($stmt, ':user_id', $_SESSION['userObject']->getId());
            oci_bind_by_name($stmt, ':albid', $albid);
            oci_bind_by_name($stmt, ':cid', $cid);
            oci_bind_by_name($stmt, ':descr', $desc);
            oci_bind_by_name($stmt, ':place', $place);
            oci_execute($stmt, OCI_NO_AUTO_COMMIT);

            if ($dlob->save($blob)) {
                oci_commit($con);
                oci_close($con);
                return true;
            } else {
                oci_rollback($con);
                oci_close($con);
                return false;
            }
        }

        //Picture($id, $category, $desc, $time, $place, $data, $owner)
        public function getPictures($album_id){
            if (!$album_id){
                $query = 'SELECT NEV, KEPEK.ID, LEIRAS, HELYSZIN, KEPFAJL, KAT_ID, FELTOLTES_IDEJE
                FROM FELHASZNALOK, KEPEK
                WHERE FELHASZNALOK.ID = KEPEK.FELH_ID 
                AND FELH_ID = ' . $_SESSION['userObject']->getId().' 
                AND ALBUM_ID IS NULL';
            } else {
                $query = 'SELECT NEV, KEPEK.ID, LEIRAS, HELYSZIN, KEPFAJL, KAT_ID, FELTOLTES_IDEJE
                FROM FELHASZNALOK, KEPEK
                WHERE FELHASZNALOK.ID = KEPEK.FELH_ID 
                AND FELH_ID = ' . $_SESSION['userObject']->getId().' 
                AND ALBUM_ID = ' . $album_id;
            }
            $con = oci_connect(constant('DB_USER'), constant('DB_PW'), 'localhost/XE','AL32UTF8');
            $stmt = oci_parse($con, $query);
            oci_execute($stmt);   
            $pics = array();
            while ($row = oci_fetch_array($stmt,  OCI_ASSOC + OCI_RETURN_NULLS)) {
                if (is_object($row['KEPFAJL'])) {
                    $owner = $row['NEV'];
                    $id = $row['ID'];
                    $desc = $row['LEIRAS'];
                    $place = $row['HELYSZIN'];
                    $time = $row['FELTOLTES_IDEJE'];
                    $blob = $row['KEPFAJL']->load();
                    $pics[$id] = new Picture($id, null , $desc, $time, $place, $blob, $owner);
                    $row['KEPFAJL']->free();
                }
            }
            $stmt = null;
            oci_close($con);
            return $pics;
        }

        public function getPictureById($picture_id){
             $con  = oci_connect(constant('DB_USER'), constant('DB_PW'), 'localhost/XE','AL32UTF8');
            if (!$con) {
                $e = oci_error();
                trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
            }
           $query = 'SELECT NEV, LEIRAS, HELYSZIN, KEPFAJL, FELTOLTES_IDEJE
                FROM FELHASZNALOK, KEPEK
                WHERE FELHASZNALOK.ID = KEPEK.FELH_ID 
                AND KEPEK.ID = '. $picture_id;
            $stmt = oci_parse($con, $query);
            oci_execute($stmt);
            while ($row = oci_fetch_array($stmt,  OCI_ASSOC + OCI_RETURN_NULLS)) {
                if (is_object($row['KEPFAJL'])) {
                    $owner = $row['NEV'];
                    $desc = $row['LEIRAS'];
                    $place = $row['HELYSZIN'];
                    $time = $row['FELTOLTES_IDEJE'];
                    $blob = $row['KEPFAJL']->load();
                    $pic = new Picture($picture_id,null,$desc,$time,$place,$blob,$owner);
                    $row['KEPFAJL']->free();
                }
            }
            $stmt = null;
            oci_close($con);
            return $pic;
        }


        public function createAlbum($name, $desc){
            $con  = oci_connect(constant('DB_USER'), constant('DB_PW'), 'localhost/XE','AL32UTF8');
            if (!$con) {
                $e = oci_error();
                trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
            }
            $query = 'begin create_album(:name, :desc, :uid, :id, :time); end;';
            $stmt = oci_parse($con, $query);
            oci_bind_by_name($stmt, ':name', $name);
            oci_bind_by_name($stmt, ':desc', $desc);
            oci_bind_by_name($stmt, ':uid', $_SESSION['userObject']->getId());
            oci_bind_by_name($stmt, ':id', $album_id, 32);
            oci_bind_by_name($stmt, ':time', $album_time, 64);
            oci_execute($stmt);
            oci_close($con);
            return true;
        }

        public function getAlbums(){
            $con  = oci_connect(constant('DB_USER'), constant('DB_PW'), 'localhost/XE','AL32UTF8');
            if (!$con) {
                $e = oci_error();
                trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
            }
            $query = 'SELECT ID, NEV, LEIRAS, TO_CHAR(LETREHOZAS_IDEJE, \'YYYY/MM/DD HH24:MI:SS\') AS LETREHOZAS_IDEJE FROM ALBUMOK WHERE FELH_ID=' . $_SESSION['userObject']->getId();
            $stmt = oci_parse($con, $query);
            $lines = oci_execute($stmt);
            $albumok = array();
            while ($row = oci_fetch_array($stmt,OCI_ASSOC + OCI_RETURN_NULLS)) {
                
                $id = $row["ID"];
                $name = $row["NEV"];
                $desc = $row["LEIRAS"];
                $date = $row["LETREHOZAS_IDEJE"];
                $albumok[$id] = new Album($id, $name, $desc, $date);
                
            }
            /*foreach ($albumok as $key => $value) {
                $query = 'SELECT COUNT(*) FROM KEPEK WHERE ALBUM_ID = '.$value->getId();
                $stmt = oci_parse($con, $query);
                oci_execute($stmt);
                $numofpics[0] = oci_fetch_array($stmt);
                for ($i = 0 ; $i < $numofpics; $i++){
                    $value->incNumOfPics();
                }
            }*/
            oci_close($con);
            return $albumok;
        }

        public function getAlbumById($album_id){
             $con  = oci_connect(constant('DB_USER'), constant('DB_PW'), 'localhost/XE','AL32UTF8');
            if (!$con) {
                $e = oci_error();
                trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
            }
            $query = 'SELECT NEV, LEIRAS, TO_CHAR(LETREHOZAS_IDEJE, \'YYYY/MM/DD HH24:MI:SS\') 
                      AS LETREHOZAS_IDEJE 
                      FROM ALBUMOK 
                      WHERE FELH_ID=' . $_SESSION['userObject']->getId() .'
                      AND ID = '.$album_id;
            $stmt = oci_parse($con, $query);
            oci_execute($stmt);
            $album = null;
            while ($row = oci_fetch_array($stmt, OCI_ASSOC + OCI_RETURN_NULLS)) {
                $id = $album_id;
                $name = $row["NEV"];
                $desc = $row["LEIRAS"];
                $date = $row["LETREHOZAS_IDEJE"];
                $album = new Album($id, $name, $desc, $date);
            }
            /*$query = 'SELECT COUNT(*) INTO :numofpics FROM KEPEK WHERE ALBUM_ID = '.$id;
            $stmt = oci_parse($con, $query);
            oci_execute($stmt);
            $numofpics = oci_fetch($stmt);
            for ($i = 0 ; $i < $numofpics; $i++){
                $album->incNumOfPics();
            }*/
            oci_close($con);
            return $album;
        }


        public function updateAvatar($blob){
            $con  = oci_connect(constant('DB_USER'), constant('DB_PW'), 'localhost/XE','AL32UTF8');
            $query = 'UPDATE FELHASZNALOK
                    SET AVATAR = EMPTY_BLOB()
                    WHERE ID = :id
                    RETURNING AVATAR INTO :myblob';
            $stmt = oci_parse($con, $query);
            $dlob = oci_new_descriptor($con, OCI_D_LOB);
            oci_bind_by_name($stmt, ':myblob', $dlob, -1, OCI_B_BLOB);
            oci_bind_by_name($stmt, ':id', $_SESSION['userObject']->getId());
            oci_execute($stmt, OCI_NO_AUTO_COMMIT);
            if ($dlob->save($blob)) {
                oci_commit($con);
                oci_close($con);
                return true;
            } else {
                oci_close($con);
                return false;
            }
        }
    }
?>
