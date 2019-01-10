<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once "../../config/apidatabase.php";
include_once "../../model/ApiModel.php";

//CONNECT TO DATABASE
$database = new Database();
$connection = $database->connect();;

//CALL MODEL
$reservation = new ApiModel($connection);
$id = isset($_GET['id']) ? $_GET['id'] : 0;
$result = $reservation->getallpendingreservation($id);

//OUTPUT
if(isset($result)){
    echo json_encode($result);
}
else{
    echo json_encode("No Data Found!");
}
?>