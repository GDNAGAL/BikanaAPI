<?php
include("../connection.php");
require("../encryption.php");

if($_SERVER['REQUEST_METHOD'] == 'GET'){
    $headers = getallheaders();
    
    $authorizationHeader = trim($headers['Authorization']);

	if (array_key_exists('Authorization', $headers) && preg_match('/Bearer\s(\S+)/', $authorizationHeader, $matches)){
		if(verifyToken($matches[1])){

            $UserID = $LoginUserID;
            $VariantArr = [];
            $ProductID = deCodeID($_GET['ProductID'],"PD");

            $VariantList = mysqli_query($conn, "SELECT * FROM `product_variant` WHERE ProductID = '$ProductID'");
            
            while($PCRow = mysqli_fetch_assoc($VariantList)){
                
                // $PCRow['ProductID'] = setCodeID($PCRow['ID'],"PD");
                // $PCRow['CategoryID'] = setCodeID($PCRow['CategoryID'],"PC");
                // unset($PCRow['ID']);
                // unset($PCRow['InventoryID']);
                $VariantArr[] = $PCRow;
            }
            $data = array ("VariantList" => $VariantArr);
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