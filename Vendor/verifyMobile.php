<?php
include("../connection.php");
require("../encryption.php");

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $headers = getallheaders();
	if (array_key_exists('Authorization', $headers) && preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)){

		if(verifyToken($matches[1])){

            $UserID = $LoginUserID;
            $Mobile = $_POST['Mobile'];

            $EmailQ = mysqli_fetch_assoc(mysqli_query($conn, "SELECT Count(Mobile) as ECount FROM `vendor` WHERE Mobile = '$Mobile'"));
            if($EmailQ['ECount']==0){
                $data = array ("Message" => "Valid Mobile No.");
                response(200, $data);
            }else{
                $data = array ("Message" => "Mobile No. Already Exist.");
                response(401, $data);
            } 

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