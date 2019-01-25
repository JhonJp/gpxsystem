<?php

class IntransitController extends GenericController
{
    public function __construct()
    {
        parent::__construct();
        require_once __DIR__ . "/../model/InTransitModel.php";
    }

    //LIST
    public function list()
    {               
        $model = new InTransitModel($this->connection);
        $list = $model->getlist();
        $columns = array("container_no","etd","eta","qty","box_number","status");
        echo $this->twig->render('_generic_component/list.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,
            "list" => $list,            
            "columns" => $columns, 
            "url" => $_SERVER['REQUEST_URI'],                        
        ));        
    }

    public function edit()
    {     
        $model = new InTransitModel($this->connection);
        $container_no = isset($_GET['container_no']) ? $_GET['container_no'] : null;      
        $status = null;
        $result = null;
        $history = null;
        if (isset($container_no)) {
            $status = $model->getIntransitStatus($container_no);
            $result = $model->getdetailsbyid($container_no);
            $history = $model->gethistory($container_no);
        }

        echo $this->twig->render('cargo-management/in-transit/edit.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,         
            "result" => $result,            
            "status" => $status,            
            "history" => $history,            
        ));
    }

    public function save()
    {
        $container_no = isset($_POST['container_no'])  ?  $_POST['container_no'] : "";
        $eta = isset($_POST['eta'])  ?  $_POST['eta'] : "";
        $etd = isset($_POST['etd'])  ?  $_POST['etd'] : "";
        $status = isset($_POST['intransstat'])  ?  $_POST['intransstat'] : "";
        $model = new InTransitModel($this->connection);
        $result = $model->updateLoading($container_no,$eta,$etd,$status);

        header("Location: index.php?controller=intransit&action=list");
    }

}

