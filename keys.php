<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, userId, date, badgeDesc, key");
    
    include_once './config/database.php';
	include_once './objects/helper.php';
    include_once './config/keyhandler.php';

    // instantiate database and product object
    $database = new Database();
	$db = $database->getConnection();
	
    // initialize object
	$helper = new Helper();
    $keyHandler = new KeyHandler($db);

    $request_method=$_SERVER["REQUEST_METHOD"];
    
	$headers = apache_request_headers();
	$id;
    $date;
    $badgeDesc;
    $key;
	foreach ($headers as $header => $value) {
		if(strcasecmp($header, 'userId')==0) {
			$id = $value;
		} else if (strcasecmp($header, 'date')==0) {
			$date = $value;
		} else if (strcasecmp($header, 'badgeDesc')==0) {
			$badgeDesc = $value;
		} else if(strcasecmp($header, 'key')==0) {
            $key = $value;
        }
    }
	
	//check if key exists
 	// get extApp Access key
 	$keyHandler->keyName = "extApp";
    $apikey = $keyHandler->get_key();
    if(strcasecmp($apikey, $key)==0) {
		switch($request_method) {
			case 'GET':
                $keyHandler->keyName = "core";
                $coreKey = $keyHandler->get_key();
                http_response_code(200);
                echo json_encode($coreKey);
				break;
			default:
				// Invalid Request Method
				header("HTTP/1.0 405 Method Not Allowed");
				break;
		}
	} else {
		http_response_code(403);
        echo json_encode(
            array("message" => $apikey, "usent"=> $key)
        );
	}
?>
