<?php
class Score{
 
    // database connection and table name
    private $conn;
    private $table_name = "scores";
 
    // object properties
    public $scoreId;
    public $userId;
    public $challengeId;
    public $score;
 
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }
}