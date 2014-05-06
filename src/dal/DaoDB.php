<?php
    require_once $_SERVER['DOCUMENT_ROOT']."/kotprog/src/dbstrings.php";
    //require_once('/dbstrings.php');
    require_once $_SERVER['DOCUMENT_ROOT']."/kotprog/src/model/User.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/kotprog/src/model/Album.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/kotprog/src/model/Picture.php";

    class DaoDB
    {
        // data post
        public function updateUser($data){
            $con  = oci_connect(constant('DB_USER'), constant('DB_PW'), 'localhost/XE','AL32UTF8');
            $query = 'begin updateUserData(:bv_usr, :bv_name, :bv_email, :bv_country, :bv_city); end;';
            $stmt = oci_parse($con, $query);
            oci_bind_by_name($stmt, ':bv_usr', $_SESSION['userObject']->getId());
            oci_bind_by_name($stmt, ':bv_name', isset($data['name_new']) ? $data['name_new'] : $_SESSION['userObject']->getName());
            oci_bind_by_name($stmt, ':bv_email', isset($data['email_new']) ? $data['email_new'] : $_SESSION['userObject']->getEmail());
            oci_bind_by_name($stmt, ':bv_country', isset($data['country_new']) ? $data['country_new'] : $_SESSION['userObject']->getCountry());
            oci_bind_by_name($stmt, ':bv_city', isset($data['city_new']) ? $data['city_new'] : $_SESSION['userObject']->getCity());
            $suc = oci_execute($stmt);
            if ($suc) {
                if (isset($data['name_new']))
                    $_SESSION['userObject']->setName($data['name_new']);
                if (isset($data['email_new']))
                    $_SESSION['userObject']->setEmail($data['email_new']);
                if (isset($data['country_new']))
                    $_SESSION['userObject']->setCountry($data['country_new']);
                if (isset($data['city_new']))
                    $_SESSION['userObject']->setCity($data['city_new']);
                return true;
            } else {
                return false;
            }
        }

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
                $_SESSION['userObject']->setAvatar($blob);
                return true;
            } else {
                oci_close($con);
                return false;
            }
        }

        public function getUserPassword() {
            $con = oci_connect(constant('DB_USER'), constant('DB_PW'), 'localhost/XE','AL32UTF8');
            $query = 'SELECT JELSZO FROM BEJELENTKEZESI_ADATOK WHERE FELH_ID=:bv_felh_id';
            $stmt = oci_parse($con, $query);
            oci_bind_by_name($stmt, ':bv_felh_id', $_SESSION['userObject']->getId());
            oci_execute($stmt);
            $row = oci_fetch_array($stmt, OCI_RETURN_NULLS);
            return $row['JELSZO'];
        }

        public function updateUserPassword($pass){
            $con = oci_connect(constant('DB_USER'), constant('DB_PW'), 'localhost/XE','AL32UTF8');
            $query = 'UPDATE BEJELENTKEZESI_ADATOK SET JELSZO=:bv_passw WHERE FELH_ID=:bv_felh_id';
            $stmt = oci_parse($con, $query);
            oci_bind_by_name($stmt, ':bv_passw', $pass);
            oci_bind_by_name($stmt, ':bv_felh_id', $_SESSION['userObject']->getId());
            return oci_execute($stmt);
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
                      VALUES (image_seq.nextval, :descr, CURRENT_DATE, :place, EMPTY_BLOB(), :albid, :user_id, :cid)
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

        public function getPictures($from_index, $to_index, $category_id, $city_id, $orderby, $populardests = false){
            $query = '
                SELECT * FROM (   
                    SELECT NEV, KEPEK.ID, LEIRAS, HELYSZIN, KEPFAJL, KAT_ID,
                    TO_CHAR(FELTOLTES_IDEJE, \'YYYY/MM/DD HH24:MI:SS\') AS FELTOLTES_IDEJE,
                    (SELECT AVG(ERTEKELES) FROM ERTEKELESEK WHERE KEP_ID = KEPEK.ID) AS RATE
                    FROM FELHASZNALOK, KEPEK
                    WHERE FELHASZNALOK.ID = KEPEK.FELH_ID'.
                    (($category_id != "all") && ($category_id) ? ' AND KAT_ID = :category_id_bi ' : ''). 
                    (!is_null($city_id) ? " AND HELYSZIN LIKE '" . $city_id. "\\_%' escape '\\'" : '').
                    ($populardests ? " AND FELHASZNALOK.VAROS_ID NOT LIKE '" . $city_id. "\\_%' escape '\\'" : '').
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

        public function getNumOfPicsByCategory(){
            $con = oci_connect(constant('DB_USER'), constant('DB_PW'), 'localhost/XE','AL32UTF8');
            $query = 'SELECT KATEGORIAK.KATEGORIA AS KATEGORIA, COUNT(KEPEK.ID) AS NUM
            FROM KATEGORIAK, KEPEK 
            WHERE KATEGORIAK.ID = KEPEK.KAT_ID
            GROUP BY KATEGORIAK.KATEGORIA';
            $stmt = oci_parse($con, $query);
            oci_execute($stmt);
            $count = array();
            $i = 0;
            while ($row = oci_fetch_array($stmt,  OCI_ASSOC + OCI_RETURN_NULLS)) {
                $count[$i++] = $row;
            }
            return $count;
        }

        public function getNumOfPicsByUser(){
            $con = oci_connect(constant('DB_USER'), constant('DB_PW'), 'localhost/XE','AL32UTF8');
            $query = 'SELECT * FROM (SELECT FELHASZNALOK.ID AS ID, FELHASZNALOK.NEV AS NEV, COUNT(KEPEK.ID) AS PICNUM
            FROM FELHASZNALOK, KEPEK
            WHERE FELHASZNALOK.ID = KEPEK.FELH_ID
            GROUP BY FELHASZNALOK.ID, FELHASZNALOK.NEV ORDER BY PICNUM DESC)
            WHERE ROWNUM <= 10';
            $stmt = oci_parse($con, $query);
            oci_execute($stmt);
            $count = array();
            $i = 0;
            while ($row = oci_fetch_array($stmt,  OCI_ASSOC + OCI_RETURN_NULLS)) {
                $count[$i++] = $row;
            }
            return $count;
        }

        public function getUserPictureRating($usr_id){
            $con = oci_connect(constant('DB_USER'), constant('DB_PW'), 'localhost/XE','AL32UTF8');
            $query = 'SELECT AVG(ERTEKELESEK.ERTEKELES) AS ERTEK
                FROM ERTEKELESEK 
                WHERE KEP_ID IN (SELECT KEPEK.ID FROM KEPEK WHERE FELH_ID = :bv_usrid)';
            $stmt = oci_parse($con, $query);
            oci_bind_by_name($stmt, ':bv_usrid', $usr_id);
            oci_execute($stmt);
            $row = oci_fetch_array($stmt);
            return is_null($row['ERTEK']) ? '0' : $row['ERTEK'];
        }

        public function getNumOfTable($table){
            $con = oci_connect(constant('DB_USER'), constant('DB_PW'), 'localhost/XE','AL32UTF8');
            $query = 'SELECT COUNT(*) FROM ' . $table;
            $stmt = oci_parse($con, $query);
            oci_execute($stmt);
            $row = oci_fetch_array($stmt);
            return $row['COUNT(*)'];
        }
        
        public function getNumOfAlbumsByUserId($usr_id){
            $con = oci_connect(constant('DB_USER'), constant('DB_PW'), 'localhost/XE','AL32UTF8');
            $query = 'SELECT COUNT(ALBUMOK.ID) AS ALBNUM FROM ALBUMOK WHERE FELH_ID = :bv_usrid';
            $stmt = oci_parse($con, $query);
            oci_bind_by_name($stmt, ':bv_usrid', $usr_id);
            oci_execute($stmt);
            $row = oci_fetch_array($stmt);
            return $row['ALBNUM'];
        }

        public function getNumOfPicsByCities(){
            $con = oci_connect(constant('DB_USER'), constant('DB_PW'), 'localhost/XE','AL32UTF8');
            $query = 'SELECT * FROM (SELECT VAROSOK.VAROS as VAROS, VAROSOK.ORSZAG as ORSZAG, COUNT(VAROS_ID) as KEPEK_SZAMA 
                        FROM (
                        SELECT  REGEXP_SUBSTR(HELYSZIN,\'[^_]+\',1,1) AS VAROS_ID FROM KEPEK
                        ),VAROSOK
                        WHERE VAROS_ID = VAROSOK.ID
                        group by VAROS_ID, VAROSOK.VAROS, VAROSOK.ORSZAG
                        order by KEPEK_SZAMA DESC)
                        WHERE ROWNUM <= 10';
            $stmt = oci_parse($con, $query);
            oci_execute($stmt);
            $count = array();
            $i = 0;
            while ($row = oci_fetch_array($stmt,  OCI_ASSOC + OCI_RETURN_NULLS)) {
                $count[$i++] = $row;
            }
            return $count;
        }

        public function getNumOfPictures($category_id){
            $query = 'SELECT COUNT(*) FROM KEPEK '.(!is_null($category_id) ? 'WHERE KAT_ID = '.$category_id : "");
            $con = oci_connect(constant('DB_USER'), constant('DB_PW'), 'localhost/XE','AL32UTF8');
            $stmt = oci_parse($con, $query);
            oci_execute($stmt);
            $count = oci_fetch_array($stmt); 
            return $count['COUNT(*)'];
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

        //városok arcai
        public function getCityAlbums(){
            $con = oci_connect(constant('DB_USER'), constant('DB_PW'), 'localhost/XE','AL32UTF8');
            $query = '  SELECT VAROS_ID as ID,VAROSOK.VAROS as VAROS, VAROSOK.ORSZAG as ORSZAG, COUNT(VAROS_ID) as KEPEK_SZAMA 
                        FROM (
                        SELECT  REGEXP_SUBSTR(HELYSZIN,\'[^_]+\',1,1) AS VAROS_ID FROM KEPEK
                        ),VAROSOK
                        WHERE VAROS_ID = VAROSOK.ID
                        group by VAROS_ID, VAROSOK.VAROS, VAROSOK.ORSZAG
                        having COUNT(*) > 1
                        order by KEPEK_SZAMA DESC ';
            $stmt = oci_parse($con, $query);
            oci_execute($stmt);
            $result=array();
            $i=0;//LIKE '7835\_%' escape '\'
            while ($row = oci_fetch_array($stmt)){
                $result[$i]['ID'] = $row['ID'];
                $result[$i]['VAROS'] = $row['VAROS'];
                $result[$i]['ORSZAG'] = $row['ORSZAG'];
                $result[$i]['KEPEK_SZAMA'] = $row['KEPEK_SZAMA'];
                $query = "SELECT ID FROM KEPEK WHERE HELYSZIN LIKE '" . $row['ID']. "\\_%' escape '\\'";
                $stmt2 = oci_parse($con, $query);
                oci_execute($stmt2);
                oci_fetch_all($stmt2, $pics);
                $result[$i]['KEPEK'] = $pics;
                $i++;
            }
            oci_close($con);
            return $result;
        }

         public function getPopularDestinations(){
            $con = oci_connect(constant('DB_USER'), constant('DB_PW'), 'localhost/XE','AL32UTF8');
            $query = '  SELECT VAROS_ID as ID,VAROSOK.VAROS as VAROS, VAROSOK.ORSZAG as ORSZAG, COUNT(VAROS_ID) as KEPEK_SZAMA 
                        FROM (


                            SELECT  REGEXP_SUBSTR(HELYSZIN,\'[^_]+\',1,1) AS VAROS_ID FROM KEPEK,FELHASZNALOK
                            WHERE KEPEK.FELH_ID = FELHASZNALOK.ID
                            AND FELHASZNALOK.VAROS_ID != REGEXP_SUBSTR(HELYSZIN,\'[^_]+\',1,1)

                        ),VAROSOK
                        WHERE VAROS_ID = VAROSOK.ID
                        group by VAROS_ID, VAROSOK.VAROS, VAROSOK.ORSZAG
                        having COUNT(*) > 1
                        order by KEPEK_SZAMA DESC ';
            $stmt = oci_parse($con, $query);
            oci_execute($stmt);
            $result=array();
            $i=0;//LIKE '7835\_%' escape '\'
            while ($row = oci_fetch_array($stmt)){
                $result[$i]['ID'] = $row['ID'];
                $result[$i]['VAROS'] = $row['VAROS'];
                $result[$i]['ORSZAG'] = $row['ORSZAG'];
                $result[$i]['KEPEK_SZAMA'] = $row['KEPEK_SZAMA'];
                $query = "SELECT ID FROM KEPEK WHERE HELYSZIN LIKE '" . $row['ID']. "\\_%' escape '\\'";
                $stmt2 = oci_parse($con, $query);
                oci_execute($stmt2);
                oci_fetch_all($stmt2, $pics);
                $result[$i]['KEPEK'] = $pics;
                $i++;
            }
            oci_close($con);
            return $result;
        }

        public function getCityById($city_id){
            $con = oci_connect(constant('DB_USER'), constant('DB_PW'), 'localhost/XE','AL32UTF8');
            $query = "SELECT VAROS,ORSZAG FROM VAROSOK WHERE ID = ".$city_id;
            $stmt = oci_parse($con, $query);
            oci_execute($stmt);
            $city = oci_fetch_array($stmt);
            oci_close($con);
            return $city['VAROS'].", ".$city['ORSZAG'];
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

        //Picture($id, $category, $desc, $time, $place, $data, $owner, $rating)
       public function getPicturesByUser($album_id){
            if (!$album_id){
                $query = 'SELECT NEV, KEPEK.ID, LEIRAS, HELYSZIN, KEPFAJL, KATEGORIA,
                TO_CHAR(FELTOLTES_IDEJE, \'YYYY/MM/DD HH24:MI:SS\') AS FELTOLTES_IDEJE,
                (SELECT AVG(ERTEKELES) FROM ERTEKELESEK WHERE KEP_ID = KEPEK.ID) AS RATE
                FROM FELHASZNALOK, KEPEK, KATEGORIAK
                WHERE FELHASZNALOK.ID = KEPEK.FELH_ID AND (KEPEK.KAT_ID = KATEGORIAK.ID OR KEPEK.KAT_ID IS NULL)
                AND FELH_ID = ' . $_SESSION['userObject']->getId().' 
                AND ALBUM_ID IS NULL';
            } else {
                $query = 'SELECT NEV, KEPEK.ID, LEIRAS, HELYSZIN, KEPFAJL, KATEGORIA,
                TO_CHAR(FELTOLTES_IDEJE, \'YYYY/MM/DD HH24:MI:SS\') AS FELTOLTES_IDEJE,
                (SELECT AVG(ERTEKELES) FROM ERTEKELESEK WHERE KEP_ID = KEPEK.ID) AS RATE
                FROM FELHASZNALOK, KEPEK, KATEGORIAK
                WHERE FELHASZNALOK.ID = KEPEK.FELH_ID AND (KEPEK.KAT_ID = KATEGORIAK.ID OR KEPEK.KAT_ID IS NULL)
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
                    $category = $row['KATEGORIA'];
                    $desc = $row['LEIRAS'];
                    $place = $row['HELYSZIN'];
                    $time = $row['FELTOLTES_IDEJE'];
                    $blob = $row['KEPFAJL']->load();
                    $rating = (is_null($row['RATE']) ? 0 : $row['RATE']);
                    $pics[$id] = new Picture($id, $category , $desc, $time, $place, $blob, $owner, $rating);
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
           $query = 'SELECT NEV, KATEGORIA, LEIRAS, HELYSZIN, KEPFAJL, 
                TO_CHAR(FELTOLTES_IDEJE, \'YYYY/MM/DD HH24:MI:SS\') AS FELTOLTES_IDEJE,
                (SELECT AVG(ERTEKELES) FROM ERTEKELESEK WHERE KEP_ID = :pid) AS RATE
                FROM FELHASZNALOK, KEPEK, KATEGORIAK
                WHERE FELHASZNALOK.ID = KEPEK.FELH_ID AND (KEPEK.KAT_ID = KATEGORIAK.ID OR KEPEK.KAT_ID IS NULL)
                AND KEPEK.ID = :pid';
            $stmt = oci_parse($con, $query);
            oci_bind_by_name($stmt, ':pid', $picture_id);
            oci_execute($stmt);
            $row = oci_fetch_array($stmt,  OCI_ASSOC + OCI_RETURN_NULLS);
            $pic = null;
            if (is_object($row['KEPFAJL'])) {
                    $owner = $row['NEV'];
                    $category = $row['KATEGORIA'];
                    $desc = $row['LEIRAS'];
                    $place = $row['HELYSZIN'];
                    $time = $row['FELTOLTES_IDEJE'];
                    $blob = $row['KEPFAJL']->load();
                    $rating = (is_null($row['RATE']) ? 0 : $row['RATE']);
                    $pic = new Picture($picture_id, $category, $desc, $time, $place, $blob, $owner, $rating);
                    $row['KEPFAJL']->free();
            }
            $stmt = null;
            oci_close($con);
            return $pic;
        }

        public function getPictureTileById($pic_id){
            $con  = oci_connect(constant('DB_USER'), constant('DB_PW'), 'localhost/XE','AL32UTF8');
            $query = 'SELECT KEPFAJL FROM KEPEK WHERE ID = '.$pic_id;
            $stmt = oci_parse($con, $query);
            oci_execute($stmt);
            $row = oci_fetch_array($stmt, OCI_ASSOC + OCI_RETURN_NULLS);
            if (is_object($row['KEPFAJL'])) {
                $blob = $row['KEPFAJL']->load();
                $row['KEPFAJL']->free();
            }
            oci_close($con);
            $picture = imagecreatefromstring(base64_decode($blob));
            $new_width = 250;
            $new_height = 120;
            $picture_tile = imagecreatetruecolor(250, 120);
            $width = imagesx($picture);
            $height = imagesy($picture);
            imagecopyresized($picture_tile, $picture, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            ob_start();
            imagejpeg($picture_tile);
            imagedestroy($picture_tile);
            $picture_tile = ob_get_clean();
            return base64_encode($picture_tile);
        }


        public function updatePicture($id, $desc, $alb_id, $cat_id, $place = 0){
            if ($alb_id = "null")
                $alb_id = null;
            echo "id: ".$id."  leiras: ".$desc."  albid:  ". $alb_id."  catid:". $cat_id." place: ". $place;
            $con  = oci_connect(constant('DB_USER'), constant('DB_PW'), 'localhost/XE','AL32UTF8');
            $query = 'UPDATE KEPEK SET LEIRAS = :bv_desc, ALBUM_ID = :bv_albid, KAT_ID = :bv_catid WHERE ID = :bv_id';
            $stmt = oci_parse($con, $query);
            oci_bind_by_name($stmt, ':bv_id', $id);
            oci_bind_by_name($stmt, ':bv_desc', $desc);
            oci_bind_by_name($stmt, ':bv_albid', $alb_id);
            oci_bind_by_name($stmt, ':bv_catid', $cat_id);
            // oci_bind_by_name($stmt, ':bv_place', $place); // TODO
            return oci_execute($stmt);
        }


        public function updateAlbum($id, $name, $desc){
            $con  = oci_connect(constant('DB_USER'), constant('DB_PW'), 'localhost/XE','AL32UTF8');
            $query = 'UPDATE ALBUMOK SET NEV=:bv_name, LEIRAS=:bv_leiras WHERE ID=:bv_id';
            $stmt = oci_parse($con, $query);
            oci_bind_by_name($stmt, ':bv_name', $name);
            oci_bind_by_name($stmt, ':bv_leiras', $desc);
            oci_bind_by_name($stmt, ':bv_id', $id);
            return oci_execute($stmt);;
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

        public function commentPicture($pic_id, $user_id, $comment, $answer)
        {
            $con  = oci_connect(constant('DB_USER'), constant('DB_PW'), 'localhost/XE','AL32UTF8');
            $query = 'INSERT INTO HOZZASZOLASOK (ID, MEGJEGYZES,IDOBELYEG, FELH_ID, KEP_ID, VALASZ_ID) 
                        VALUES (comment_seq.nextval, :comment_bi, CURRENT_DATE , :usr_bi, :pic_bi, :bv_answer)';
            $stmt = oci_parse($con, $query);
            oci_bind_by_name($stmt, ':comment_bi', $comment);
            oci_bind_by_name($stmt, ':usr_bi', $user_id);
            oci_bind_by_name($stmt, ':pic_bi', $pic_id);
            oci_bind_by_name($stmt, ':bv_answer', $answer);
            oci_execute($stmt);
            oci_close($con);
            return true;
        }

        public function updateComment($comment_id, $text){
            $con = oci_connect(constant('DB_USER'), constant('DB_PW'), 'localhost/XE','AL32UTF8');
            $query = 'UPDATE HOZZASZOLASOK SET MEGJEGYZES = :bv_text WHERE ID = :bv_comm_id';
            $stmt = oci_parse($con, $query);
            oci_bind_by_name($stmt, ':bv_text', $text);
            oci_bind_by_name($stmt, ':bv_comm_id', $comment_id);
            return oci_execute($stmt);
        }

        public function deleteComment($comment_id){
            $con = oci_connect(constant('DB_USER'), constant('DB_PW'), 'localhost/XE','AL32UTF8');
            $query = 'DELETE FROM HOZZASZOLASOK WHERE ID = :bv_comm_id';
            $stmt = oci_parse($con, $query);
            oci_bind_by_name($stmt, ':bv_comm_id', $comment_id);
            return oci_execute($stmt);
        }

        public function getAnswersForComment($comment_id){
            $con  = oci_connect(constant('DB_USER'), constant('DB_PW'), 'localhost/XE','AL32UTF8');
            $query = 'SELECT HOZZASZOLASOK.ID AS ID, NEV, MEGJEGYZES, FELH_ID,
                        TO_CHAR(IDOBELYEG, \'YYYY/MM/DD HH24:MI:SS\') AS IDOBELYEG
                        FROM FELHASZNALOK, HOZZASZOLASOK
                        WHERE HOZZASZOLASOK.FELH_ID = FELHASZNALOK.ID
                        AND HOZZASZOLASOK.VALASZ_ID = :bv_commid ORDER BY IDOBELYEG ASC';
            $stmt = oci_parse($con, $query);
            oci_bind_by_name($stmt, ':bv_commid', $comment_id);
            oci_execute($stmt);
            oci_close($con);
            $comments = array();
            $i = 0;
            while ($row = oci_fetch_array($stmt, OCI_ASSOC + OCI_RETURN_NULLS)){
                $comments[$i++] = $row;
            }
            return $comments;
        }

        public function getComments($pic_id){
            $con  = oci_connect(constant('DB_USER'), constant('DB_PW'), 'localhost/XE','AL32UTF8');
            $query = 'SELECT HOZZASZOLASOK.ID AS ID, NEV, MEGJEGYZES, FELH_ID,
                        TO_CHAR(IDOBELYEG, \'YYYY/MM/DD HH24:MI:SS\') AS IDOBELYEG
                        FROM FELHASZNALOK, HOZZASZOLASOK
                        WHERE HOZZASZOLASOK.FELH_ID = FELHASZNALOK.ID AND HOZZASZOLASOK.VALASZ_ID IS NULL
                        AND HOZZASZOLASOK.KEP_ID = :bv_picid ORDER BY IDOBELYEG ASC';
            $stmt = oci_parse($con, $query);
            oci_bind_by_name($stmt, ':bv_picid', $pic_id);
            oci_execute($stmt);
            oci_close($con);
            $comments = array();
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
            oci_close($con);
            while($row = oci_fetch_array($stmt)){
                $categories[$row['ID']] = $row['KATEGORIA'];
            }
            return $categories;
        }

        public function deletePictureById($id){
            $con  = oci_connect(constant('DB_USER'), constant('DB_PW'), 'localhost/XE','AL32UTF8');
            $query = 'DELETE FROM KEPEK WHERE ID = ' . $id;
            $stmt = oci_parse($con, $query);
            return oci_execute($stmt);
            oci_close($con);
        }

        public function deleteAlbumById($id){
            $con  = oci_connect(constant('DB_USER'), constant('DB_PW'), 'localhost/XE','AL32UTF8');
            $query = 'DELETE FROM ALBUMOK WHERE ID = ' . $id;
            $stmt = oci_parse($con, $query);
            return oci_execute($stmt);
            oci_close($con);
        }

        public function getCountries(){
            $con  = oci_connect(constant('DB_USER'), constant('DB_PW'), 'localhost/XE','AL32UTF8');
            $query = 'SELECT DISTINCT ORSZAG FROM VAROSOK';
            $stmt = oci_parse($con, $query);
            oci_execute($stmt);
            oci_close($con);
            $i = 0;
            while ($tmp = oci_fetch_array($stmt)){
                $countries[$i] = $tmp['ORSZAG'];
                $i+=1;
            }

            return $countries;
        }

        public function getCities(){
            $con  = oci_connect(constant('DB_USER'), constant('DB_PW'), 'localhost/XE','AL32UTF8');
            $query = 'SELECT DISTINCT VAROS FROM VAROSOK';
            $stmt = oci_parse($con, $query);
            oci_execute($stmt);
            $i = 0;
            while ($tmp = oci_fetch_array($stmt)){
                $cities[$i] = $tmp['VAROS'];
                $i+=1;
            }
            return $cities;
            die(var_dump($cities));
            oci_close($con);
        }

        public function getCityId($city,$country){
            $con  = oci_connect(constant('DB_USER'), constant('DB_PW'), 'localhost/XE','AL32UTF8');
            $query = 'SELECT ID FROM VAROSOK WHERE VAROS = :city AND ORSZAG = :country';
            $stmt = oci_parse($con, $query);
            oci_bind_by_name($stmt, ':city', $city);
            oci_bind_by_name($stmt, ':country', $country);
            oci_execute($stmt);
            $tmp = oci_fetch_array($stmt);
            oci_close($con);
            return $tmp['ID'];
        }

        public function addCity($city, $country){
            $con  = oci_connect(constant('DB_USER'), constant('DB_PW'), 'localhost/XE','AL32UTF8');
            $query = 'INSERT INTO VAROSOK VALUES (city_seq.nextval, :city, :country)';
            $stmt = oci_parse($con, $query);
            oci_bind_by_name($stmt, ':city', $city);
            oci_bind_by_name($stmt, ':country', $country);
            oci_execute($stmt);
            $query = 'SELECT ID FROM VAROSOK WHERE VAROS = :city AND ORSZAG = :country';
            $stmt = oci_parse($con, $query);
            oci_bind_by_name($stmt, ':city', $city);
            oci_bind_by_name($stmt, ':country', $country);
            oci_execute($stmt);
            $tmp = oci_fetch_array($stmt);
            return $tmp['ID'];
            oci_close($con);
        }
    }


    if (isset($_POST)){
        $controller = new DaoDB();
        if (isset($_POST['getThumb'])){
            echo "data:image/jpeg;base64,".$controller->getPictureTileById($_POST['getThumb']);
            exit();
        } 
        if (isset($_POST['deletePicture'])) {
            $controller->deletePictureById($_POST['deletePicture']);
            exit();
        }
        if (isset($_POST['deleteAlbum'])) {
            $controller->deleteAlbumById($_POST['deleteAlbum']);
            exit();
        }
        if (isset($_POST['getCities'])){
            echo json_encode($controller->getCities());
        }

        if (isset($_POST['getCountries'])){
            echo json_encode($controller->getCountries());
        }
        if (isset($_POST['getCityId'])){
            echo ($controller->getCityId($_POST['getCityId'],$_POST['country']));
        }
        if (isset($_POST['addCity'])){
            echo ($controller->addCity($_POST['addCity'],$_POST['country']));
        }
    }
?>
