<?php
include("../connection.php");
require("../encryption.php");

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $headers = getallheaders();
	if (array_key_exists('Authorization', $headers) && preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)){

		if(verifyToken($matches[1])){

            $UserID = $LoginUserID;
            $User = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM `users` INNER JOIN user_groups ON user_groups.ID = users.UserGroupID WHERE users.ID = '$UserID'"));
            $Permissions = mysqli_query($conn, "SELECT PermissionKey FROM `user_group_permissions` WHERE UserGroupID = $User[UserGroupID]");
            while($PermissionsRow = mysqli_fetch_assoc($Permissions)){
                $PermissionsArr[] = $PermissionsRow;
            }
            $USERPermissions = mysqli_query($conn, "SELECT PermissionKey FROM `users_permissions` WHERE UserID = $User[ID]");
            while($USERPermissionsRow = mysqli_fetch_assoc($USERPermissions)){
                $PermissionsArr[] = $USERPermissionsRow;
            }
            $data = array ("Message" => "Success", "User" => $User, "UserPermission" => $PermissionsArr);
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