<?php

class AcceptanceController extends GenericController
{
    public function __construct()
    {
        parent::__construct();
        require_once __DIR__ . "/../model/WarehouseAcceptanceModel.php";
    }

    //LIST
    public function list()
    {       
        $model = new WarehouseAcceptanceModel($this->connection);
        $list = $model->getlist();         
        $columns = array("accepted_date","warehouse_name","deliver_by","truck_no","accepted_by","qty","box_number");
        $moduledescription = "List of all accepted box numbers per warehouse";
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

    public function view()
    {        
        $transaction_no = isset($_GET['transaction_no']) ? $_GET['transaction_no'] : null;
        $model = new WarehouseAcceptanceModel($this->connection);
        $result = $model->getdetails($transaction_no); 
        $box_numbers = $model->getboxnumber($transaction_no);

        echo $this->twig->render('cargo-management/warehouseacceptance/view.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,     
            "result" => $result,   
            "box_numbers" => $box_numbers,     
        ));
    }

}

