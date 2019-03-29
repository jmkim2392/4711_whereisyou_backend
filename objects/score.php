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
    

    public function addScore() {
        // query to insert record
        $query = "INSERT INTO " . $this->table_name . " SET
        scoreId=:scoreId, userId=:userId, challengeId=:challengeId, score=:score, distance=:distance, date=:date";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->name=htmlspecialchars(strip_tags($this->scoreId));
        $this->price=htmlspecialchars(strip_tags($this->userId));
        $this->description=htmlspecialchars(strip_tags($this->challengeId));
        $this->category_id=htmlspecialchars(strip_tags($this->score));
        $this->created=htmlspecialchars(strip_tags($this->distance));
        $this->created=htmlspecialchars(strip_tags($this->date));

        // bind values
        $stmt->bindParam(":scoreId", $this->scoreId);
        $stmt->bindParam(":userId", $this->userId);
        $stmt->bindParam(":challengeId", $this->challengeId);
        $stmt->bindParam(":score", $this->score);
        $stmt->bindParam(":distance", $this->distance);
        $stmt->bindParam(":date", $this->date);

        // execute query
        if($stmt->execute()){
            return true;
        }

        return false;
    }
}