<?php 
    include_once '../config/database.php';
    include_once '../objects/challenge.php';
	include_once '../objects/helper.php';

    $min_lat = -90;
    $max_lat = 90;
    $min_long = -180;
    $max_long = 180;
    // instantiate database and product object
    $database = new Database();
	$db = $database->getConnection();
	
    // initialize object
    $challengeObj = new Challenge($db);
    $helper = new Helper();
    
    $request_method=$_SERVER["REQUEST_METHOD"];

    switch($request_method) {
        case 'GET':
            $challengeObj->date = $helper->get_current_date();

            $stmt = $challengeObj->get_daily_challenges();

            $num = $stmt->rowCount();

			if($num<1){
                for($i =0; $i< 5; $i++) {
                    $challengeObj->challengeId = $helper->getGUID();
                    $challengeObj->latitude = $helper->get_random_float($this->min_lat, $this->max_lat, 6);
                    $challengeObj->longitude = $helper->get_random_float($this->min_long, $this->max_long, 6);
                    $challengeObj->qNum = $i;
                    $challengeObj->generate_daily_challenges();
                }
            }
        
            $challenges_arr=array();
            $challenges_arr["records"]=array();
        
            $stmt = $challengeObj->get_daily_challenges();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                
                $challenge=array(
                    "challengeId" => $challengeId,
                    "latitude" => $latitude,
                    "longitude" => $longitude,
                    "date" => $date,
                    "qNum" => $qNum
                );
                array_push($challenges_arr["records"], $challenge);
            }
            http_response_code(200);
            echo json_encode($challenges_arr);
            break;
		default:
            // Invalid Request Method
            header("HTTP/1.0 405 Method Not Allowed");
		    break;
    }
?>
