<?php

class RoleController extends GenericController
{
    public function __construct()
    {
        parent::__construct();
        require_once __DIR__ . "/../model/RoleModel.php";
    }

    //LIST
    public function list()
    {
        $model = new RoleModel($this->connection);
        $list = $model->getlist();        
        $columns = array("name","description");
        echo $this->twig->render('_generic_component/list.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,
            "list" => $list,            
            "columns" => $columns,                     
        ));        
    }

    public function edit()
    {
        $result = null; 
        echo $this->twig->render('user-management/roles/edit.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,
            "result" => $result
        ));
    }

    public function save()
    {
       
    }

}

