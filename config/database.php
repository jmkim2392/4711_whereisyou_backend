<?php
class Database{
 
    // specify your own database credentials
    private $host = "etdq12exrvdjisg6.cbetxkdyhwsb.us-east-1.rds.amazonaws.com";
    private $db_name = "d2moyd6fk8672okl";
    private $username = "gs2tvykuklxwbil0";
    private $password = "fu1xgtliegjg1iyj";
    public $conn;
 
    // get the database connection
    public function getConnection(){
 
        $this->conn = null;
 
        try{
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        }catch(PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
        }
 
        return $this->conn;
    }
}
?>