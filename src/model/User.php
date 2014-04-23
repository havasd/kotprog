<?php
	require_once('dbstrings.php');
	require_once('Album.php');

	class User
	{
		private $m_id;
		private $m_name;
		private $m_email;
		private $m_country;
		private $m_city;
		private $m_avatar;

		public function User($id,$name,$email,$country,$city){
			$this->m_id = $id;
			$this->m_name = $name;
			$this->m_email = $email;
			$this->m_country = $country;
			$this->m_city = $city;
			$this->m_avatar = null;
		}

		public function getId(){
			return $this->m_id;
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
			$str = "User: id: " . $this->m_id . 
			" name: " . $this->m_name . 
			" email: " . $this->m_email . 
			" country: " . $this->m_country . 
			" city: " . $this->m_city . "</br>";
			return $str;
		}

		public function getAvatar(){
			return $this->m_avatar;
		}

		public function setAvatar($blob){
			$this->m_avatar = $blob;
		}

		

		

		
	}
?>
