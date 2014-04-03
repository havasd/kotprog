<?php
class DBConnection {
	private $con;
	private $stmt;

	public function DBConnection(){
		$this->con = oci_connect('admin', 'admin', 'localhost/XE','AL32UTF8');	
	}

	public function register($user){
		$query='begin register(:usr,:pwd,:name,:mail,:country,:city); end;';
		$this->stmt = oci_parse($this->con, $query);
		oci_bind_by_name($this->stmt, ':usr', $user['username']);
		oci_bind_by_name($this->stmt, ':pwd', $user['password']);
		oci_bind_by_name($this->stmt, ':name', $user['name']);
		oci_bind_by_name($this->stmt, ':mail', $user['email']);
		oci_bind_by_name($this->stmt, ':country', $user['country']);
		oci_bind_by_name($this->stmt, ':city', $user['city']);
		oci_execute($this->stmt);
	}

	public function verifyUser($username,$password){

	}
}

$dbconn=new DBConnection();
$dbconn->register($_POST);
echo (json_encode(array("reg" =>"true")));
exit();
?>