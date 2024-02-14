<?php
include("../connection.php");
require("../encryption.php");

if($_SERVER['REQUEST_METHOD'] == 'GET'){
    $headers = getallheaders();
    
    $authorizationHeader = trim($headers['Authorization']);

	if (array_key_exists('Authorization', $headers) && preg_match('/Bearer\s(\S+)/', $authorizationHeader, $matches)){
		if(verifyToken($matches[1])){

            $UserID = $LoginUserID;
            $UserGroupID = $_GET['roleid'];
            $RolesArr = [];
            $RolesList = mysqli_query($conn, "SELECT * FROM `permissions`");
            while($PCRow = mysqli_fetch_assoc($RolesList)){
                $PermissionKey = $PCRow['PermissionKey'];
                $checkRoleAllow = mysqli_fetch_assoc(mysqli_query($conn, "SELECT Count(PermissionKey) as AP FROM `user_group_permissions` WHERE UserGroupID = '$UserGroupID' AND PermissionKey = '$PermissionKey'"));
                $PCRow['isAdministrator'] = false;
                if($UserGroupID === 1){
                    $PCRow['isAllowed'] = true;
                    $PCRow['isAdministrator'] = true;
                }
                else if($checkRoleAllow['AP']==1){
                    $PCRow['isAllowed'] = true;
                }else{
                    $PCRow['isAllowed'] = false;
                }
                $PCRow['PermissionID'] = $PCRow['ID'];
                unset($PCRow['ID']);
                $RolesArr[] = $PCRow;
            }
            $data = array ("PermissionList" => $RolesArr);
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

    $data = array ("Message" => "UNAUTHORIZED");
    response(405, $data);
	
}

?>