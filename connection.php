<?php
Class dbObj{
	/* Database connection start */
	var $servername = "etdq12exrvdjisg6.cbetxkdyhwsb.us-east-1.rds.amazonaws.com";
	var $username = "gs2tvykuklxwbil0";
	var $password = "fu1xgtliegjg1iyj";
	var $dbname = "d2moyd6fk8672okl";
	var $conn;
	function getConnstring() {
		$con = mysqli_connect($this->servername, $this->username, $this->password, $this->dbname) or die("Connection failed: " . mysqli_connect_error());
 
		/* check connection */
		if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
		} else {
		$this->conn = $con;
		}
		return $this->conn;
	}
}
 
?>