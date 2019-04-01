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
        $today = $this->date;
        
        $query = "INSERT INTO " . $this->table_name . "(challengeId, latitude, longitude, date ,qNum)
        VALUES (:challengeId, :latitude, :longitude, :date, :qNum)";
        $stmt = $this->conn->prepare($query);

        for($i =0; $i< 5; $i++) {
            $challengeId = $this->challengeId;
            $lat = $this->latitude;
            $long = $this->longitude;

            $stmt->bindParam(":challengeId", $challengeId);
            $stmt->bindParam(":latitude", $lat);
            $stmt->bindParam(":longitude", $long);
            $stmt->bindParam(":date", $today);
            $stmt->bindParam(":qNum", $i);

            $stmt->execute();
        }
    }
}