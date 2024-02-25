<?php
include("../connection.php");
require("../encryption.php");

if($_SERVER['REQUEST_METHOD'] == 'GET'){
    $headers = getallheaders();
	if (array_key_exists('Authorization', $headers) && preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)){

		if(verifyToken($matches[1])){

            $UserID = $LoginUserID;
            $wa_id = $_GET['wa_id'];
            $label = $_GET['label'];

            mysqli_query($conn, "INSERT INTO `conversation_label`(`wa_id`, `label`, `Created_At`, `Created_By`) VALUES ('$wa_id','$label','$CurrendDateTime','$UserID')");
            $data = array ("Message" => "Label Added Successfully");
            response(200, $data);

		}else{
			
            $data = array ("Message" => "Invalid Token");
            response(401, $data);

		}

	}else{

		$data = array ("Message" => "Token Not Found");
        response(401, $data);

	}

	
}else{

    $data = array ("Message" => "UNAUTHORIZED");
    response(405, $data);
	
}

?>