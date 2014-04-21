<?php
    require_once('dal/DaoDB.php');

    class Controller
    {
        private $m_dao;

        public function Controller(){
                $this->m_dao = new DaoDB(); 
        }

        public function getUserById($id){
            return $this->m_dao->getUserById($id);
        }
        
        public function addUser($user){
            return $this->m_dao->addUser($user);
        }

        public function isUserNameTaken($username){
            return $this->m_dao->isUserNameTaken($username);
        }

        public function verifyUser($username, $password){
            return $this->m_dao->verifyUser($username, $password);
        }
    }
?>