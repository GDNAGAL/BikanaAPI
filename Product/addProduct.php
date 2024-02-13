<?php
include("../connection.php");
require("../encryption.php");

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $headers = getallheaders();
	if (array_key_exists('Authorization', $headers) && preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)){

		if(verifyToken($matches[1])){

            $UserID = $LoginUserID;
            $InventoryID = deCodeID($_POST['InventoryID'], "PI");
            $ProductName = $_POST['ProductName'];
            $ProductDesc = $_POST['ProductDesc'];
            $CategoryID = deCodeID($_POST['CategoryID'], "PC");

            $VariantTitle = $_POST['VariantTitle'];
            $UnitID = $_POST['UnitID'];
            $MRP = $_POST['MRP'];
            $Price = $_POST['Price'];


            mysqli_query($conn, "INSERT INTO `products`(`ProductName`, `ProductDesc`, `CategoryID`, `InventoryID`, `PinVariant`, `Created_At`, `Modified_At`, `StoreID`)  VALUES ('$ProductName','$ProductDesc','$CategoryID','$InventoryID','null','$CurrendDateTime','$CurrendDateTime','$UserID')");
            $data = array ("Message" => "Product Added Successfully");
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