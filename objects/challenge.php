<?php
class Challenge{
 
    // database connection and table name
    private $conn;
    private $table_name = "challenges";
 
    // object properties
    public $id;
    public $challengeId;
    public $latitude;
    public $longitude;
 
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }
}