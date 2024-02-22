<?php
include("../connection.php");
require("../encryption.php");

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $headers = getallheaders();
    
    $authorizationHeader = trim($headers['Authorization']);

	if (array_key_exists('Authorization', $headers) && preg_match('/Bearer\s(\S+)/', $authorizationHeader, $matches)){
		if(verifyToken($matches[1])){

            $UserID = $LoginUserID;
            $Wa_number = $_POST['WANumber'];
            $MessagesArr = [];

            $MessagesList = mysqli_query($conn, "SELECT * FROM `whatsapp_messages` WHERE FromOrTo = '$Wa_number'");
            while($PCRow = mysqli_fetch_assoc($MessagesList)){
                $timestamp = 1645593600; // Replace this with your actual timestamp
                // Convert timestamp to datetime
                $PCRow['M_TimeStamp'] = date("Y-m-d H:i:s", $PCRow['MessageTimeStamp']);
                $MessageID = $PCRow['ID'];

                if($PCRow['MessageType']=="text"){
                       $M =  mysqli_fetch_assoc(mysqli_query($conn, "SELECT MessageText FROM `whatsapp_text_messages` WHERE MessageID = '$MessageID'"));
                       $PCRow['text'] = $m['MessageText'];
                }
                unset($PCRow['MessageTimeStamp']);
                $MessagesArr[] = $PCRow;
            }
            $data = array ("MessagesList" => $MessagesArr);
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