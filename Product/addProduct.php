<?php
include("../connection.php");
require("../encryption.php");

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $headers = getallheaders();
	if (array_key_exists('Authorization', $headers) && preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)){

		if(verifyToken($matches[1])){

            $UserID = $LoginUserID;
            $InventoryID = deCodeID($_POST['InventoryID'], "PI");

            $checkDuplicateProduct =  mysqli_fetch_assoc(mysqli_query($conn, "SELECT Count(ID) as total FROM `products` WHERE StoreID = '$UserID' AND InventoryID = '$InventoryID'"));

            if($checkDuplicateProduct['total'] == 0){

                $ProductName = $_POST['ProductName'];
                $ProductDesc = $_POST['ProductDesc'];
                $CategoryID = deCodeID($_POST['CategoryID'], "PC");
                
                $VariantTitle = $_POST['VariantTitle'];
                $UnitID = $_POST['UnitID'];
                $MRP = $_POST['MRP'];
                $Price = $_POST['Price'];
                
                
                mysqli_query($conn, "INSERT INTO `products`(`ProductName`, `ProductDesc`, `CategoryID`, `InventoryID`, `PinVariant`, `Created_At`, `Modified_At`, `StoreID`)  VALUES ('$ProductName','$ProductDesc','$CategoryID','$InventoryID','null','$CurrendDateTime','$CurrendDateTime','$UserID')");
                $ProductID = mysqli_insert_id($conn);
                mysqli_query($conn, "INSERT INTO `product_variant`(`VariantTitle`, `ProductID`, `UnitID`, `MRP`, `Price`, `AvailableQuantity`, `isActive`)  VALUES ('$VariantTitle','$ProductID','$UnitID','$MRP','$Price','0','1')");
                $VariantID = mysqli_insert_id($conn);
                mysqli_query($conn, "UPDATE `products` set `PinVariant`=$VariantID WHERE ID=$ProductID");

                $data = array ("Message" => "Product Added Successfully");
                response(200, $data);

            }else{
                $data = array ("Message" => "Product Already Added in Your Store".$checkDuplicateProduct['total']);
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