<?php
class User {
	private $m_con;
	private $m_id;
	private $m_stmt;
	private $m_name;
	private $m_email;
	private $m_country;
	private $m_city;
	private $m_albums;
	//private $avatar;

	public function User($uid){
		$this->m_con  = oci_connect('havas', '123456', 'localhost/XE','AL32UTF8');
		if (!$this->m_con) {
		    $e = oci_error();
		    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
		}
		$this->m_id = $uid;
		$this->m_albums = array();
		$this->load();
	}

	private function load(){
		$query = 'SELECT NEV,EMAIL,VAROS,ORSZAG 
				FROM FELHASZNALOK, VAROSOK 
				WHERE FELHASZNALOK.VAROS_ID = VAROSOK.ID 
				AND FELHASZNALOK.ID =' . $this->m_id;
		$this->m_stmt = oci_parse($this->m_con, $query);
		oci_execute($this->m_stmt);
		oci_fetch_all($this->m_stmt, $result);
		$this->m_name = $result["NEV"][0];
		$this->m_email = $result["EMAIL"][0];
		$this->m_country = $result["VAROS"][0];
		$this->m_city = $result["ORSZAG"][0];
	}

	private function loadAlbums(){
		$query = 'SELECT ID, NEV, LEIRAS, LETREHOZAS_IDEJE FROM ALBUMOK WHERE FELH_ID=' . $id;
		$this->m_stmt = oci_parse($this->m_conn, $query);
		oci_execute($this->m_stmt);

		while ($row = oci_fetch_array($this->stmt, OCI_ASSOC + OCI_RETURN_NULLS)) {
			$id = $row["ID"];
		}
	}

	public function getName(){
		return $this->m_name;
	}

	public function getEmail(){
		return $this->m_email;
	}

	public function getCountry(){
		return $this->m_country;
	}

	public function getCity(){
		return $this->m_city;
	}

	public function toString(){
		return "User: id: " . $this->m_id . 
		" name: " . $this->m_name . 
		" email: " . $this->m_email . 
		" country: " . $this->m_country . 
		" city: " . $this->m_city;
	}

}
?>