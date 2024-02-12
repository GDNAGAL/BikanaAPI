<?php
include("../connection.php");
require("../encryption.php");

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $headers = getallheaders();
	if (array_key_exists('Authorization', $headers) && preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)){

		if(verifyToken($matches[1])){

            $UserID = $LoginUserID;
            $ProductName = $_POST['ProductName'];
            $ProductDesc = $_POST['ProductDesc'];
            $CategoryID = decodeIDs($_POST['CategoryID'], "PC");


            mysqli_query($conn, "INSERT INTO `product_inventory`(`ProductName`, `ProductDesc`, `CategoryID`, `Created_By`, `Created_At`, `Modified_At`)  VALUES ('$ProductName','$ProductDesc','$CategoryID','$UserID','$CurrendDateTime','$CurrendDateTime')");
            $data = array ("Message" => "Inventory Added Successfully");
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