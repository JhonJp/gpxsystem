<?php

class DeliveryController extends GenericController
{
    public function __construct()
    {
        parent::__construct();
        //require_once __DIR__ . "/../model/DeliveryModel.php";
    }

    //LIST
    public function list()
    {       
        $model = new DeliveryModel($this->connection);
        $list = $model->getlist();         
        $columns = array("delivered_date","destination","delivered_by","customer","receiver","box_number","received_by","status");
        $moduledescription = "List of all delivered box numbers";
        
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

    public function view()
    {        
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        $model = new DeliveryModel($this->connection);
        $result = $model->checkdata($id); 
        $box_numbers = $model->getdeliveryboxnumber($id);
        $image = $model->getimages($id); 

        echo $this->twig->render('cargo-management/delivery/view.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,     
            "result" => $result,
            "images" => $image,     
            "box_numbers" =>$box_numbers,     
        ));
    }

}

