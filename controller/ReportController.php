<?php

class ReportController extends GenericController
{
    public function __construct()
    {
        parent::__construct();
        require_once __DIR__ . "/../model/ReportModel.php";
    }

    //LIST
    public function list()
    {       
        $list = null;
        $module = isset($_GET['module']) ? $_GET['module'] : "";
        
        if($module == 'boxpurchase'){

            $model = new ReportModel($this->connection);
            $list = $model->getboxpurchasereport();

            $columns = array("date","warehouse_name","manufacturer_name","box_type","qty");
        }
        else if($module == 'branchincentives'){
            //$model = new ReportModel($this->connection);
            $list = null;//$model->getboxpurchasereport();
            $columns = array("branch","employee_name","total_sales","5_%","extra","sales","qty","box_quota","incentive","salary","deductions","total");
        }

        echo $this->twig->render('_generic_component/report/list.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,
            "list" => $list,        
            "columns" => $columns,  
            "module" => "BOX PURCHASE REPORT"              
        ));  
    }

    public function  edit()
    {
    }

    public function save()
    {
       
    }

}

