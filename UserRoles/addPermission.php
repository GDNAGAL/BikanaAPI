<?php
include("../connection.php");
require("../encryption.php");

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $headers = getallheaders();
	if (array_key_exists('Authorization', $headers) && preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)){

		if(verifyToken($matches[1])){

            $UserID = $LoginUserID;
            $PermissionKey = $_POST['PermissionKey'];
            $PermissionText = $_POST['PermissionText'];

            $VerifyPermissionKey = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as PCount From `permissions` WHERE PermissionKey = $PermissionKey"));
            if($VerifyPermissionKey['PCount']==0){
                mysqli_query($conn, "INSERT INTO `permissions`(`PermissionKey`, `PermissionText`) VALUES ('$PermissionKey','$PermissionText')");
                $data = array ("Message" => "Permission Key Added Successfully");
                response(200, $data);
            }else{
                $data = array ("Message" => "Permission Key Already Exist.");
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