<?php
    class Album
    {
        private $m_id;
        private $m_name;
        private $m_desc;
        private $m_numofpics;
        private $m_createdate;

        public function Album($_id, $_name, $_desc, $_createdate, $_numofpics = 0){
            $this->m_id = $_id;
            $this->m_name = $_name;
            $this->m_desc = $_desc;
            $this->m_numofpics = $_numofpics;
            $this->m_createdate = $_createdate;
        }

        public function getId(){
            return $this->m_id;
        }

        public function setNumOfPics($num){
            $this->m_numofpics = $num;
        }
        
        public function getNumOfPics(){
            return $this->m_numofpics;
        }

        public function getName(){
            return $this->m_name;
        }

        public function getDescription(){
            return $this->m_desc;
        }

        public function getCreateDate(){
            return $this->m_createdate;
        }

        public function toString(){
            return 'Album id: ' . $this->m_id . 
            ' name: ' . $this->m_name . 
            ' description: ' . $this->m_desc . ' createdate: ' . $this->m_createdate;
        }
    }
?>