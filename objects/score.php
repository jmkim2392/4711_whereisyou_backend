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
    public $distance;
    public $date;
 
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    public function get_scores($id=0) {
        $query="SELECT * FROM ".$this->table_name;
        if($id != 0)
        {
            $query.=" WHERE userId=".$id;
        }
        
        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // execute query
        $stmt->execute();
    
        return $stmt;
    }
    
    public function get_daily_scores($date) {
        $date_query = date($date);
        $query="SELECT * FROM ".$this->table_name . " WHERE date=".$date_query;
        
        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // execute query
        $stmt->execute();
    
        return $stmt;
    }



    public function addScore() {
        // query to insert record
        $query = "INSERT INTO " . $this->table_name . " SET
        scoreId=:scoreId, userId=:userId, challengeId=:challengeId, score=:score, distance=:distance, date=:date";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->scoreId= $this->getGUID();
        $this->userId=htmlspecialchars(strip_tags($this->userId));
        $this->challengeId=htmlspecialchars(strip_tags($this->challengeId));
        $this->score=htmlspecialchars(strip_tags($this->score));
        $this->distance=htmlspecialchars(strip_tags($this->distance));
        $this->date=htmlspecialchars(strip_tags($this->date));

        // bind values
        $stmt->bindParam(":scoreId", $this->scoreId);
        $stmt->bindParam(":userId", $this->userId);
        $stmt->bindParam(":challengeId", $this->challengeId);
        $stmt->bindParam(":score", $this->score);
        $stmt->bindParam(":distance", $this->distance);
        $stmt->bindParam(":date", date($this->date));

        // execute query
        if($stmt->execute()){
            return true;
        }
        return false;
    }

    private function getGUID(){
        if (function_exists('com_create_guid')){
            return com_create_guid();
        }
        else {
            mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45);// "-"
            $uuid = chr(123)// "{"
                .substr($charid, 0, 8).$hyphen
                .substr($charid, 8, 4).$hyphen
                .substr($charid,12, 4).$hyphen
                .substr($charid,16, 4).$hyphen
                .substr($charid,20,12)
                .chr(125);// "}"
            return $uuid;
        }
    }
}