<?php
    class Picture
    {
        private $m_id;
        private $m_category;
        private $m_desc;
        private $m_time;
        private $m_place;
        private $m_data;
        private $m_owner;
        private $m_rating;

        public function Picture($id, $category, $desc, $time, $place, $data, $owner, $rating) {
            $this->m_id = $id;
            $this->m_category = $category;
            $this->m_desc = $desc;
            $this->m_time = $time;
            $this->m_place = $place;
            $this->m_data = $data;
            $this->m_owner = $owner;
            $this->m_rating = $rating;
        }

        public function getId() {
            return $this->m_id;
        }

        public function getCategory() {
            return $this->m_category;
        }

        public function getUploadTime() {
            return $this->m_time;
        }

        public function getDescription() {
            return $this->m_desc;
        }

        public function getPlace() {
            return $this->m_place;
        }
        public function getPictureBinary() {
            return $this->m_data;
        }

        public function getOwner(){
            return $this->m_owner;
        }

        public function getRating(){
            return $this->m_rating;
        }

        public function getPictureTileBinary(){
             $picture = imagecreatefromstring(base64_decode($this->m_data));
            $picture_tile = imagecreatetruecolor(250, 120);
            list($width, $height) = getimagesizefromstring(base64_decode($this->m_data));
            imagecopyresampled($picture_tile, $picture, 0, 0, 0, 0, 250, 120, $width, $height);
            imagejpeg($picture_tile, "tiletemp.jpg");
            $blob_tile = base64_encode(file_get_contents("tiletemp.jpg"));
            unlink("tiletemp.jpg");
            return $blob_tile;
        }
    }
?>
