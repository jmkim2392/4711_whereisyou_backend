<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    include_once './config/database.php';
    include_once './objects/badge.php';
	include_once './objects/helper.php';

    // instantiate database and product object
    $database = new Database();
	$db = $database->getConnection();
	
    // initialize object
	$badgeObj = new Score($db);
	$helper = new Helper();

	$request_method=$_SERVER["REQUEST_METHOD"];
	$headers = apache_request_headers();
	$id;
    $date;
    $badgeDesc;
	foreach ($headers as $header => $value) {
		if(strcasecmp($header, 'userId')==0) {
			$id = $value;
		} else if (strcasecmp($header, 'date')==0) {
			$date = $value;
		} else if (strcasecmp($header, 'badgeDesc')==0) {
			$badgeDesc = $value;
		}
	}
    switch($request_method) {
        case 'GET':
            if(!empty($id)) {
                $stmt = $badgeObj->get_user_badges($id);
            } else {
                $stmt = $badgeObj->get_daily_scores($date, $badgeDesc);
            }
            $num = $stmt->rowCount();
            break;
        case 'POST':
            break;
		default:
            // Invalid Request Method
            header("HTTP/1.0 405 Method Not Allowed");
		    break;
    }
?>
