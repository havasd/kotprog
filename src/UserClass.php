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
	private $m_avatar;

	public function User($uid){
		$this->m_con  = oci_connect('admin', 'admin', 'localhost/XE','AL32UTF8');
		if (!$this->m_con) {
		    $e = oci_error();
		    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
		}
		$this->m_id = $uid;
		$this->m_albums = array();
		$this->load();
		$this->m_avatar = $this->getAvatar();
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
		$this->m_city = $result["VAROS"][0];
		$this->m_country = $result["ORSZAG"][0];
	}

	private function loadAlbums(){
		$query = 'SELECT ID, NEV, LEIRAS, LETREHOZAS_IDEJE FROM ALBUMOK WHERE FELH_ID=' . $id;
		$this->m_stmt = oci_parse($this->m_con, $query);
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

	public function getAvatar(){
		$this->m_con = oci_connect('admin', 'admin', 'localhost/XE','AL32UTF8');
		$query = 'SELECT AVATAR FROM FELHASZNALOK WHERE ID = :id';
        $this->m_stmt = oci_parse($this->m_con, $query);
        oci_bind_by_name($this->m_stmt, ':id', $this->m_id);
        //oci_set_action($this->m_con, $query);
        oci_execute($this->m_stmt);
        $row = oci_fetch_array($this->m_stmt, OCI_RETURN_NULLS);
        $blob = null;
        if (is_object($row['AVATAR'])) {
            $blob = $row['AVATAR']->load();
            $row['AVATAR']->free();
            $this->m_stmt = null;
	        oci_close($this->m_con);
	        return $blob;
        }
        else {
        	return false;
        }
	}

	public function setAvatar($blob){
		$this->m_con  = oci_connect('admin', 'admin', 'localhost/XE','AL32UTF8');
		$query = 'UPDATE FELHASZNALOK 
				SET AVATAR = EMPTY_BLOB() 
				WHERE ID = :id 
				RETURNING AVATAR INTO :myblob';
		$this->m_stmt = oci_parse($this->m_con, $query);
		$dlob = oci_new_descriptor($this->m_con, OCI_D_LOB);
	    oci_bind_by_name($this->m_stmt, ':myblob', $dlob, -1, OCI_B_BLOB);
        oci_bind_by_name($this->m_stmt, ':id', $this->m_id);
        oci_execute($this->m_stmt, OCI_NO_AUTO_COMMIT);
        if ($dlob->save($blob)) {
            oci_commit($this->m_con);
	        oci_close($this->m_con);
	        return true;
        }
        else {
        	return false;
        }
	}


}
?>