<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once "../../config/apidatabase.php";
include_once "../../model/DistributionModel.php";

//CONNECT TO DATABASE
$database = new Database();
$connection = $database->connect();

//CALL MODEL    
$model = new DistributionModel($connection);
$result = $model->getdistributionhub();

//OUTPUT
echo json_encode($result);

?>