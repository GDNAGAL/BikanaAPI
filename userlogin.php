<?php
include("connection.php");
require("encryption.php");

if($_SERVER['REQUEST_METHOD'] == 'POST'){
	$myusername = md5(mysqli_real_escape_string($conn,$_POST['Username']));
    $mypassword = md5(mysqli_real_escape_string($conn,$_POST['Password'])); 


    $result = mysqli_query($conn, "SELECT * FROM `users`  WHERE `UsernameEnc` = '$myusername' AND `PasswordEnc` = '$mypassword' LIMIT 1");
    if (mysqli_num_rows($result)==1) {
		$row = mysqli_fetch_assoc($result);
		
			$loginDateTime = $CurrendDateTime; 
			$row['loginDateTime'] = $loginDateTime;
			$row['Type'] = "USER";
			$accesstoken = encrypt(json_encode($row));
			$UserID = $row['ID'];
			$UserIP = getUserIP();
			$isActive = 1;
			$Success = 1;
			
			mysqli_query($conn,"INSERT INTO `user_logins` (`UserID`, `Token`, `Created_At`, `isActive`, `IP_Address`, `Success`) VALUES ($UserID, '$accesstoken', '$loginDateTime', 1, '$UserIP', 1)");
			mysqli_query($conn,"CALL setUserAllowedLogins($UserID)");

			$data = array ("Status" => "Success", "Message" => "Login Success", "Token" => $accesstoken);
			response(200, $data);

	}else{

		$data = array ("Status" => "Failed", "Message" => "Incorrect Username And Password");
		response(401, $data);

	}
	
}else{

    $data = array ("Message" => "GET Method Not Allowed.");
    response(401, $data);
	
}

?>