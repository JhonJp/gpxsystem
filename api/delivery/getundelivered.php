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
$counting = $model->countundelivered();
$result = $model->getundelivered();

//OUTPUT
if ($counting == 0){
    echo "No Data Found!";
}else{
    echo json_encode($result);
}
?>