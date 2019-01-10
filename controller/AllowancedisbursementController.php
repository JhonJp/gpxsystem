<?php

class AllowancedisbursementController extends GenericController
{
    public function __construct()
    {
        parent::__construct();
        require_once __DIR__ . "/../model/AllowancedisbursementModel.php";
    }

    //LIST
    public function list()
    {     
        $model = new AllowancedisbursementModel($this->connection);
        $list = $model->getlist();        
        $columns = array("employee","account_chart","disbursement_date","amount");
        echo $this->twig->render('_generic_component/list.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,
            "list" => $list,            
            "columns" => $columns, 
            "url" => $_SERVER['REQUEST_URI'],                    
        ));  
    }

    public function edit()
    {
        $result = null;
        if(isset($_GET['id'])){
            $model = new AllowancedisbursementModel($this->connection);
            $result = $model->getallowancebyid($_GET['id']);
        }
        echo $this->twig->render('finance-management/allowance-disbursement/edit.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,
            "allemployee" => $this->allemployee,
            "allchartaccounts" => $this->allchartaccounts,
            "result" => $result
        ));
    }

    public function save()
    {
        $id = isset($_POST['id']) ? $_POST['id'] : "" ;

        // $filename = $_FILES['documents']['name'];
        // $destination = "documents/". $filename;        
        // copy($_FILES['documents']['tmp_name'],$destination);

        $data = array(
            "employee_id" => (isset($_POST['employee_id']) ? $_POST['employee_id'] : ""),
            "chart_accounts" => (isset($_POST['chart_accounts']) ? $_POST['chart_accounts'] : 1),
            "disbursement_date" => (isset($_POST['disbursement_date']) ? $_POST['disbursement_date'] : ""),            
            "amount" => (isset($_POST['amount']) ? $_POST['amount'] : ""),
            "description" => (isset($_POST['description']) ? $_POST['description'] : ""),
            //"documents" => ($destination <> "documents/") ? $destination : null,
            "createdby" => $this->current_userid
        );
       

        if($id <> ""){
            $model = new AllowancedisbursementModel($this->connection);
            $result = $model->update($id,$data);
        }
        else{        
            $model = new GenericModel($this->connection);
            $result = $model->insert($data,ALLOWANCEDISBURSEMENT);
        }

        if($result == 1){}
            header("Location: index.php?controller=allowancedisbursement&action=list");
       
    }

}

