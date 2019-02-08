<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once "../../config/apidatabase.php";
include_once "../../model/GenericModel.php";

//CONNECT TO DATABASE
$database = new Database();
$connection = $database->connect();;

//CALL MODEL    
$boxtype = new GenericModel($connection);
$result = $boxtype->getnsbrates();

//OUTPUT
echo json_encode($result);

?>