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
$count = $model->getcountdirects();
$result = $model->getdistributiondirect();

//OUTPUT
if($count == 0){
    echo "No Data Found!";
}else{
    echo json_encode($result);
}

?>