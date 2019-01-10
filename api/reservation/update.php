<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');


include_once "../../config/apidatabase.php";
include_once "../../model/ApiModel.php";

$database = new Database();
$connection = $database->connect();

$reservation = new ApiModel($connection);
$data = file_get_contents('php://input');  
//$data = json_decode(file_get_contents('http://localhost:90/gpx-web-central/data.json'));                                               
//$result = $reservation->savereservation(json_decode($data));
$result = $reservation->testsave($data);
echo json_encode($result);

?>