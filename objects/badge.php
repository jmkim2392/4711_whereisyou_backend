<?php
class Badge{
 
    // database connection and table name
    private $conn;
    private $table_name = "badges";
 
    // object properties
    public $badgeId;
    public $badgeDesc;
    public $userId;
 
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }
}