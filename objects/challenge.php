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
    public $date;
 
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }
}