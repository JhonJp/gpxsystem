<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once "../../config/apidatabase.php";
include_once "../../model/GenericModel.php";

//CONNECT TO DATABASE
$database = new Database();
$connection = $database->connect();;

//CALL MODEL    
$model = new GenericModel($connection);
$id = isset($_GET['id']) ? $_GET['id'] : 0;
$counting = $model->getcountbydriver($id);

//OUTPUT
if ($counting == 0 ){
    echo json_encode("No Data Found!");
}
else{
    $result = $model->getboxnumbersbyDriver($id);
    echo json_encode($result);
}
?>