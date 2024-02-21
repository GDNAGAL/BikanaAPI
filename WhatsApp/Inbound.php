<?php
if($_SERVER['REQUEST_METHOD']=="GET"){
    echo $_GET['hub_challenge']; //respond back hub_callenge key
    http_response_code(200);
}else{
    $data = json_decode(file_get_contents('php://input'), true);
    error_log(json_encode($data)); //print inbound message     
}
?>