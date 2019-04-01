<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
	header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, userId, date");
	
    include_once './config/database.php';
    include_once './objects/score.php';
	include_once './objects/helper.php';

    // instantiate database and product object
    $database = new Database();
	$db = $database->getConnection();
	
    // initialize object
	$scoreObj = new Score($db);
	$helper = new Helper();

	$request_method=$_SERVER["REQUEST_METHOD"];
	$headers = apache_request_headers();
	$id;
	$date;
	foreach ($headers as $header => $value) {
		if(strcasecmp($header, 'userId')==0) {
			$id = $value;
		} else if (strcasecmp($header, 'date')==0) {
			$date = $value;
		}
	}
    switch($request_method) {
		case 'GET':
            if(!empty($id)) {
                $stmt = $scoreObj->get_scores($id);
            } else {
            	$stmt = $scoreObj->get_daily_scores($date);
			}
			
			$num = $stmt->rowCount();

			// check if more than 0 record found
			if($num>0){
			
				$scores_arr=array();
				$scores_arr["records"]=array();
			
				while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
					extract($row);
					
					$score_item=array(
						"scoreId" => $scoreId,
						"challengeId" => $challengeId,
						"score" => $score,
						"distance" => $distance,
						"date" => $date,
					);
					array_push($scores_arr["records"], $score_item);
				}
				http_response_code(200);
				echo json_encode($scores_arr);
			} else {
				http_response_code(404);
				echo json_encode(
					array("message" => "Scores not found.")
				);
			}
            break;
        case 'POST':
            // get posted data
			$data = json_decode(file_get_contents("php://input"));

			// make sure data is not empty
			if(!empty($data->userId) && !empty($data->challengeId) && !empty($data->score) && !empty($data->distance)) {
			
				// set score property values
				$scoreObj->scoreId = $helper->getGUID();
				$scoreObj->userId = $data->userId;
				$scoreObj->challengeId = $data->challengeId;
				$scoreObj->score = $data->score;
				$scoreObj->distance = $data->distance;
				$scoreObj->date = $helper->get_current_date();
			
				// create the score
				if ($scoreObj->addScore()) {
					http_response_code(201);
					echo json_encode(array("message" => "Score created."));
				} else {
					http_response_code(500);
					echo json_encode(array("message" => "Failed to create score."));
				}
			} else {
				http_response_code(400);
				echo json_encode(array("message" => "Unable to create score. Data is incomplete."));
			}
            break;
		default:
            // Invalid Request Method
            header("HTTP/1.0 405 Method Not Allowed");
		    break;
    }
?>
