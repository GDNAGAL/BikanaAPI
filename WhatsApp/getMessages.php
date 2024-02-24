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
            $getType = $_POST['getType'];
            $MessagesArr = [];

            if($getType == "START"){
                $MessagesList = mysqli_query($conn, "SELECT * FROM `whatsapp_messages` WHERE FromOrTo = '$Wa_number' ORDER BY MessageTimeStamp ASC");
            }elseif($getType == "UNREAD"){
                $MessagesList = mysqli_query($conn, "SELECT * FROM `whatsapp_messages` WHERE FromOrTo = '$Wa_number' AND isRead = '0' ORDER BY MessageTimeStamp ASC");
            }
            while($PCRow = mysqli_fetch_assoc($MessagesList)){
                $timestamp = 1645593600; // Replace this with your actual timestamp
                // Convert timestamp to datetime
                $PCRow['M_TimeStamp'] = date("Y-m-d H:i:s", $PCRow['MessageTimeStamp']);
                $MessageID = $PCRow['ID'];

                if($PCRow['MessageType']=="text"){
                       $M =  mysqli_fetch_assoc(mysqli_query($conn, "SELECT MessageText FROM `whatsapp_text_messages` WHERE MessageID = '$MessageID'"));
                       $PCRow['text'] = $M['MessageText'];
                    //    $PCRow['text'] = $MessageID;
                }
                if($PCRow['MessageType']=="image"){
                    // $I =  mysqli_fetch_assoc(mysqli_query($conn, "SELECT Caption, image_path FROM `whatsapp_image_messages` WHERE MessageID = '$MessageID'"));
                    $I =  mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM `whatsapp_image_messages` WHERE MessageID = '$MessageID'"));
                    $PCRow['image'] = $I;
                 //    $PCRow['text'] = $MessageID;
                }
                unset($PCRow['MessageTimeStamp']);
                $MessagesArr[] = $PCRow;
            }
            mysqli_query($conn, "UPDATE `whatsapp_messages` SET `isRead` = '1' WHERE FromOrTo = '$Wa_number'");
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