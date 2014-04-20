<?php
	require_once('dbstrings.php');
	require_once('AlbumClass.php');

	class User
	{
		private $m_id;
		private $m_name;
		private $m_email;
		private $m_country;
		private $m_city;
		private $m_albums;
		private $m_avatar;

		public function User($uid){
			$this->m_id = $uid;
			$this->m_albums = array();
			$this->load();
			$this->loadAlbums();
			$this->m_avatar = $this->getAvatar();
		}

		private function load(){
			$con  = oci_connect(constant('DB_USER'), constant('DB_PW'), 'localhost/XE','AL32UTF8');
			if (!$con) {
			    $e = oci_error();
			    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
			}
			$query = 'SELECT NEV,EMAIL,VAROS,ORSZAG 
					FROM FELHASZNALOK, VAROSOK 
					WHERE FELHASZNALOK.VAROS_ID = VAROSOK.ID 
					AND FELHASZNALOK.ID =' . $this->m_id;
			$stmt = oci_parse($con, $query);
			oci_execute($stmt);
			oci_fetch_all($stmt, $result);
			$this->m_name = $result["NEV"][0];
			$this->m_email = $result["EMAIL"][0];
			$this->m_city = $result["VAROS"][0];
			$this->m_country = $result["ORSZAG"][0];
			oci_close($con);
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
			oci_bind_by_name($stmt, ':uid', $this->m_id);
			oci_bind_by_name($stmt, ':id', $album_id, 32);
			oci_bind_by_name($stmt, ':time', $album_time, 64);
			oci_execute($stmt);
			$album = new Album($album_id, $name, $desc, $album_time);
			$this->addAlbum($album);
			oci_close($con);
		}

		private function loadAlbums(){
			$con  = oci_connect(constant('DB_USER'), constant('DB_PW'), 'localhost/XE','AL32UTF8');
			if (!$con) {
			    $e = oci_error();
			    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
			}
			$query = 'SELECT ID, NEV, LEIRAS, TO_CHAR(LETREHOZAS_IDEJE, \'YYYY/MM/DD HH24:MI:SS\') AS LETREHOZAS_IDEJE FROM ALBUMOK WHERE FELH_ID=' . $this->m_id;
			$stmt = oci_parse($con, $query);
			oci_execute($stmt);
			while ($row = oci_fetch_array($stmt, OCI_ASSOC + OCI_RETURN_NULLS)) {
				$id = $row["ID"];
				$name = $row["NEV"];
				$desc = $row["LEIRAS"];
				$date = $row["LETREHOZAS_IDEJE"];
				$album = new Album($id, $name, $desc, $date);
				$this->addAlbum($album);
			}

			oci_close($con);
		}

		public function getAvatar(){
			$con = oci_connect(constant('DB_USER'), constant('DB_PW'), 'localhost/XE','AL32UTF8');
			$query = 'SELECT AVATAR FROM FELHASZNALOK WHERE ID = :id';
	        $stmt = oci_parse($con, $query);
	        oci_bind_by_name($stmt, ':id', $this->m_id);
	        //oci_set_action($this->m_con, $query);
	        oci_execute($stmt);
	        $row = oci_fetch_array($stmt, OCI_RETURN_NULLS);
	        $blob = null;
	        if (is_object($row['AVATAR'])) {
	            $blob = $row['AVATAR']->load();
	            $row['AVATAR']->free();
	            $stmt = null;
		        oci_close($con);
		        return $blob;
	        } else {
	        	oci_close($con);
	        	return false;
	        }
		}

		public function setAvatar($blob){
			$con  = oci_connect(constant('DB_USER'), constant('DB_PW'), 'localhost/XE','AL32UTF8');
			$query = 'UPDATE FELHASZNALOK
					SET AVATAR = EMPTY_BLOB()
					WHERE ID = :id
					RETURNING AVATAR INTO :myblob';
			$stmt = oci_parse($con, $query);
			$dlob = oci_new_descriptor($con, OCI_D_LOB);
		    oci_bind_by_name($stmt, ':myblob', $dlob, -1, OCI_B_BLOB);
	        oci_bind_by_name($stmt, ':id', $this->m_id);
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

		public function uploadPicture($blob){
			$cid = null;
			$albid = null;
			$desc = 'teszt';
			$place = 'teszt2';

			$con = oci_connect(constant('DB_USER'), constant('DB_PW'), 'localhost/XE','AL32UTF8');
			$query = "INSERT INTO KEPEK (ID, LEIRAS, FELTOLTES_IDEJE, HELYSZIN, KEPFAJL, ALBUM_ID, FELH_ID, KAT_ID)
					  VALUES (image_seq.nextval, :descr, SYSDATE, :place, EMPTY_BLOB(), :albid, :user_id, :cid)
					  RETURNING KEPFAJL INTO :myblob";
			/*$query = "INSERT INTO KEPEK (ID, KEPFAJL, FELH_ID)
					  VALUES (image_seq.nextval, EMPTY_BLOB(), :user_id)
					  RETURNING KEPFAJL INTO :myblob";*/

			$stmt = oci_parse($con, $query);
			$dlob = oci_new_descriptor($con, OCI_D_LOB);
			oci_bind_by_name($stmt, ':myblob', $dlob, -1, OCI_B_BLOB);
			oci_bind_by_name($stmt, ':user_id', $this->m_id);
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

		public function getPictures(){
			$con = oci_connect(constant('DB_USER'), constant('DB_PW'), 'localhost/XE','AL32UTF8');
			$query = 'SELECT ID, LEIRAS, HELYSZIN, KEPFAJL, ALBUM_ID, KAT_ID FROM KEPEK WHERE FELH_ID = ' . $this->m_id;
	        $stmt = oci_parse($con, $query);
	        //oci_set_action($this->m_con, $query);
	        oci_execute($stmt);
	        $pics = array();
	        while (($row = oci_fetch_array($stmt, OCI_BOTH)) != false) {
		        if (is_object($row['KEPFAJL'])) {
		            $blob = $row['KEPFAJL']->load();
		            $row['KEPFAJL']->free();
		            $pics[$row['ID']] = $blob;
			    }
			}

		    $stmt = null;
		    oci_close($con);
		    return $pics;
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

		public function getAlbums(){
			return $this->m_albums;
		}

		public function getAlbumById($id){
			return $this->m_albums[$id];
		}

		private function addAlbum($album) {
			$this->m_albums[$album->getId()] = $album;
		}

		private function removeAlbum($id){
			unset($this->m_albums[$id]);
		}

		public function toString(){
			$str = "User: id: " . $this->m_id . 
			" name: " . $this->m_name . 
			" email: " . $this->m_email . 
			" country: " . $this->m_country . 
			" city: " . $this->m_city . "</br>";
			$str = $str . count($this->m_albums) . "</br>";
			foreach ($this->m_albums as $val) {
				$str = $str . $val->toString() . "</br>";
			}

			return $str;
		}
	}
?>