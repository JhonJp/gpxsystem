<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once "../../config/apidatabase.php";
include_once "../../model/ApiModel.php";
include_once "../../model/RemittanceModel.php";

$database = new Database();
$connection = $database->connect();

$id = $_GET['id'];
$model = new RemittanceModel($connection);
$result = $model->getRemittanceByOic($id);

echo json_encode($result);

?>