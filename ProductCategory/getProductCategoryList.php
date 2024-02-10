<?php
include("../connection.php");
require("../encryption.php");

if($_SERVER['REQUEST_METHOD'] == 'GET'){
    $headers = getallheaders();
	if (array_key_exists('Authorization', $headers) && preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)){

		if(verifyToken($matches[1])){

            $UserID = $LoginUserID;
            $CategoryArr = [];
            $CategoryList = mysqli_query($conn, "SELECT pc.ID, pc.SmallImage, pc.CategoryName, pc.CategoryDesc, pc.Created_At, users.Name  FROM `product_category` as pc INNER JOIN users ON users.ID = pc.Created_By");
            while($PCRow = mysqli_fetch_assoc($CategoryList)){
                $data = explode(',', $PCRow['SmallImage']);
                $base64Image = 'data:image/ ;base64,' . $PCRow['SmallImage'];
                $PCRow['SmallImage'] = $base64Image;
                $PCRow['CategoryID'] = setCategoryID($PCRow['ID']);
                $PCRow['Created_By'] = $PCRow['Name'];
                unset($PCRow['ID']);
                unset($PCRow['Name']);
                $CategoryArr[] = $PCRow;
            }
            $data = array ("ProductCategoryList" => $CategoryArr);
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