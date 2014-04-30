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

        public function uploadPicture($blob,$place,$desc,$albid,$cid){
            if ($albid == "null"){
                $albid = null;
            }
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

        public function getPictures($from_index, $to_index, $category_id, $orderby){
            $query = '
                SELECT * FROM (   
                    SELECT NEV, KEPEK.ID, LEIRAS, HELYSZIN, KEPFAJL, KAT_ID,
                    TO_CHAR(FELTOLTES_IDEJE, \'YYYY/MM/DD HH24:MI:SS\') AS FELTOLTES_IDEJE,
                    (SELECT AVG(ERTEKELES) FROM ERTEKELESEK WHERE KEP_ID = KEPEK.ID) AS RATE
                    FROM FELHASZNALOK, KEPEK
                    WHERE FELHASZNALOK.ID = KEPEK.FELH_ID'.
                    (($category_id != "all") && ($category_id) ? ' AND KAT_ID = :category_id_bi ' : ''). 
                    ' ORDER BY ' .$orderby.' )
                 
                WHERE ROWNUM >= :from_index_bi 
                AND ROWNUM <= :to_index_bi';
            $con = oci_connect(constant('DB_USER'), constant('DB_PW'), 'localhost/XE','AL32UTF8');
            $stmt = oci_parse($con, $query);
            
            if ($category_id != "all"){
                oci_bind_by_name($stmt, ':category_id_bi', $category_id);
            }
            
            oci_bind_by_name($stmt, ':from_index_bi', $from_index);
            oci_bind_by_name($stmt, ':to_index_bi', $to_index);
            oci_execute($stmt);
            $pics = array();
            $i=0;
            while ($row = oci_fetch_array($stmt,  OCI_ASSOC + OCI_RETURN_NULLS)) {
                if (is_object($row['KEPFAJL'])) {
                    $owner = $row['NEV'];
                    $id = $row['ID'];
                    $desc = $row['LEIRAS'];
                    $place = $row['HELYSZIN'];
                    $cat_id = $row['KAT_ID'];
                    $time = $row['FELTOLTES_IDEJE'];
                    $blob = $row['KEPFAJL']->load();
                    $rating = (is_null($row['RATE']) ? 0 : $row['RATE']);
                    $pics[$i] = new Picture($id, $cat_id , $desc, $time, $place, $blob, $owner, $rating);
                    $row['KEPFAJL']->free();
                    $i++;
                }
                //var_dump($row);
                //echo "<br>";
            }
            $stmt = null;
            oci_close($con);
            //var_dump($pics);
            return $pics;
        }

        public function getAllPictures(){
            $query = 'SELECT NEV, KEPEK.ID, LEIRAS, HELYSZIN, KEPFAJL, KAT_ID,
            TO_CHAR(FELTOLTES_IDEJE, \'YYYY/MM/DD HH24:MI:SS\') AS FELTOLTES_IDEJE,
            (SELECT AVG(ERTEKELES) FROM ERTEKELESEK WHERE KEP_ID = KEPEK.ID) AS RATE
            FROM FELHASZNALOK, KEPEK
            WHERE FELHASZNALOK.ID = KEPEK.FELH_ID';
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
                    $rating = (is_null($row['RATE']) ? 0 : $row['RATE']);
                    $pics[$id] = new Picture($id, null , $desc, $time, $place, $blob, $owner, $rating);
                    $row['KEPFAJL']->free();
                }
            }
            $stmt = null;
            oci_close($con);
            return $pics;
        }



        public function getPicturesByCategory($cid){
            $query = 'SELECT NEV, KEPEK.ID, LEIRAS, HELYSZIN, KEPFAJL,
            TO_CHAR(FELTOLTES_IDEJE, \'YYYY/MM/DD HH24:MI:SS\') AS FELTOLTES_IDEJE,
            (SELECT AVG(ERTEKELES) FROM ERTEKELESEK WHERE KEP_ID = KEPEK.ID) AS RATE
            FROM FELHASZNALOK, KEPEK
            WHERE FELHASZNALOK.ID = KEPEK.FELH_ID
            AND KAT_ID = '.$cid;
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
                    $rating = (is_null($row['RATE']) ? 0 : $row['RATE']);
                    $pics[$id] = new Picture($id, $cid , $desc, $time, $place, $blob, $owner, $rating);
                    $row['KEPFAJL']->free();
                }
            }
            $stmt = null;
            oci_close($con);
            return $pics;
        }

        //Picture($id, $category, $desc, $time, $place, $data, $owner)
        public function getPicturesByUser($album_id){
            if (!$album_id){
                $query = 'SELECT NEV, KEPEK.ID, LEIRAS, HELYSZIN, KEPFAJL, KAT_ID,
                TO_CHAR(FELTOLTES_IDEJE, \'YYYY/MM/DD HH24:MI:SS\') AS FELTOLTES_IDEJE,
                (SELECT AVG(ERTEKELES) FROM ERTEKELESEK WHERE KEP_ID = KEPEK.ID) AS RATE
                FROM FELHASZNALOK, KEPEK
                WHERE FELHASZNALOK.ID = KEPEK.FELH_ID 
                AND FELH_ID = ' . $_SESSION['userObject']->getId().' 
                AND ALBUM_ID IS NULL';
            } else {
                $query = 'SELECT NEV, KEPEK.ID, LEIRAS, HELYSZIN, KEPFAJL, KAT_ID,
                TO_CHAR(FELTOLTES_IDEJE, \'YYYY/MM/DD HH24:MI:SS\') AS FELTOLTES_IDEJE,
                (SELECT AVG(ERTEKELES) FROM ERTEKELESEK WHERE KEP_ID = KEPEK.ID) AS RATE
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
                    $rating = (is_null($row['RATE']) ? 0 : $row['RATE']);
                    $pics[$id] = new Picture($id, null , $desc, $time, $place, $blob, $owner, $rating);
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
           $query = 'SELECT NEV, LEIRAS, HELYSZIN, KEPFAJL, 
                TO_CHAR(FELTOLTES_IDEJE, \'YYYY/MM/DD HH24:MI:SS\') AS FELTOLTES_IDEJE,
                (SELECT AVG(ERTEKELES) FROM ERTEKELESEK WHERE KEP_ID = :pid) AS RATE
                FROM FELHASZNALOK, KEPEK
                WHERE FELHASZNALOK.ID = KEPEK.FELH_ID 
                AND KEPEK.ID = :pid';
            $stmt = oci_parse($con, $query);
            oci_bind_by_name($stmt, ':pid', $picture_id);
            oci_execute($stmt);
            $row = oci_fetch_array($stmt,  OCI_ASSOC + OCI_RETURN_NULLS);
            if (is_object($row['KEPFAJL'])) {
                    $owner = $row['NEV'];
                    $desc = $row['LEIRAS'];
                    $place = $row['HELYSZIN'];
                    $time = $row['FELTOLTES_IDEJE'];
                    $blob = $row['KEPFAJL']->load();
                    $rating = (is_null($row['RATE']) ? 0 : $row['RATE']);
                    $pic = new Picture($picture_id, null, $desc, $time, $place, $blob, $owner, $rating);
                    $row['KEPFAJL']->free();
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

        public function getAlbumsByUser(){
            $con  = oci_connect(constant('DB_USER'), constant('DB_PW'), 'localhost/XE','AL32UTF8');
            if (!$con) {
                $e = oci_error();
                trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
            }
            $query = 'SELECT ID, NEV, LEIRAS, (SELECT COUNT(KEPEK.ID) FROM KEPEK WHERE ALBUM_ID = ALBUMOK.ID) AS NUMOFPICS,
                    TO_CHAR(LETREHOZAS_IDEJE, \'YYYY/MM/DD HH24:MI:SS\') AS LETREHOZAS_IDEJE
                    FROM ALBUMOK WHERE FELH_ID=' . $_SESSION['userObject']->getId();
            $stmt = oci_parse($con, $query);
            $lines = oci_execute($stmt);
            $albumok = array();
            while ($row = oci_fetch_array($stmt, OCI_ASSOC + OCI_RETURN_NULLS)) {
                
                $id = $row["ID"];
                $name = $row["NEV"];
                $desc = $row["LEIRAS"];
                $date = $row["LETREHOZAS_IDEJE"];
                $numofpics = $row["NUMOFPICS"];
                $albumok[$id] = new Album($id, $name, $desc, $date, $numofpics);
            }
            oci_close($con);
            return $albumok;
        }

        public function getAlbumById($album_id){
             $con  = oci_connect(constant('DB_USER'), constant('DB_PW'), 'localhost/XE','AL32UTF8');
            if (!$con) {
                $e = oci_error();
                trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
            }
            $query = 'SELECT NEV, LEIRAS, (SELECT COUNT(KEPEK.ID) FROM KEPEK WHERE ALBUM_ID = ' . $album_id . ') AS NUMPICS,
                    TO_CHAR(LETREHOZAS_IDEJE, \'YYYY/MM/DD HH24:MI:SS\') AS LETREHOZAS_IDEJE
                    FROM ALBUMOK WHERE ID = ' . $album_id;
            $stmt = oci_parse($con, $query);
            oci_execute($stmt);
            $album = null;
            $row = oci_fetch_array($stmt, OCI_ASSOC + OCI_RETURN_NULLS);
            $id = $album_id;
            $name = $row["NEV"];
            $desc = $row["LEIRAS"];
            $date = $row["LETREHOZAS_IDEJE"];
            $numofpics = $row["NUMPICS"];
            $album = new Album($id, $name, $desc, $date, $numofpics);
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

        public function ratePicture($pic_id, $rate){
            $con  = oci_connect(constant('DB_USER'), constant('DB_PW'), 'localhost/XE','AL32UTF8');
            $query = 'DELETE FROM ERTEKELESEK WHERE KEP_ID=:kepid AND FELH_ID=:felhid';
            $stmt = oci_parse($con, $query);
            oci_bind_by_name($stmt, ':felhid', $_SESSION['userObject']->getId());
            oci_bind_by_name($stmt, ':kepid', $pic_id);
            oci_execute($stmt, OCI_NO_AUTO_COMMIT);
            $query = 'INSERT INTO ERTEKELESEK (FELH_ID, KEP_ID, ERTEKELES) VALUES (:felhid, :kepid, :rating)';
            $stmt = oci_parse($con, $query);
            oci_bind_by_name($stmt, ':felhid', $_SESSION['userObject']->getId());
            oci_bind_by_name($stmt, ':kepid', $pic_id);
            oci_bind_by_name($stmt, ':rating', $rate);
            $succed = oci_execute($stmt, OCI_NO_AUTO_COMMIT);
            if ($succed)
                oci_commit($con);
            else
                oci_rollback($con);

            oci_close($con);
            return $succed;
        }

        public function commentPicture($pic_id, $user_id, $comment)
        {
            $con  = oci_connect(constant('DB_USER'), constant('DB_PW'), 'localhost/XE','AL32UTF8');
            $query = 'INSERT INTO HOZZASZOLASOK (ID, MEGJEGYZES,IDOBELYEG, FELH_ID, KEP_ID) 
                        VALUES (comment_seq.nextval, :comment_bi, SYSDATE , :usr_bi, :pic_bi)';
            $stmt = oci_parse($con, $query);
            oci_bind_by_name($stmt, ':comment_bi', $comment);
            oci_bind_by_name($stmt, ':usr_bi', $user_id);
            oci_bind_by_name($stmt, ':pic_bi', $pic_id);
            oci_execute($stmt);
            return true;
        }

        public function getComments($pic_id){
            $con  = oci_connect(constant('DB_USER'), constant('DB_PW'), 'localhost/XE','AL32UTF8');
            $query = 'SELECT FELHASZNALONEV, MEGJEGYZES FROM BEJELENTKEZESI_ADATOK, HOZZASZOLASOK
                        WHERE HOZZASZOLASOK.FELH_ID = BEJELENTKEZESI_ADATOK.FELH_ID
                        AND HOZZASZOLASOK.KEP_ID = '.$pic_id;
            $stmt = oci_parse($con, $query);
            oci_execute($stmt);
            $comments=array();
            $i = 0;
            while ($row = oci_fetch_array($stmt, OCI_ASSOC + OCI_RETURN_NULLS)){
                $comments[$i++] = $row;
            }
            return $comments;
        }

        public function getCategories(){
            $con  = oci_connect(constant('DB_USER'), constant('DB_PW'), 'localhost/XE','AL32UTF8');
            $query = 'SELECT ID,KATEGORIA FROM KATEGORIAK';
            $categories = array();
            $stmt = oci_parse($con, $query);
            oci_execute($stmt);
            while($row = oci_fetch_array($stmt, OCI_ASSOC + OCI_RETURN_NULLS)){
                $categories[$row['ID']] = $row['KATEGORIA'];
            }
            return $categories;
        }
    }
?>
