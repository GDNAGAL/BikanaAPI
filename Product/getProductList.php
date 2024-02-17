<?php
include("../connection.php");
require("../encryption.php");

if($_SERVER['REQUEST_METHOD'] == 'GET'){
    $headers = getallheaders();
    
    $authorizationHeader = trim($headers['Authorization']);

	if (array_key_exists('Authorization', $headers) && preg_match('/Bearer\s(\S+)/', $authorizationHeader, $matches)){
		if(verifyToken($matches[1])){

            $UserID = $LoginUserID;
            $ProductArr = [];
            // $ProductList = mysqli_query($conn, "SELECT products.ID, ProductName, CategoryName, StoreName, StoreID, products.Created_At FROM `products` INNER JOIN vendor_stores ON products.StoreID = vendor_stores.ID INNER JOIN product_category ON products.CategoryID = product_category.ID");
            if(isset($_GET['ProductID'])){
                $ProductID = deCodeID($_GET['ProductID'],"PD");

                $ProductList = mysqli_query($conn, "CALL `CRM.getProductById`($UserID, $ProductID)");
            }else{
                $ProductList = mysqli_query($conn, "CALL `CRM.getProductsList`($UserID)");
            }
            while($PCRow = mysqli_fetch_assoc($ProductList)){
                
                $PCRow['ProductID'] = setCodeID($PCRow['ID'],"PD");
                $PCRow['CategoryID'] = setCodeID($PCRow['CategoryID'],"PC");
                unset($PCRow['ID']);
                unset($PCRow['InventoryID']);
                $ProductArr[] = $PCRow;
            }
            $data = array ("ProductList" => $ProductArr);
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