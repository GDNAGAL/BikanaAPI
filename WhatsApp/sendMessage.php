<?php
include("../connection.php");
require("../encryption.php");

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $headers = getallheaders();
    
    $authorizationHeader = trim($headers['Authorization']);

	if (array_key_exists('Authorization', $headers) && preg_match('/Bearer\s(\S+)/', $authorizationHeader, $matches)){
		if(verifyToken($matches[1])){

            $UserID = $LoginUserID;
            $Wa_ID = $_POST['wa_id'];
            $type = $_POST['type'];
            
            if($type=="text"){
                $text = $_POST['text'];
                
                $curl = curl_init();
                
                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://graph.facebook.com/v18.0/123484010857289/messages',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS =>'{
                        "messaging_product": "whatsapp",    
                        "recipient_type": "individual",
                        "to": '.$Wa_ID.'",
                        "type": "text",
                        "text": {
                            "preview_url": false,
                            "body": '.$text.'"
                        }
                    }',
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json',
                        'Authorization: Bearer EAAEzKRKZAiY0BOxJo1Gq9JZA9kafrKJjLmaI5RYpxv1yZBbtuR8KNJVRGu43RKH1157ZCG2QJZBrPl7SCQZBuMy3YhZCqBKar3FRJD9oN6QiIGxcY33R5QwrsHprMou1pT8pMNHHdv0QQjFoxDngwUyfveDnsh5Uv4h0gDZAdZBsuocU3fvQE3W3KFZACImCMGJtgR',
                        'Cookie: ps_l=0; ps_n=0'
                    ),
                ));
                
                $response = curl_exec($curl);
                $timestamp=time();
                curl_close($curl);
                $data = json_decode($response, true);
                $iid = $data['messages'][0]['id'];
                mysqli_query($conn,"INSERT INTO `whatsapp_messages`(`WhatsappMobile`, `Type`, `FromOrTo`, `WaMID`, `MessageTimeStamp`, `MessageType`, `ContextFrom`, `ContextID`, `isRead`) 
                VALUES ('919257567137','SENT','$Wa_ID','$iid','$timestamp','$type','NULL','NULL',0)");
                $MessageID = mysqli_insert_id($conn);
                mysqli_query($conn,"INSERT INTO `whatsapp_text_messages`(`MessageID`, `MessageText`)
                 VALUES ('$MessageID','$text')");
            }


            $data = array ("Message" => "Message Sent Successfully");
            response(200, $data);

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