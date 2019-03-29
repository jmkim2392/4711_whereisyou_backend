<?php 
    include_once '../config/database.php';
    include_once '../worker/dailyChallenge.php';

    // instantiate database and product object
    $database = new Database();
	$db = $database->getConnection();
	
    // initialize object
    $challengeObj = new Challenge($db);
    
    $challengeObj->generate_daily_challenges();
?>
