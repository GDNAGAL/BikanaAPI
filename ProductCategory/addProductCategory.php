<?php
include("../connection.php");
require("../encryption.php");

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $headers = getallheaders();
	if (array_key_exists('Authorization', $headers) && preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)){

		if(verifyToken($matches[1])){

            $UserID = $LoginUserID;
            $CategoryIcon = $_FILES['SmallImage']['tmp_name'];
            $encodedCategoryIcon = base64_encode(file_get_contents($CategoryIcon));
            $CategoryName = $_POST['CategoryName'];
            $CategoryDesc = $_POST['CategoryDesc'];
            if($_POST['SmallImage']==null){
                $encodedCategoryIcon = null;
            }

            mysqli_query($conn, "INSERT INTO `product_category`(`CategoryName`, `CategoryDesc`, `SmallImage`, `Created_By`, `Created_At`, `Modified_At`) VALUES ('$CategoryName','$CategoryDesc','$encodedCategoryIcon','$UserID','$CurrendDateTime','$CurrendDateTime')");
            $data = array ("Message" => "Category Added Successfully");
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