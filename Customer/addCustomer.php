<?php
include("../connection.php");
require("../encryption.php");

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $headers = getallheaders();
	if (array_key_exists('Authorization', $headers) && preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)){

		if(verifyToken($matches[1])){

            $UserID = $LoginUserID;
            $FirstName = $_POST['FirstName'];
            $LastName = $_POST['LastName'];
            $Email = $_POST['Email'];
            $Mobile = $_POST['Mobile'];

            $AddressLine1 = $_POST['AddressLine1'];
            $AddressLine2 = $_POST['AddressLine2'];
            $AreaID = $_POST['AreaID'];

            mysqli_query($conn, "INSERT INTO `customers`(`FirstName`, `LastName`, `Username`, `Password`, `Email`, `isEmailVerified`, `Mobile`, `WhatsappMobile`, `isMobileVerified`, `CustomerStatusID`, `Created_At`) VALUES ('$FirstName','$LastName','','','$Email','0','$Mobile','$Mobile','0','1','$CurrendDateTime')");
            $CustomerID = mysqli_insert_id($conn);

            mysqli_query($conn, "INSERT INTO `customers_address`(`CustomerID`, `AddressLine1`, `AddressLine2`, `AreaID`, `ZoneID`, `CityID`, `StateID`, `Mobile`, `Latitude`, `Longitude`) VALUES ('$CustomerID','$AddressLine1','$AddressLine2','$AreaID',NULL,'1','1','$Mobile','','')");

            $data = array ("Message" => "Customer Added Successfully");
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