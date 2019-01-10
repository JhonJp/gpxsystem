<?php

class FinancialliquidationController extends GenericController
{
    public function __construct()
    {
        parent::__construct();
        require_once __DIR__ . "/../model/FinancialLiquidationModel.php";
    }

    //LIST
    public function list()
    {     
        $model = new FinancialLiquidationModel($this->connection);
        $list = $model->getlist();        
        $columns = array("employee","liquidation_date","amount","description");
        echo $this->twig->render('_generic_component/list.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,
            "list" => $list,            
            "columns" => $columns,                     
        ));  
    }

    public function edit()
    {
        $result = null;
        if(isset($_GET['id'])){
            $model = new FinancialLiquidationModel($this->connection);
            $result = $model->getfinancialbyid($_GET['id']);
        }
        echo $this->twig->render('finance-management/financial-liquidation/edit.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,
            "allemployee" => $this->allemployee,
            "result" => $result
        ));
    }

    public function save()
    {
        $id = isset($_POST['id']) ? $_POST['id'] : "" ;

        $data = array(
            "employee_id" => (isset($_POST['employee_id']) ? $_POST['employee_id'] : ""),
            "liquidation_date" => (isset($_POST['liquidation_date']) ? $_POST['liquidation_date'] : ""),            
            "amount" => (isset($_POST['amount']) ? $_POST['amount'] : ""),
            "description" => (isset($_POST['description']) ? $_POST['description'] : ""),
            "createdby" => $this->current_userid
        );
        if($id <> ""){
            $model = new FinancialLiquidationModel($this->connection);
            $result = $model->update($id,$data);
        }
        else{
            $model = new GenericModel($this->connection);
            $result = $model->insert($data,FINANCIALLIQUIDATION);
        }
        if($result == 1)
            header("Location: index.php?controller=financialliquidation&action=list");
    
    }

}

