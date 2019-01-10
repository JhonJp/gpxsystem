<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once "../../config/apidatabase.php";
include_once "../../model/WarehouseInventoryModel.php";

//CONNECT TO DATABASE
$database = new Database();
$connection = $database->connect();;

//CALL MODEL    
$model = new WarehouseInventoryModel($connection);
$result = $model->getlist2();

//OUTPUT
echo json_encode($result);

?>