<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

include_once "../../config/apidatabase.php";
include_once "../../model/ApiModel.php";
include_once "../../model/RemittanceModel.php";

$database = new Database();
$connection = $database->connect();

$data2 = file_get_contents('php://input');  
$data = json_decode(file_get_contents('php://input'),true);  

//FOR TESTING
$api = new ApiModel($connection);
$result = $api->testsave($data2);

$model = new RemittanceModel($connection);
$result = $model->saveExpense($data);

echo json_encode($result);

?>