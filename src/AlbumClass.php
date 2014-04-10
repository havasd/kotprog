<?php
class Album {

    private $m_id;
    private $m_name;
    private $m_desc;
    private $m_createdate;

    public function Album($_id, $_name, $_desc, $_createdate){
        $m_id = $_id;
        $m_name = $_name;
        $m_createdate = $_createdate;
    }

    public function getName(){
        return $this->m_name;
    }

    public function getDescription(){
        return $this->m_description;
    }

    public function getCreateDate(){
        return $this->m_createdate;
    }

    public function toString(){
        return 'Album id: ' . $this->m_id . ' name: ' . $this->m_name . ' description: ' . $this->m_desc . ' createdate: ' $this->m_createdate;
    }
}
?>