<?php
include("../connection.php");
require("../encryption.php");

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $headers = getallheaders();
	if (array_key_exists('Authorization', $headers) && preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)){

		if(verifyToken($matches[1])){

            $UserID = $LoginUserID;
            $UserGroupID = $_POST['UserGroupID'];
            $Name = $_POST['Name'];
            $Mobile = $_POST['Mobile'];
            $Email = $_POST['Email'];

            $userEnc = md5($StoreUserName);
            $passEnc = md5($StorePassword);

            mysqli_query($conn, "INSERT INTO `users`(`Name`, `Mobile`, `Username`, `UsernameEnc`, `PasswordEnc`, `UserGroupID`, `UserStatusID`, `AllowedLogins`) VALUES ('$Name','$Mobile','$Mobile','$userEnc','$passEnc','$UserGroupID','1','1')");

            $data = array ("Message" => "User Added Successfully");
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