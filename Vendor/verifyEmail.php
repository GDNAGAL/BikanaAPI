<?php
include("../connection.php");
require("../encryption.php");

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $headers = getallheaders();
	if (array_key_exists('Authorization', $headers) && preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)){

		if(verifyToken($matches[1])){

            $UserID = $LoginUserID;
            $Email = $_POST['Email'];

            $EmailQ = mysqli_fetch_assoc(mysqli_query($conn, "SELECT Count(Email) as ECount FROM `vendor` WHERE Email = '$Email'"));
            if($EmailQ['ECount']==0){
                $data = array ("Message" => "Valid Email Address");
                response(200, $data);
            }else{
                $data = array ("Message" => "Email Already Exist.");
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