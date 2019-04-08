<?php 
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
	header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, userId, date");
	
    include_once './config/database.php';
    include_once './config/keyhandler.php';
    include_once './objects/helper.php';

	// instantiate database and product object
    $database = new Database();
    $db = $database->getConnection();

    $helper = new Helper();
    $keyHandler = new KeyHandler($db);
    
    $request_method=$_SERVER["REQUEST_METHOD"];

    $challengeCount=0;
    $streetviewCoord= array();

    switch($request_method) {
        case 'GET':

        // get google key
        $keyHandler->keyName = "google";
        $key = $keyHandler->get_key();

            // get random coordinates in NA
            $i=0;
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
                        if ($status == 'OK') {
                            ++$challengeCount;
                            $challengeObj = array(
                                "qNum"=> $i++,
                                "lat" => $streetviewMetadata->location->lat,
                                "lng" => $streetviewMetadata->location->lng
                            );
                            array_push($streetviewCoord, $challengeObj);
                        }
                        if ($challengeCount >=5) {
                            break;
                        }
                    }
                }
            }
            http_response_code(200);
            echo json_encode($streetviewCoord);
            break;
		default:
            // Invalid Request Method
            header("HTTP/1.0 405 Method Not Allowed");
		    break;
    }
?>
