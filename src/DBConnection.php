<?php
class DBConnection {
	private $con;
	private $stmt;

	public function DBConnection(){
		$this->con = oci_connect('admin', 'admin', 'localhost/XE','AL32UTF8');	
	}

	public function register($user){
		$query='begin register(:usr,:pwd,:name,:mail,:country,:city); end;';
		$this->stmt = oci_parse($this->con, $query);
		oci_bind_by_name($this->stmt, ':usr', $user['username']);
		oci_bind_by_name($this->stmt, ':pwd', $user['password']);
		oci_bind_by_name($this->stmt, ':name', $user['name']);
		oci_bind_by_name($this->stmt, ':mail', $user['email']);
		oci_bind_by_name($this->stmt, ':country', $user['country']);
		oci_bind_by_name($this->stmt, ':city', $user['city']);
		oci_execute($this->stmt);
	}

	public function isUsernameTaken($username){
		$tmp = 0;
		$query = "SELECT COUNT(FELHASZNALONEV) INTO :tmp FROM BEJELENTKEZESI_ADATOK WHERE FELHASZNALONEV =:nev";
		$this->stmt = oci_parse($this->con, $query);
		oci_bind_by_name($this->stmt, ":nev", $username);
		oci_bind_by_name($this->statement, ":tmp", $tmp);
		oci_execute($this->stmt);
		if ($tmp > 0){
			return true;
		} else {
			return false;
		}
	}

	public function verifyUser($username,$password){
		$user_id = 0;
		$query = 'begin verifyUser(:usr, :pwd, :uid); end;';
		$this->stmt = oci_parse($this->con, $query);
		oci_bind_by_name($this->stmt, ":usr", $username);
		oci_bind_by_name($this->stmt, ":pwd", $password);
		oci_bind_by_name($this->stmt, ":uid", $user_id,32);
		oci_execute($this->stmt);
		return $user_id;
	}
}
?>