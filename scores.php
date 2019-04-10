<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
	header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type,key, Accept, userId, date");
	
    include_once './config/database.php';
    include_once './objects/score.php';
	include_once './objects/helper.php';
	include_once './objects/badge.php';

    // instantiate database and product object
    $database = new Database();
	$db = $database->getConnection();
	
    // initialize object
	$scoreObj = new Score($db);
	$helper = new Helper();
	$badgeObj = new Badge($db);

	$request_method=$_SERVER["REQUEST_METHOD"];
	$headers = apache_request_headers();
	$id;
	$date;
	$key;
	$leaderboard;
	foreach ($headers as $header => $value) {
		if(strcasecmp($header, 'userId')==0) {
			$id = $value;
		} else if (strcasecmp($header, 'date')==0) {
			$date = $value;
		} else if(strcasecmp($header, 'key')==0) {
            $key = $value;
        } else if(strcasecmp($header, 'leaderboard')==0) {
			$leaderboard = $value;
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

				if (strcasecmp($leaderboard,'true')==0) {
					while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
						extract($row);
						
						if (array_key_exists($userId, $scores_arr)) {
							$userScore = $scores_arr[$userId][0]["score"] + $score;
							$score_item=array(
								"score" => $userScore,
								"date" => $date,
							);
							array_push($scores_arr[$userId][0], $score_item);
						} else {
							$scores_arr[$userId] = array();
							$score_item=array(
								"score" => $score,
								"date" => $date,
							);
							array_push($scores_arr[$userId], $score_item);
						}
					}
				} else {
					$scores_arr=array();
			
					while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
						extract($row);
						
						$score_item=array(
							"scoreId" => $scoreId,
							"userId" => $userId,
							"challengeId" => $challengeId,
							"score" => $score,
							"distance" => $distance,
							"date" => $date,
						);
						array_push($scores_arr, $score_item);
					}
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
					$badge = $scoreObj->checkScoreBadge();
					$badgeObj->userId = $scoreObj->userId;
					$badgeObj->date = $scoreObj->date;
					if ($badge == "top") {
						// delete today's highscore badge and reward this user
						$badgeObj->badgeDesc = $helper->highscoreBadge;
						$badgeObj->remove_badge();
						$badgeObj->add_badge();
					} else if ($badge == "worst") {
						// delete today's worstscore badge and reward this user
						$badgeObj->badgeDesc = $helper->worstScoreBadge;
						$badgeObj->remove_badge();
						$badgeObj->add_badge();
					}
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
