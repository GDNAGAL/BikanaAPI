<?php
if($_SERVER['REQUEST_METHOD']=="GET"){
    echo $_GET['hub_challenge']; //respond back hub_callenge key
    http_response_code(200);
} else {
    $jsonPayload = file_get_contents('php://input');
    
    $data = json_decode($jsonPayload, true);   
    if ($data !== null) {
        // Accessing elements in the array
        $entry = $data['entry'][0];

        $changes = $entry['changes'][0];
        
        // Accessing message details
        $messages = $changes['value']['messages'];
        $firstMessage = $messages[0];
        $from = $firstMessage['from'];
        $messageId = $firstMessage['id'];
        $timestamp = $firstMessage['timestamp'];
        $messageType = $firstMessage['type'];
        $messageBody = $firstMessage['text']['body'];
        file_put_contents("response.txt",$messageBody);
        
        // Now you can use these variables as needed in your application logic
        // ...
    
    }
}

?>