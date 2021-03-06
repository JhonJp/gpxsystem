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
$id = isset($_GET['id']) ? $_GET['id'] : 0;
$result = $model->getdeliveries($id);

//OUTPUT
echo json_encode($result);

?>