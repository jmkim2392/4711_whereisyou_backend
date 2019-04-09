<?php
class Helper{

    public $highscoreBadge = "Highest Score of the Day";
    public $worstScoreBadge = "Worst Score of the Day";

    private $min_lat = 30;
    private $max_lat = 60;
    private $min_long = -135;
    private $max_long = -75;

    // constructor with $db as database connection
    public function __construct(){
    }

    public function get_current_date () {
        date_default_timezone_set('America/Los_Angeles');
        $date = date('Y-m-d');
        return $date;
    }

    public function get_random_float($min, $max, $decimals = 0) {
        $scale = pow(10, $decimals);
        return mt_rand($min * $scale, $max * $scale) / $scale;
    }
    
    public function getGUID(){
        if (function_exists('com_create_guid')){
            return com_create_guid();
        }
        else {
            mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45);// "-"
            $uuid = substr($charid, 0, 8).$hyphen
                .substr($charid, 8, 4).$hyphen
                .substr($charid,12, 4).$hyphen
                .substr($charid,16, 4).$hyphen
                .substr($charid,20,12);
            return $uuid;
        }
    }

    public function get_100_coordinates() {
        $coord_arr=array();
        $latitude_arr = array();
        $longitude_arr = array();

        for($i =0; $i< 100; $i++) {
            $latitude = $this->get_random_float($this->min_lat, $this->max_lat, 6);
            $longitude = $this->get_random_float($this->min_long, $this->max_long, 6);
            $latitude_arr[$i] = $latitude;
            $longitude_arr[$i] = $longitude;
        }
        $coord_arr = array("lat" => $latitude_arr, "long" => $longitude_arr);
        return $coord_arr;
    }

    public function generate_nearest_roads_query($coord_arr) {
        $google = "https://roads.googleapis.com/v1/nearestRoads?points=";
        $lat_arr = $coord_arr["lat"];
        $long_arr = $coord_arr["long"];

        $query = $google;

        for ($i=0; $i <100; $i++) {
            $query = $query.$lat_arr[$i].",".$long_arr[$i];
            if($i!=99) {
                $query = $query."|";
            }
        }
        return $query;
    }

    public function generate_streetview_query($lat,$long) {
        $google = "https://maps.googleapis.com/maps/api/streetview/metadata?location=";

        $query = $google.$lat.",".$long;

        return $query;
    }
}
?>