<?php

class BoxrateController extends GenericController
{
    public function __construct()
    {
        parent::__construct();
        require_once __DIR__ . "/../model/BoxRateModel.php";
    }

    public function list()
    {
        $model = new BoxRateModel($this->connection);
        $list = $model->getlist();        
        $columns = array("boxtype","cbm","source","destination","amount");
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
        $result = null;
        if(isset($_GET['id'])){
            $model = new BoxRateModel($this->connection);
            $result = $model->getboxratebyid($_GET['id']);
        }
        echo $this->twig->render('settings/boxrate/edit.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,
            "allboxtype" => $this->allboxtype,            
            "allsource" => $this->allsource,  
            "alldestination" => $this->alldestination, 
            "allcurrency" => $this->allcurrency,
            "result" => $result
        ));
    }

    public function save(){

        $id = isset($_POST['id']) ? $_POST['id'] : "" ;

        $data = array(
            "boxtype_id" => (isset($_POST['boxtype_id']) ? $_POST['boxtype_id'] : ""),           
            "cbm" => (isset($_POST['cbm']) ? $_POST['cbm'] :  0),
            "source_id" => (isset($_POST['source_id']) ? $_POST['source_id'] : 1),
            "destination_id" => (isset($_POST['destination_id']) ? $_POST['destination_id'] : 1),
            "currency_id" => (isset($_POST['currency_id']) ? $_POST['currency_id'] : 1),
            "amount" => (isset($_POST['amount']) ? $_POST['amount'] :  0),
        );
        
        if($id <> ""){
            $model = new BoxRateModel($this->connection);
            $result = $model->update($id,$data);
        }
        else{
            $model = new GenericModel($this->connection);
            $result = $model->insert($data,"gpx_boxrate");
        }
        if($result == 1)
            header("Location: index.php?controller=boxrate&action=list");
    }



}

