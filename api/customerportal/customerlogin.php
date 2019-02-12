<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once "../../config/apidatabase.php";
include_once "../../model/GenericModel.php";

//CONNECT TO DATABASE
$database = new Database();
$connection = $database->connect();;

//CALL MODEL    
$customers = new GenericModel($connection);
$username = isset($_GET['username']) ? $_GET['username'] : "";
$password = isset($_GET['password']) ? $_GET['password'] : "";
$counting = $customers->countlogin($username,$password);


//OUTPUT
if ($counting == 0){
    $result = 'ERROR';
}else{
    $result = $customers->customerlogin($username,$password);
}
echo json_encode($result);


?>