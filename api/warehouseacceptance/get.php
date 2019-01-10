<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once "../../config/apidatabase.php";
include_once "../../model/WarehouseAcceptanceModel.php";

//CONNECT TO DATABASE
$database = new Database();
$connection = $database->connect();;

//CALL MODEL    
$model = new WarehouseAcceptanceModel($connection);
$result = $model->getlist();

//OUTPUT
echo json_encode($result);

?>