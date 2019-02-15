<?php

class InventoryController extends GenericController
{
    public function __construct()
    {
        parent::__construct();
        require_once __DIR__ . "/../model/WarehouseInventoryModel.php";
    }

    //LIST
    public function list()
    {       
        $model = new WarehouseInventoryModel($this->connection);
        $list = $model->getlist();                
        $columns = array("warehouse_name","manufacturer_name","box_type_and_quantity");   
        $moduledescription = "List of quantity per box type";             
        $image = "List of quantity per box type";
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

    //VIEW PER ITEM ON INVENTORY
    public function view()
    {
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        $model = new WarehouseInventoryModel($this->connection);
        if (isset($id)) {
            $data = $model->getdata($id);
            $image = $model->getimages($id);
            $tabledata = $model->getboxes($data[0]['manufacturer_name'],$data[0]['warehouse_id']);
        }
        echo $this->twig->render('cargo-management/inventory/view.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,
            "data" => $data,    
            "result" => $tabledata,    
            "images" => $image,    
        ));
    }

}

