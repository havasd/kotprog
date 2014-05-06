<?php
	require_once $_SERVER['DOCUMENT_ROOT']."/kotprog/src/dbstrings.php";
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

		public function setName($name){
			$this->m_name = $name;
		}

		public function getName(){
			return $this->m_name;
		}

		public function setEmail($email){
			$this->m_email = $email;
		}

		public function getEmail(){
			return $this->m_email;
		}

		public function setCountry($country){
			$this->m_country = $country;
		}

		public function getCountry(){
			return $this->m_country;
		}

		public function setCity($city){
			$this->m_city = $city;
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
			$avatar = imagecreatefromstring(base64_decode($blob));
            $new_width = 250;
            $new_height = 250;
            $avatar_resized = imagecreatetruecolor(250, 250);
            $width = imagesx($avatar);
            $height = imagesy($avatar);
            imagecopyresized($avatar_resized, $avatar, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            ob_start();
            imagejpeg($avatar_resized);
            $avatar_resized = ob_get_clean();
			$this->m_avatar = base64_encode($avatar_resized);
		}
	}
?>
