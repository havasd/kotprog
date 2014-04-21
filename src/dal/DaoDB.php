<?php
    require_once('dbstrings.php');

    class DaoDB
    {
        public function getUserById($id){

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
    }
?>