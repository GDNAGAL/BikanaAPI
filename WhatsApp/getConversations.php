<?php
include("../connection.php");
require("../encryption.php");

if($_SERVER['REQUEST_METHOD'] == 'GET'){
    $headers = getallheaders();
    
    $authorizationHeader = trim($headers['Authorization']);

	if (array_key_exists('Authorization', $headers) && preg_match('/Bearer\s(\S+)/', $authorizationHeader, $matches)){
		if(verifyToken($matches[1])){

            $UserID = $LoginUserID;
            $ConversationArr = [];

            $ConversationList = mysqli_query($conn, "SELECT * FROM `whatsapp_conversation` ORDER BY `Modified_At` ASC");
            while($PCRow = mysqli_fetch_assoc($ConversationList)){
                $fromorto = $PCRow['wa_id'];
                $unreadCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT Count(isRead) as unread FROM `whatsapp_messages` WHERE `isRead`='0' AND `FromOrTo`='$fromorto'"));
                $PCRow['UnReadMessageCount'] = $unreadCount['unread'];
                $ConversationArr[] = $PCRow;
            }
            $data = array ("ConversationList" => $ConversationArr);
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