<?php
include("../connection.php");
require("../encryption.php");

if($_SERVER['REQUEST_METHOD'] == 'GET'){
    $headers = getallheaders();
    
    $authorizationHeader = trim($headers['Authorization']);

	if (array_key_exists('Authorization', $headers) && preg_match('/Bearer\s(\S+)/', $authorizationHeader, $matches)){
		if(verifyToken($matches[1])){

            $UserID = $LoginUserID;
            $RolesArr = [];
            $RolesList = mysqli_query($conn, "SELECT * FROM `user_groups`");
            while($PCRow = mysqli_fetch_assoc($RolesList)){
                
                $PCRow['UserRoleID'] = $PCRow['ID'];
                unset($PCRow['ID']);
                $RolesArr[] = $PCRow;
            }
            $data = array ("RolesList" => $RolesArr);
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