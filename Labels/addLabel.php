<?php
include("../connection.php");
require("../encryption.php");

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $headers = getallheaders();
	if (array_key_exists('Authorization', $headers) && preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)){

		if(verifyToken($matches[1])){

            $UserID = $LoginUserID;
            $wa_id = $_POST['wa_id'];
            $label = $_POST['label'];
            $b_class = $_POST['b_class'];

            $q = mysqli_query($conn,"SELECT * FROM `conversation_label` WHERE wa_id = '$wa_id'");
            $checkforduplicate = mysqli_fetch_assoc($q);
            $rows = mysqli_num_rows($q);
            if($rows>0){
                $lastinsert = new DateTime($checkforduplicate['Created_At']);
                $currentTime = new DateTime();
                $timeDifference = $lastinsert->diff($currentTime);
                $hourDifference = $timeDifference->h;
                if($hourDifference<20){
                    $data = array ("Message" => "Label Already Added");
                    response(401, $data);
                    exit();
                }

            }
            mysqli_query($conn, "INSERT INTO `conversation_label`(`wa_id`, `label`, `Created_At`, `Created_By`, `b_class`) VALUES ('$wa_id','$label','$CurrendDateTime','$UserID','$b_class')");
            $data = array ("Message" => "Label Added Successfully", "sdg"=>$b_class);
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