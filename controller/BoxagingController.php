<?php

class BoxagingController extends GenericController
{
    public function __construct()
    {
        parent::__construct();
        require_once __DIR__ . "/../model/BoxAgingModel.php";
    }

    //LIST
    public function list()
    {
        $model = new BoxAgingModel($this->connection);
        $list = $model->getlist();         
        $columns = array("unloaded_date","box_number","receiver","destination","last_status","age");
        $moduledescription = "Box Aging";
        echo $this->twig->render('_generic_component/list.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,
            "list" => $list,            
            "columns" => $columns,           
            "url" => $_SERVER['REQUEST_URI'],
            "moduledescription" => $moduledescription                                            
        ));       
    }

    public function edit()
    {
        
    }

    public function save()
    {
        
    }

}

