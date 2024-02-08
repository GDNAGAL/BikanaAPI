<?php
    $iv = '1234567890123654'; // Generate a random IV
    $encryptionKey = "fc9a16a5bb367d3da1f7656bf2e8bc35"; // Replace with your secret key
    $cipher = "AES-256-CBC";
    $options = 0;
    function encrypt($data) {
        global $cipher, $encryptionKey, $iv, $options;
        
        $encrypted = openssl_encrypt($data, $cipher, $encryptionKey, $options, $iv);
        return base64_encode($encrypted);
    }

    function decrypt($data){
        global $cipher, $encryptionKey, $iv, $options;
        $decrypted = openssl_decrypt(base64_decode($data), $cipher, $encryptionKey, $options,  $iv);
        return $decrypted;
    }	
?>