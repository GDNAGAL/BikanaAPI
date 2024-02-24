<?php
include("../connection.php");
require("../encryption.php");

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $headers = getallheaders();
    $Barer = "EAAEzKRKZAiY0BOxJo1Gq9JZA9kafrKJjLmaI5RYpxv1yZBbtuR8KNJVRGu43RKH1157ZCG2QJZBrPl7SCQZBuMy3YhZCqBKar3FRJD9oN6QiIGxcY33R5QwrsHprMou1pT8pMNHHdv0QQjFoxDngwUyfveDnsh5Uv4h0gDZAdZBsuocU3fvQE3W3KFZACImCMGJtgR";
    $authorizationHeader = trim($headers['Authorization']);

	if (array_key_exists('Authorization', $headers) && preg_match('/Bearer\s(\S+)/', $authorizationHeader, $matches)){
		if(verifyToken($matches[1])){

            $UserID = $LoginUserID;
            $MediaID = $_POST['MediaID'];

            $MessagesInfo = mysqli_fetch_assoc(mysqli_query($conn, "SELECT MessageType FROM `whatsapp_messages` WHERE `ID`='$MediaID'"));
            if($MessagesInfo['MessageType']=="image"){
                $ImageInfo = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM `whatsapp_image_messages` WHERE `MessageID` = '$MediaID'"));
                $ImageID = $ImageInfo['iid'];
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://graph.facebook.com/v18.0/'.$ImageID,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER => array(
                        'Authorization: Bearer '.$Barer,
                    ),
                ));

                $response = curl_exec($curl);
                $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                curl_close($curl);

                if ($httpStatus == 200) {
                    // Decode JSON response
                    $data = json_decode($response, true);
                    // Check for errors in the response
                    if (isset($data['error'])) {
                        die('API Error: ' . $data['error']['message']);
                    }
                    $imageUrl = $data['url'];
                    $mime_type = $data['mime_type'];
                    $extension = '';
                    switch ($mime_type) {
                        case 'image/jpeg':
                            $extension = 'jpg';
                            break;
                        case 'image/png':
                            $extension = 'png';
                            break;
                        case 'image/gif':
                            $extension = 'gif';
                            break;
                        // Add more cases as needed for other image types
                        default:
                            // Default to a generic extension if the MIME type is not recognized
                            $extension = 'unknown';
                    }


                    //get image and save to directoy
                    $curlimage = curl_init();

                    curl_setopt_array($curlimage, array(
                    CURLOPT_URL => $imageUrl,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER => array(
                        'Authorization: Bearer '.$Barer
                    ),
                    ));

                    $responseimage = curl_exec($curlimage);
                    $httpStatuss = curl_getinfo($curlimage, CURLINFO_HTTP_CODE);

                    curl_close($curlimage);

                    // Generate a random filename with the determined extension
                    $randomFilename = uniqid('image_', true) . '.' . $extension;

                    // Specify the directory and the random filename to save the image
                    $filename = '../Data/' . $randomFilename;

                    // Save the image to the specified directory
                    file_put_contents($filename, $responseimage);
                    echo $responseimage;
                }
            }



            // mysqli_query($conn, "UPDATE `whatsapp_messages` SET `isRead` = '1' WHERE FromOrTo = '$Wa_number'");
            // $data = array ("Status" => $imageUrl);
            // response(200, $data);

		}else{
			
            $data = array ("Message" => "Invalid Token");
            response(401, $data);

		}

	}else{

		$data = array ("Message" => "Token Missing");
        response(401, $data);

	}

	
}else{

    $data = array ("Message" => "Method Not Allowed");
    response(405, $data);
	
}

?>