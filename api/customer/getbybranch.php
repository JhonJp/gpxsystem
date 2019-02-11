<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once "../../config/apidatabase.php";
include_once "../../model/GenericModel.php";

//CONNECT TO DATABASE
$database = new Database();
$connection = $database->connect();;

//CALL MODEL    
$customers = new GenericModel($connection);
$id = isset($_GET['branchid']) ? $_GET['branchid'] : 0;
$result = $customers->getallcustomersbybranch($id);

//OUTPUT
echo json_encode($result);

?>