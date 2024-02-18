<?php
include("../connection.php");
require("../encryption.php");

if($_SERVER['REQUEST_METHOD'] == 'GET'){
    $headers = getallheaders();
    
    $authorizationHeader = trim($headers['Authorization']);

	if (array_key_exists('Authorization', $headers) && preg_match('/Bearer\s(\S+)/', $authorizationHeader, $matches)){
		if(verifyToken($matches[1])){

            $UserID = $LoginUserID;
            $CustomerArr = [];
            // $CategoryList = mysqli_query($conn, "SELECT vn.ID, vn.Name as vendorname, Email, vn.Mobile, vn.Created_At, users.Name  FROM `vendor` as vn INNER JOIN users ON users.ID = vn.Created_By");
            $CustomerList = mysqli_query($conn, "CALL `CRM.getCustomerList`()");
            while($PCRow = mysqli_fetch_assoc($CustomerList)){
                $CustomerArr[] = $PCRow;
            }
            $data = array ("CustomerList" => $CustomerArr);
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