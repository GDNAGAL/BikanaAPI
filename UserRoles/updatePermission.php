<?php
include("../connection.php");
require("../encryption.php");

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $headers = getallheaders();
	if (array_key_exists('Authorization', $headers) && preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)){

		if(verifyToken($matches[1])){

            $UserID = $LoginUserID;
            $Permission = json_decode($_POST['Permission']);
            $UserGroupID = $_POST['UserGroupID'];

            mysqli_query($conn, "DELETE FROM `user_group_permissions` WHERE UserGroupID= '$UserGroupID'");
            foreach ($Permission as $PermissionID) {
                $PermissionKey = mysqli_fetch_assoc(mysqli_query($conn, "SELECT PermissionKey From `permissions` WHERE ID = '$PermissionID'"));
                if($PermissionKey['PermissionKey']){
                    $key = $PermissionKey['PermissionKey'];
                    mysqli_query($conn, "INSERT INTO `user_group_permissions`(`UserGroupID`, `PermissionKey`) VALUES ('$UserGroupID','$key')");
                }  
            }
            $data = array ("Message" => "Permissions Updated Successfully");
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