<?php
include("../connection.php");
require("../encryption.php");

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $headers = getallheaders();
    
    $authorizationHeader = trim($headers['Authorization']);

	if (array_key_exists('Authorization', $headers) && preg_match('/Bearer\s(\S+)/', $authorizationHeader, $matches)){
		if(verifyToken($matches[1])){

            $UserID = $LoginUserID;
            $Wa_number = $_POST['Number'];
            $MessagesArr = [];

            $MessagesList = mysqli_query($conn, "SELECT * FROM `whatsapp_messages` WHERE FromOrTo = '$Wa_number'");
            while($PCRow = mysqli_fetch_assoc($MessagesList)){
                $PCRow['UnReadMessageCount'] = 4;
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