<?php
class Challenge{
 
    // database connection and table name
    private $conn;
    private $table_name = "challenges";
    private $min_lat = -90;
    private $max_lat = 90;
    private $min_long = -180;
    private $max_long = 180;

    // object properties
    public $id;
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
        $today = $this->get_current_date();
        
        $query = "INSERT INTO " . $this->table_name . "(challengeId, latitude, longitude, date ,qNum)
        VALUES (:challengeId, :latitude, :longitude, :date, :qNum)";
        $stmt = $this->conn->prepare($query);

        for($i =0; $i< 5; $i++) {
            $id = guidv4();
            $lat = $this->get_random_float($this->min_lat, $this->max_lat, 6);
            $long = $this->get_random_float($this->min_long, $this->max_long, 6);

            $stmt->bindParam(":challengeId", $id);
            $stmt->bindParam(":latitude", $lat);
            $stmt->bindParam(":longitude", $long);
            $stmt->bindParam(":date", $today);
            $stmt->bindParam(":qNum", $i);

            $stmt->execute();
        }
    }

    private function get_current_date () {
        date_default_timezone_set('America/Los_Angeles');
        $date = date('Y-m-d');
        return $date;
    }

    private function get_random_float($min, $max, $decimals = 0) {
        $scale = pow(10, $decimals);
        return mt_rand($min * $scale, $max * $scale) / $scale;
    }
    
}