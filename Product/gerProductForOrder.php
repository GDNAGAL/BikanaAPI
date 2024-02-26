<?php
include("../connection.php");
require("../encryption.php");

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $headers = getallheaders();
    
    $authorizationHeader = trim($headers['Authorization']);

	if (array_key_exists('Authorization', $headers) && preg_match('/Bearer\s(\S+)/', $authorizationHeader, $matches)){
		if(verifyToken($matches[1])){

            $UserID = $LoginUserID;
            $VariantArr = [];
            $ProductID = $_POST['ProductID'];

            $VariantList = mysqli_query($conn, "SELECT product_variant.*, UnitText FROM `product_variant` INNER JOIN product_units ON product_variant.UnitID = product_units.ID WHERE product_variant.ID = '$ProductID'");
            
            while($PCRow = mysqli_fetch_assoc($VariantList)){
                
                // $PCRow['ProductID'] = setCodeID($PCRow['ID'],"PD");
                // $PCRow['CategoryID'] = setCodeID($PCRow['CategoryID'],"PC");
                // unset($PCRow['ID']);
                // unset($PCRow['InventoryID']);
                $VariantArr[] = $PCRow;
            }
            $data = array ("Product" => $VariantArr);
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