<?php

class LoadingController extends GenericController
{
    public function __construct()
    {
        parent::__construct();
        require_once __DIR__ . "/../model/LoadingModel.php";
    }

    //LIST
    public function list()
    {       
        $model = new LoadingModel($this->connection);
        $list = $model->getlist();  
        $columns = array("loaded_date","shipping_line","container_no","etd","eta","qty","loaders_name","branch");
        $moduledescription = "List of all loaded in container";
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
        $model = new LoadingModel($this->connection);
        $result = $model->getdetails($transaction_no); 
        $box_numbers = $model->getboxnumber($transaction_no);

        echo $this->twig->render('cargo-management/loading/view.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,     
            "result" => $result,   
            "box_numbers" => $box_numbers,     
        ));
    }

}

