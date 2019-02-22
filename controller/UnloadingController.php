<?php

class UnloadingController extends GenericController
{
    public function __construct()
    {
        parent::__construct();
        require_once __DIR__ . "/../model/UnloadingModel.php";
    }

    //LIST
    public function list()
    {       
        $model = new UnloadingModel($this->connection);
        $list = $model->getlist();         
        $columns = array("unload_date","container_no","forwarder_name","time_start","time_end","arrival_time","qty");
        $moduledescription = "List of all unloaded";
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
        $transaction_no = isset($_GET['transaction_no']) ? $_GET['transaction_no'] : null;
        $model = new UnloadingModel($this->connection);
        $result = $model->getdetails($transaction_no); 
        $box_numbers = $model->getboxnumber($transaction_no);

        echo $this->twig->render('cargo-management/unloading/view.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,     
            "result" => $result,   
            "box_numbers" => $box_numbers,     
        ));
    }

}

