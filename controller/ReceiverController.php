<?php

class ReceiverController extends GenericController
{
    public function __construct()
    {
        parent::__construct();
        require_once __DIR__ . "/../model/CustomerModel.php";
    }
    
    //LIST
    public function list()
    {
        $model = new ReservationModel($this->connection);
        $list = $model->getlist();        
        $columns = array("account_no","name","email","mobile");
        echo $this->twig->render('_generic_component/list.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,
            "list" => $this->allconsignee,            
            "columns" => $columns, 
            "url" => $_SERVER['REQUEST_URI'],                        
        ));        
    }

    public function edit()
    {
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        $result = null;
        $subreceiver = null;
        $allcity = null;
        $allbarangay = null;

        if (isset($id)) {
            $customer = new CustomerModel($this->connection);
            $result = $customer->getcustomerbyid($id);
            $customer = new CustomerModel($this->connection);
            $subreceiver = $customer->getsubreciever($result[0]['account_no']); 

            $customer = new CustomerModel($this->connection);
            $allcity = $customer->getallcity($result[0]['province']);    

            $customer = new CustomerModel($this->connection);
            $allbarangay = $customer->getallbarangay($result[0]['city']);            
        }
        echo $this->twig->render('customer-management/customer/edit.html', array(
            "logindetails" => $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,
            "result" => $result,
            "subreceiver" => $subreceiver,
            "allprovince" => $this->allprovince,
            "allcity" => $allcity,
            "allbarangay" => $allbarangay,
            "url" => $_SERVER['REQUEST_URI']
        ));
    }
    

}

