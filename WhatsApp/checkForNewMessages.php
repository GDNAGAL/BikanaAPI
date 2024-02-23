<?php
include("../connection.php");
require("../encryption.php");

if($_SERVER['REQUEST_METHOD'] == 'GET'){
    $headers = getallheaders();
    
    $authorizationHeader = trim($headers['Authorization']);

	if (array_key_exists('Authorization', $headers) && preg_match('/Bearer\s(\S+)/', $authorizationHeader, $matches)){
		if(verifyToken($matches[1])){

            $UserID = $LoginUserID;


            $CheckNew= mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(isRead) as NewMsg FROM `whatsapp_messages` WHERE isRead = 0"));
            if($CheckNew['NewMsg']==0){
                $data = array ("Result" => false);
                response(200, $data);
            }else{
                $data = array ("Result" => true);
                response(200, $data);
            }
           

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