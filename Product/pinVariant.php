<?php
include("../connection.php");
require("../encryption.php");

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $headers = getallheaders();
	if (array_key_exists('Authorization', $headers) && preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)){

		if(verifyToken($matches[1])){

            $UserID = $LoginUserID;
            $VariantID = $_POST['VariantID'];
            $ProductID = deCodeID($_POST['ProductID'],"PD");

            $stmt = mysqli_query($conn, "UPDATE `products` SET `PinVariant`='$VariantID' WHERE ID = '$ProductID'");

            $data = array ("Message" => "Variant Pinned Successfully");
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