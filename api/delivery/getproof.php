<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once "../../config/apidatabase.php";
include_once "../../model/DeliveryModel.php";

//CONNECT TO DATABASE
$database = new Database();
$connection = $database->connect();

//CALL MODEL    
$model = new DeliveryModel($connection);
$result = $model->getproof();

//OUTPUT
echo json_encode($result);

?>