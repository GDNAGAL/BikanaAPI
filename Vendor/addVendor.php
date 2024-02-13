<?php
include("../connection.php");
require("../encryption.php");

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $headers = getallheaders();
	if (array_key_exists('Authorization', $headers) && preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)){

		if(verifyToken($matches[1])){

            $UserID = $LoginUserID;
            $Name = $_POST['Name'];
            $Email = $_POST['Email'];
            $Mobile = $_POST['Mobile'];

            $StoreName = $_POST['StoreName'];
            $StoreAddress = $_POST['StoreAddress'];
            $StoreUserName = $_POST['StoreUserName'];
            $StorePassword = $_POST['StorePassword'];
            $userEnc = md5($StoreUserName);
            $passEnc = md5($StorePassword);

            mysqli_query($conn, "INSERT INTO `vendor`(`Name`, `Email`, `Mobile`, `Created_By`, `Created_At`, `Modified_At`, `VendorStatusID`) VALUES ('$Name','$Email','$Mobile','$UserID','$CurrendDateTime','$CurrendDateTime','1')");
            $VendorID = mysqli_insert_id($conn);

            mysqli_query($conn, "INSERT INTO `users`(`Name`, `Mobile`, `Username`, `UsernameEnc`, `PasswordEnc`, `UserGroupID`, `UserStatusID`, `AllowedLogins`) VALUES ('$StoreName','$Mobile',$StoreUserName','$userEnc','$passEnc','2','1','1')");
            $NewUSerID = mysqli_insert_id($conn);
            mysqli_query($conn, "INSERT INTO `vendor_stores`(`ID`, `VendorID`, `StoreName`, `StoreAddress`, `StoreUserName`, `StorePassword`, `StoreStatusID`, `Created_By`, `Created_At`, `Modified_At`) VALUES ('$NewUSerID','$VendorID','$StoreName','$StoreAddress','$StoreUserName','$StorePassword','1','$UserID','$CurrendDateTime','$CurrendDateTime')");
            $data = array ("Message" => "Vendor Added Successfully");
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