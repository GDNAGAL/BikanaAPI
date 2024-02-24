<?php
include("../connection.php");
require("../encryption.php");

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $headers = getallheaders();
    
    $authorizationHeader = trim($headers['Authorization']);

	if (array_key_exists('Authorization', $headers) && preg_match('/Bearer\s(\S+)/', $authorizationHeader, $matches)){
		if(verifyToken($matches[1])){

            $UserID = $LoginUserID;
            $MediaID = $_POST['MediaID'];

            // $MessagesInfo = mysqli_fetch_assoc(mysqli_query($conn, "SELECT Message_Type FROM `whatsapp_messages` WHERE `ID`='$MediaID'"));
            // if($MessagesInfo['Message_Type']=="image"){
            //     $ImageInfo = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM `whatsapp_image_messages` WHERE `MessageID` = '$MediaID'"));
            //     $ImageID = $ImageInfo['iid'];
            //     // $curl = curl_init();

            //     curl_setopt_array($curl, array(
            //         CURLOPT_URL => 'https://graph.facebook.com/v18.0/'.$ImageID,
            //         CURLOPT_RETURNTRANSFER => true,
            //         CURLOPT_ENCODING => '',
            //         CURLOPT_MAXREDIRS => 10,
            //         CURLOPT_TIMEOUT => 0,
            //         CURLOPT_FOLLOWLOCATION => true,
            //         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            //         CURLOPT_CUSTOMREQUEST => 'GET',
            //         CURLOPT_HTTPHEADER => array(
            //             'Authorization: Bearer EAAEzKRKZAiY0BOxJo1Gq9JZA9kafrKJjLmaI5RYpxv1yZBbtuR8KNJVRGu43RKH1157ZCG2QJZBrPl7SCQZBuMy3YhZCqBKar3FRJD9oN6QiIGxcY33R5QwrsHprMou1pT8pMNHHdv0QQjFoxDngwUyfveDnsh5Uv4h0gDZAdZBsuocU3fvQE3W3KFZACImCMGJtgR',
            //         ),
            //     ));

            //     $response = curl_exec($curl);
            //     $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            //     curl_close($curl);
            // }



            // mysqli_query($conn, "UPDATE `whatsapp_messages` SET `isRead` = '1' WHERE FromOrTo = '$Wa_number'");
            $data = array ("Status" => "ok");
            response(200, $data);

		}else{
			
            $data = array ("Message" => "Invalid Token");
            response(401, $data);

		}

	}else{

		$data = array ("Message" => "Token Missing");
        response(401, $data);

	}

	
}else{

    $data = array ("Message" => "Method Not Allowed");
    response(405, $data);
	
}

?>