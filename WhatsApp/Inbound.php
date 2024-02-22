<?php
if($_SERVER['REQUEST_METHOD']=="GET"){
    echo $_GET['hub_challenge']; //respond back hub_callenge key
    http_response_code(200);
} else {
    $jsonPayload = file_get_contents('php://input');
    
    $data = json_decode($jsonPayload, true);   
    if ($data !== null) {
        file_put_contents("response.txt","ok");
        $entry = $data['entry'][0];
        $changes = $entry['changes'][0];
        $messages = $changes['value']['messages'];
        $contacts = $changes['value']['contacts'];
        $metadata = $changes['value']['metadata'];
        $bussiness_number = $metadata['display_phone_number'];

        foreach($contacts as $contact){
            $wa_id = $contact['wa_id'];
            $profile_Name =  $contact['profile']['name'];
            $CountConversation = mysqli_fetch_assoc(mysqli_query($conn, "SELECT Count(wa_id) FROM `whatsapp_conversation` WHERE wa_id = '$wa_id'"));
            if($CountConversation['wa_id']==0){
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
            if($messageType == "text"){
                $messageBody = $message['text']['body'];

            }

            
        }

       
        
        
        // Now you can use these variables as needed in your application logic
        // ...
    
    }
}

?>