<?php

class IncidentreportController extends GenericController
{
    public function __construct()
    {
        parent::__construct();
        require_once __DIR__ . "/../model/IncidentReportModel.php";
    }

    //LIST
    public function list()
    {     
        $model = new IncidentReportModel($this->connection);
         $list = $model->getlist();        
        $columns = array("module","incident_type","reason","box_number");
        echo $this->twig->render('_generic_component/list.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,
            "list" => $list,            
            "columns" => $columns,   
            "url" => $_SERVER['REQUEST_URI'],                     
        ));  
  
    }
    
    public function view()
    {        
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        $model = new IncidentReportModel($this->connection);

        $result = $model->getincidentreportbyid($id); 
        $images = $model->getimages($id); 

        echo $this->twig->render('cargo-management/incident-report/view.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,     
            "result" => $result,     
            "images" => $images,     
        ));
    }
    

    public function edit()
    {
    }

    public function save()
    {
       
    }

}

