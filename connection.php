<?php
$type = "TESTDB";   //LIVEDB OR TESTDB OR LOCALDB

if($type == "LOCALDB"){

  $servername = "localhost"; $username = "root"; $password = ""; $db = "demo_g_db";

}elseif($type == "TESTDB"){

  $servername = "localhost"; $username = "u664437076_grocery"; $password = ";9tYHTiD"; $db = "u664437076_grocery";

}elseif($type == "LIVEDB"){

  $servername = "localhost"; $username = "root"; $password = ""; $db = "easy";

}



$allowedOrigins = [
  "http://localhost:3000",
  "http://localhost:3000/",
  "http://localhost",
  "https://royalplay.live", 
];


if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowedOrigins)) {
  header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
  header('Access-Control-Allow-Credentials: true');
  header('Access-Control-Max-Age: 86400');
  header("Access-Control-Allow-Headers: Content-Type, Authorization");
  header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
}

// header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
// header('Access-Control-Allow-Credentials: true');
// header('Access-Control-Max-Age: 31536000');
// header("Access-Control-Allow-Headers: *");
// header("Access-Control-Allow-Methods: *");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
  header("HTTP/1.1 200 OK");
  exit;
}


date_default_timezone_set("Asia/Calcutta");

$conn = new mysqli($servername, $username, $password, $db);
$LoginUserID = 0;
$CurrendDateTime = date("Y-m-d H:i:s");

function verifyToken($token){
  
  global $conn, $LoginUserID;

  $datajson = json_decode(decrypt($token),true);
  if(isset($datajson['Type'])){

    //For Users
    if($datajson['Type'] == "USER"){
       $row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as rowcount, UserID FROM `user_logins`  WHERE `Token` = '$token' AND `isActive` = 1"));
      if($row['rowcount']==1){
        $LoginUserID = $row['UserID'];
        return true;
      }else{
        return false;
      }
    }

  }else{
    return false;
  }
}



// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

function response($code,$data){
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode( $data );
}

function getUserIP() {
  $ip = $_SERVER['REMOTE_ADDR'];
  
  // Use a proxy or load balancer's IP address if available
  if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
  } elseif (array_key_exists('HTTP_CLIENT_IP', $_SERVER)) {
      $ip = $_SERVER['HTTP_CLIENT_IP'];
  }

  return $ip;
}


function setCategoryID($id){
    // Validate if $number is a positive integer
    $number = intval($id);
    if ($number > 0) {
      $result = sprintf('PC%03d', $number);
      return $result;
    } else {
      // Handle invalid input
      return "Wrong";
    }
}

function decodeIDs($string, $ch) {
  // Remove the specified prefix from the beginning of the string
  $string = preg_replace('/^' . preg_quote($ch, '/') . '/', '', $string);

  // Remove leading zeros
  $string = ltrim($string, '0');

  // Find the position of the first digit greater than zero
  $first_valid_digit_pos = strcspn($string, '123456789');

  // Extract the substring up to the first valid digit
  $result = substr($string, $first_valid_digit_pos);

  return $result;
}

?>