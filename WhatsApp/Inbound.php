<?php
if($_SERVER['REQUEST_METHOD']=="GET"){
    echo $_GET['hub_challenge']; //respond back hub_callenge key
    http_response_code(200);
} else {
    $data = file_get_contents('php://input');
    file_put_contents("response.txt",$data);
    error_log(json_encode($data)); //print inbound message     
    print_r($data); // print the decoded data
}

?>