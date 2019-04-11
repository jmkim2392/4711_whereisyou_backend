<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, userId, date, badgeDesc, key");
    
    include_once './config/database.php';
    include_once './objects/badge.php';
	include_once './objects/helper.php';

    // instantiate database and product object
    $database = new Database();
	$db = $database->getConnection();
	
    // initialize object
	$badgeObj = new Badge($db);
	$helper = new Helper();

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
				if(!empty($id)) {
					$stmt = $badgeObj->get_user_badges($id);
				} else if(!empty($date)) {
					$stmt = $badgeObj->get_daily_badge_by_type($date, $badgeDesc);
				} else {
					$stmt = $badgeObj->get_badges_by_type($badgeDesc);
				}
				$num = $stmt->rowCount();
				// check if more than 0 record found
				if($num>0){
					$badges_arr=array();
					$badges_arr["records"]=array();
				
					while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
						extract($row);
						
						$badge_item=array(
							"userId" => $userId,
							"badgeDesc" => $badgeDesc,
							"date" => $date,
						);
						array_push($badges_arr["records"], $badge_item);
					}
					http_response_code(200);
					echo json_encode($badges_arr);
				} else {
					http_response_code(404);
					echo json_encode(
						array("message" => "Badges not found.")
					);
				}
				break;
			case 'POST':
				// get posted data
				$data = json_decode(file_get_contents("php://input"));
				// make sure data is not empty
				if(!empty($data->userId) && !empty($data->badgeDesc)) {
				
					// set score property values
					$badgeObj->userId = $data->userId;
					$badgeObj->badgeDesc = $data->badgeDesc;
					$badgeObj->date = $helper->get_current_date();
				
					// create the score
					if ($badgeObj->add_badge()) {
						http_response_code(201);
						echo json_encode(array("message" => "Badge created."));
					} else {
						http_response_code(500);
						echo json_encode(array("message" => "Failed to create Badge."));
					}
				} else {
					http_response_code(400);
					echo json_encode(array("message" => "Unable to create Badge. Data is incomplete."));
				}
				break;
			default:
				// Invalid Request Method
				header("HTTP/1.0 405 Method Not Allowed");
				break;
		}
	} else {
		http_response_code(403);
					echo json_encode(
						array("message" => "Incorrect Api Key.")
					);
	}
?>
