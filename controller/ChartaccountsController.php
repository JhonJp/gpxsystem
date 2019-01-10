<?php

class ChartaccountsController extends GenericController
{
    public function __construct()
    {
        parent::__construct();
        require_once __DIR__ . "/../model/ChartaccountsModel.php";
    }

    //LIST
    public function list()
    {       
        $model = new ChartaccountsModel($this->connection);
        $list = $model->getlist();        
        $columns = array("account_code","account_chart","account_type");
        echo $this->twig->render('_generic_component/list.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,         
            "columns" => $columns,              
            "list" => $list,   
            "url" => $_SERVER['REQUEST_URI'],              
        ));    
    }

    public function edit()
    {
        $result = null;
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        if(isset($id)){
            $model = new ChartaccountsModel($this->connection);
            $result = $model->getchartaccountbyid($id);     
        }

        echo $this->twig->render('finance-management/chart-accounts/edit.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,
            "chartaccounttype" => $this->allchartaccounttype,
            "result" => $result
        ));
    }

    public function save(){

        $id = isset($_POST['id']) ? $_POST['id'] : "";

        $data = array(
            "account_code" => (isset($_POST['account_code']) ? $_POST['account_code'] : ""),
            "account_chart" => (isset($_POST['account_chart']) ? $_POST['account_chart'] : ""),
            "account_type" => (isset($_POST['account_type']) ? $_POST['account_type'] : ""),
        );
        if($id <> ""){
            $model = new ChartaccountsModel($this->connection);
            $result = $model->update($id,$data);
        }else{
            $model = new GenericModel($this->connection);
            $result = $model->insert($data,CHARTACCOUNT);
        }
        if($result == 1)
            header("Location: index.php?controller=chartaccounts&action=list");
    }

}

