<?php

class DistributionController extends GenericController{
    
    public function __construct() {
        parent::__construct();    
    }

    //LIST
    public function list()
    {
        $model = new DistributionModel($this->connection);
        $list = $model->getlist();         
        $columns = array("type","destination","truck_number","remarks","qty","box_number");
        $moduledescription = "List of all distributed box type";

        echo $this->twig->render('_generic_component/list.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,
            "list" => $list,            
            "columns" => $columns,           
            "url" => $_SERVER['REQUEST_URI'],
            "moduledescription" => $moduledescription                                            
        ));    
    }

    public function view()
    {              
        $id = isset($_GET['id']) ? $_GET['id'] : null; 
        $image = null;       
        $model = new DistributionModel($this->connection);
        $result = $model->getdetails($id);      
        $boxnumber = $model->getboxnumber($id);
        $image = $model->getimages($id);      

        echo $this->twig->render('cargo-management/distribution/view.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,  
            "result" => $result,
            "boxnumber" => $boxnumber,
            "images" => $image,
        ));        
    }
    

}

