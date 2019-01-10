<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once "../../config/apidatabase.php";
include_once "../../model/ApiModel.php";
include_once "../../model/GenericModel.php";

$database = new Database();
$connection = $database->connect();

$employee = new GenericModel($connection);
$username = isset($_GET['username']) ? $_GET['username'] : null;
$password = isset($_GET['password']) ? $_GET['password'] : null;
$result = $employee->login($username, $password);
if(count($result) == 0 ){
print_r(json_encode("No data Found"));
}
else{
print_r(json_encode($result));
}
?>