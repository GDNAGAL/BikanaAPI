<?php
include("../connection.php");
require("../encryption.php");

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $headers = getallheaders();
	if (array_key_exists('Authorization', $headers) && preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)){

		if(verifyToken($matches[1])){

            $UserID = $LoginUserID;
            $ProductID = deCodeID($_POST['ProductID'], "PD");
            $VariantTitle = $_POST['VariantTitle'];
            $UnitID = $_POST['UnitID'];
            $AvailableQuantity = $_POST['AvailableQuantity'];
            // if($AvailableQuantity == "" || $AvailableQuantity == NULL){
            //     $AvailableQuantity = NULL;
            // }
            $AvailableQuantity = ($AvailableQuantity == "") ? NULL : $AvailableQuantity;
            $MRP = $_POST['MRP'];
            $Price = $_POST['Price'];

            $stmt = mysqli_prepare($conn, "INSERT INTO `product_variant` (`VariantTitle`, `ProductID`, `UnitID`, `MRP`, `Price`, `AvailableQuantity`, `isActive`) VALUES (?, ?, ?, ?, ?, ?, '1')");
            mysqli_stmt_bind_param($stmt, "ssssss", $VariantTitle, $ProductID, $UnitID, $MRP, $Price, $AvailableQuantity);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            $data = array ("Message" => "Variant Added Successfully");
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