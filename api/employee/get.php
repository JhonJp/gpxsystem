<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

include_once "../../config/apidatabase.php";
include_once "../../model/GenericModel.php";

//CONNECT TO DATABASE
$database = new Database();
$connection = $database->connect();

//CALL MODEL    
$model = new GenericModel($connection);
$result = $model->gettallemployee();

echo json_encode($result);

?>