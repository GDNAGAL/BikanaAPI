<?php
include("../connection.php");
require("../encryption.php");

if($_SERVER['REQUEST_METHOD'] == 'GET'){
    $headers = getallheaders();
    
    $authorizationHeader = trim($headers['Authorization']);

	if (array_key_exists('Authorization', $headers) && preg_match('/Bearer\s(\S+)/', $authorizationHeader, $matches)){
		if(verifyToken($matches[1])){

            $UserID = $LoginUserID;
            $SearchText = $_GET['SearchText'];
            $InventoryArr = [];
            $InventoryList = mysqli_query($conn, "SELECT pi.ID,pi.Created_By, ProductName, ProductDesc, pi.Created_At, CategoryName, users.Name FROM `product_inventory` as pi INNER JOIN users ON users.ID = pi.Created_By INNER JOIN product_category ON product_category.ID = pi.CategoryID WHERE ProductName LIKE '%$SearchText%'");
            while($PCRow = mysqli_fetch_assoc($InventoryList)){
                
                $PCRow['InventoryID'] = setCodeID($PCRow['ID'],"PI");
                $PCRow['Created_ByID'] = $PCRow['Created_By'];
                $PCRow['Created_By'] = $PCRow['Name'];
                unset($PCRow['ID']);
                unset($PCRow['Name']);
                $InventoryArr[] = $PCRow;
            }
            $data = array ("InventoryList" => $InventoryArr);
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