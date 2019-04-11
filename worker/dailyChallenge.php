<?php 
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
	header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, userId, date, key");
    
    include_once '../config/database.php';
    include_once '../config/keyhandler.php';
    include_once '../objects/challenge.php';
    include_once '../objects/helper.php';

    // instantiate database and product object
    $database = new Database();
	$db = $database->getConnection();
	
    // initialize object
    $challengeObj = new Challenge($db);
    $helper = new Helper();
    $keyHandler = new KeyHandler($db);
    
    $request_method=$_SERVER["REQUEST_METHOD"];

    // get all headers
    $headers = apache_request_headers();
    $key;
    foreach ($headers as $header => $value) {
        if(strcasecmp($header, 'key')==0) {
            $key = $value;
        }
    }
    
    $challengeCount=0;
    $streetviewCoord= array();

    //check if key exists
 	// get extApp Access key
 	$keyHandler->keyName = "extApp";
    $apikey = $keyHandler->get_key();
    if(strcasecmp($apikey, $key)==0) {

        switch($request_method) {
            case 'GET':
                $challengeObj->date = $helper->get_current_date();
                $stmt = $challengeObj->get_daily_challenges();
                $num = $stmt->rowCount();

                if($num<1){

                    // get google key
                    $keyHandler->keyName = "google";
                    $key = $keyHandler->get_key();

                    // get random coordinates in NA
                    while ($challengeCount < 5) {
                        $coord_arr = $helper->get_100_coordinates();
                        $query = $helper->generate_nearest_roads_query($coord_arr);
                        $query = $query."&key=".$key;
                
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $query);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                            'Content-Type: application/json'
                        ));
                        $result = curl_exec($ch);
                        curl_close($ch);
                
                        $temparr = json_decode($result);
                        $ret_coord_arr = $temparr->snappedPoints;
                
                        $coordIndexArr = array();
                
                        foreach ($ret_coord_arr as &$coord) {
                            if (in_array($coord->originalIndex, $coordIndexArr) === FALSE) {
                                array_push($coordIndexArr,$coord->originalIndex);
                                // check if it has streetview
                                $streetviewQuery = $helper->generate_streetview_query($coord->location->latitude,$coord->location->longitude);
                                $streetviewQuery = $streetviewQuery."&key=".$key;
                
                                $ch = curl_init();
                                curl_setopt($ch, CURLOPT_URL, $streetviewQuery);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                                    'Content-Type: application/json'
                                ));
                                $result = curl_exec($ch);
                                curl_close($ch);
                                
                                $streetviewMetadata = json_decode($result);
                                $status = $streetviewMetadata->status;
                                // if streetview available, add to array
                                if ($status == 'OK') {
                                    ++$challengeCount;
                                    $SVgoodCoord=array(
                                        "latitude" => $streetviewMetadata->location->lat,
                                        "longitude" => $streetviewMetadata->location->lng
                                    );
                                    array_push($streetviewCoord,$SVgoodCoord);
                                }
                                if ($challengeCount >=5) {
                                    break;
                                }
                            }
                        }
                    }
                    // save the generated challenges to db
                    $i =0;
                    foreach ($streetviewCoord as &$coord) {
                        $challengeObj->challengeId = $helper->getGUID();
                        $challengeObj->qNum = $i++;
                        $challengeObj->latitude = $coord["latitude"];
                        $challengeObj->longitude = $coord["longitude"];

                        $challengeObj->generate_daily_challenges();
                    }
                    // retrieve the new added challenge to return as response
                    $stmt = $challengeObj->get_daily_challenges();
                }
            
                $challenges_arr=array();

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    
                    $challenge=array(
                        "challengeId" => $challengeId,
                        "latitude" => $latitude,
                        "longitude" => $longitude,
                        "date" => $date,
                        "qNum" => $qNum
                    );
                    array_push($challenges_arr, $challenge);
                }
                http_response_code(200);
                echo json_encode($challenges_arr);
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
