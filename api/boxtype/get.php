<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once "../../config/apidatabase.php";
include_once "../../model/BoxTypeModel.php";

//CONNECT TO DATABASE
$database = new Database();
$connection = $database->connect();;

//CALL MODEL    
$boxtype = new BoxTypeModel($connection);
$result = $boxtype->getlist();

//OUTPUT
echo json_encode($result);

?>