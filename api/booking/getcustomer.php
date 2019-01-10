<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once "../../config/apidatabase.php";
include_once "../../model/BookingModel.php";

//CONNECT TO DATABASE
$database = new Database();
$connection = $database->connect();;

//CALL MODEL    
$model = new BookingModel($connection);
$result = $model->getcustomer($_GET['reservation_no']);

//OUTPUT
echo json_encode($result);

?>