<?php
include("../connection.php");
require("../encryption.php");

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $headers = getallheaders();
    
    $authorizationHeader = trim($headers['Authorization']);

	if (array_key_exists('Authorization', $headers) && preg_match('/Bearer\s(\S+)/', $authorizationHeader, $matches)){
		if(verifyToken($matches[1])){

            $UserID = $LoginUserID;
            $ProductID = deCodeID($_POST['ProductID'],"PD");
            $MRP = $_POST['MRP'];
            $VariantID = $_POST['VariantID'];
            $Price = $_POST['Price'];

            if($Price>$MRP){
                $data = array ("Message" => "Price Cannot Greater than MRP.");
                response(401, $data);
                exit;
            }

            $updateVariant = mysqli_query($conn, "UPDATE `product_variant` SET `MRP`='$MRP', `Price`='$Price', `Modified_At` = '$CurrendDateTime' WHERE ID = '$VariantID' AND ProductID = '$ProductID'");
            
            if($updateVariant){   
                $data = array ("Message" => "Price Updated Successfully");
                response(200, $data);
            }else{
                $data = array ("Message" => "Price Updation Failed");
                response(401, $data);
            }

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