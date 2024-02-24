<?php
include("../connection.php");
require("../encryption.php");

if($_SERVER['REQUEST_METHOD']=="GET"){
    echo $_GET['hub_challenge']; //respond back hub_callenge key
    http_response_code(200);
} else {
    $jsonPayload = file_get_contents('php://input');
    
    $data = json_decode($jsonPayload, true);   
    if ($data !== null) {
        $entry = $data['entry'][0];
        $changes = $entry['changes'][0];
        $messages = $changes['value']['messages'];
        $contacts = $changes['value']['contacts'];
        $metadata = $changes['value']['metadata'];
        $bussiness_number = $metadata['display_phone_number'];
        
        foreach($contacts as $contact){
            $wa_id = $contact['wa_id'];
            $profile_Name =  $contact['profile']['name'];
            $CountConversation = mysqli_fetch_assoc(mysqli_query($conn, "SELECT Count(wa_id) as wa_id_count FROM `whatsapp_conversation` WHERE `wa_id`='$wa_id'"));
            file_put_contents("response.txt",$CountConversation['wa_id_count']);
            if($CountConversation['wa_id_count']==0){
                mysqli_query($conn,"INSERT INTO `whatsapp_conversation`(`Bussiness_Number`, `wa_id`, `profile_Name`, `Conversation_Status`, `Created_At`, `Modified_At`)
                 VALUES ('$bussiness_number','$wa_id','$profile_Name','1','$CurrendDateTime','$CurrendDateTime')");
            }else{
                mysqli_query($conn,"UPDATE `whatsapp_conversation` SET `Conversation_Status`='1', `Modified_At`='$CurrendDateTime' WHERE `wa_id`='$wa_id'");
            }
        }
        
        foreach($messages as $message){
            $from = $message['from'];
            $WaMID = $message['id'];
            $timestamp = $message['timestamp'];
            $messageType = $message['type'];
            $contextFrom = NULL;
            $contextID = NULL;
            if(isset($message['context'])){
                $contextFrom = $message['context']['from'];
                $contextID = $message['context']['id'];
            }

            mysqli_query($conn,"INSERT INTO `whatsapp_messages`(`WhatsappMobile`, `Type`, `FromOrTo`, `WaMID`, `MessageTimeStamp`, `MessageType`, `ContextFrom`, `ContextID`, `isRead`) 
             VALUES ('$bussiness_number','RECEIVED','$from','$WaMID','$timestamp','$messageType'," . ($contextFrom !== null ? "'$contextFrom'" : "NULL") . "," . ($contextID !== null ? "'$contextID'" : "NULL") . ",0)");
            $MessageID = mysqli_insert_id($conn);

            if($messageType == "text"){
                $messageBody = $message['text']['body'];
                mysqli_query($conn,"INSERT INTO `whatsapp_text_messages`(`MessageID`, `MessageText`)
                 VALUES ('$MessageID','$messageBody')");

            }
            if($messageType == "image"){
                $caption = $message['image']['caption'];
                $mime_type = $message['image']['mime_type'];
                $sha256 = $message['image']['sha256'];
                $iid = $message['image']['id'];

                // mysqli_query($conn,"INSERT INTO `whatsapp_messages`(`WhatsappMobile`, `Type`, `FromOrTo`, `WaMID`, `MessageTimeStamp`, `MessageType`, `ContextFrom`, `ContextID`, `isRead`) 
                //  VALUES ('$bussiness_number','RECEIVED','$from','$WaMID','$timestamp','$messageType'," . ($contextFrom !== null ? "'$contextFrom'" : "NULL") . "," . ($contextID !== null ? "'$contextID'" : "NULL") . ",0)");
                // $MessageID = mysqli_insert_id($conn);
                

                $curl = curl_init();

                curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://graph.facebook.com/v18.0/784336136950267',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer EAAEzKRKZAiY0BOxJo1Gq9JZA9kafrKJjLmaI5RYpxv1yZBbtuR8KNJVRGu43RKH1157ZCG2QJZBrPl7SCQZBuMy3YhZCqBKar3FRJD9oN6QiIGxcY33R5QwrsHprMou1pT8pMNHHdv0QQjFoxDngwUyfveDnsh5Uv4h0gDZAdZBsuocU3fvQE3W3KFZACImCMGJtgR',
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
                    CURLOPT_URL => 'https://lookaside.fbsbx.com/whatsapp_business/attachments/?mid=784336136950267&ext=1708703682&hash=ATvIqVonYqko-XLWkJ852ytFNHZbZI6XpV8RmUi4sSZFSg',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER => array(
                        'Authorization: Bearer EAAEzKRKZAiY0BOxJo1Gq9JZA9kafrKJjLmaI5RYpxv1yZBbtuR8KNJVRGu43RKH1157ZCG2QJZBrPl7SCQZBuMy3YhZCqBKar3FRJD9oN6QiIGxcY33R5QwrsHprMou1pT8pMNHHdv0QQjFoxDngwUyfveDnsh5Uv4h0gDZAdZBsuocU3fvQE3W3KFZACImCMGJtgR'
                    ),
                    ));

                    $responseimage = curl_exec($curlimage);
                    $httpStatuss = curl_getinfo($curlimage, CURLINFO_HTTP_CODE);

                    // Check for cURL errors
                    if (curl_errno($curlimage)) {
                        echo 'Curl error: ' . curl_error($curl);
                    }

                    curl_close($curlimage);

                    // Generate a random filename with the determined extension
                    $randomFilename = uniqid('image_', true) . '.' . $extension;

                    // Specify the directory and the random filename to save the image
                    $filename = '../Data/' . $randomFilename;

                    // Save the image to the specified directory
                    file_put_contents($filename, $responseimage);
                }
                mysqli_query($conn,"INSERT INTO `whatsapp_image_messages`(`MessageID`, `Caption`, `mime_type`, `sha256`, `iid`, `image_path`)
                 VALUES ('$MessageID','$caption','$mime_type','$sha256','$iid','$httpStatuss')");


            }

            
        }

       
        
        
        // Now you can use these variables as needed in your application logic
        // ...
    
    }
}

?>