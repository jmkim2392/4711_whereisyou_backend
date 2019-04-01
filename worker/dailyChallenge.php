<?php 
    include_once '../config/database.php';
    include_once '../objects/challenge.php';

    //include_once '../objects/challenge.php';
    $min_lat = -90;
    $max_lat = 90;
    $min_long = -180;
    $max_long = 180;
    // instantiate database and product object
    $database = new Database();
	$db = $database->getConnection();
	
    // initialize object
    $challengeObj = new Challenge($db);
    
    // ->get_random_float($this->min_lat, $this->max_lat, 6)
    // ->get_random_float($this->min_long, $this->max_long, 6)
    $challengeObj->generate_daily_challenges();
?>
