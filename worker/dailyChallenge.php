<?php 
    include_once ($_SERVER['DOCUMENT_ROOT'] . "/config/database.php");
    include_once ($_SERVER['DOCUMENT_ROOT'] . "/objects/challenge.php");

    //include_once '../objects/challenge.php';

    // instantiate database and product object
    $database = new Database();
	$db = $database->getConnection();
	
    // initialize object
    $challengeObj = new Challenge($db);
    
    $challengeObj->generate_daily_challenges();
?>
