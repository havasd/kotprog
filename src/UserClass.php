<?php
class User {
	private $con;
	private $stmt;
	private $name;
	private $email;
	private $country;
	private $city;
	//private $avatar;

	public function User($uid){
		$this->con  = oci_connect('admin', 'admin', 'localhost/XE','AL32UTF8');
		if (!$this->con) {
		    $e = oci_error();
		    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
		}
		$query='SELECT NEV,EMAIL,VAROS,ORSZAG 
				FROM FELHASZNALOK, VAROSOK 
				WHERE FELHASZNALOK.VAROS_ID = VAROSOK.ID 
				AND FELHASZNALOK.ID =' . $uid;
		$this->stmt = oci_parse($this->con,$query);
		oci_execute($this->stmt);
		oci_fetch_all($this->stmt, $result);
		$this->name = $result["NEV"][0];
		$this->email = $result["EMAIL"][0];
		$this->country = $result["VAROS"][0];
		$this->city = $result["ORSZAG"][0];
	}

	public function getName(){
		return $this->name;
	}

	public function getEmail(){
		return $this->email;
	}

	public function getCountry(){
		return $this->country;
	}

	public function getCity(){
		return $this->city;
	}

}
?>