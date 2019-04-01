<?php

class Challenge{
 
    // database connection and table name
    private $conn;
    private $table_name = "challenges";
   
    // object properties
    public $challengeId;
    public $latitude;
    public $longitude;
    public $qNum;
    public $date;
 
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    public function generate_daily_challenges() {
        // need to implement nearest road otherwise, it can be anywhere.
        // google nearest roads api is not free $10
        // osm api is free, but need to look into how to use it
		// check if more than 0 record found

        $query = "INSERT INTO " . $this->table_name . "(challengeId, latitude, longitude, date ,qNum)
        VALUES (:challengeId, :latitude, :longitude, :date, :qNum)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":challengeId", $this->challengeId);
        $stmt->bindParam(":latitude", $this->latitude);
        $stmt->bindParam(":longitude", $this->longitude);
        $stmt->bindParam(":date", $this->date);
        $stmt->bindParam(":qNum", $this->qNum);

        $stmt->execute();
    }

    public function get_daily_challenges() {
        $date_query = "\""+ $this->date +"\"";
        $query = "SELECT * FROM ".$this->table_name . " WHERE date=".$date_query;
        echo $query;
        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // execute query
        $stmt->execute();

        return $stmt;
    }
}
?>