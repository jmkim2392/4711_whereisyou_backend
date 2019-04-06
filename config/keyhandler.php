<?php

class KeyHandler{
 
    // database connection and table name
    private $conn;
    private $table_name = "apikeys";
   
    // object properties
    public $keyName;
    public $keyCode;
 
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    public function add_key() {
        $query = "INSERT INTO " . $this->table_name . "(keyName, keyCode)
        VALUES (:keyName, :keyCode)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":keyName", $this->keyName);
        $stmt->bindParam(":keyCode", $this->keyCode);

        $stmt->execute();
    }

    public function get_key() {
        $name = $this->keyName;
        $query = "SELECT * FROM ".$this->table_name . " WHERE keyName=\"".$name."\"";
        
        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // execute query
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            extract($row);
            $apiKeyCode = $keyCode;
        }
        return $apiKeyCode;
    }
}
?>