<?php

class ExpenseController extends GenericController
{
    public function __construct()
    {
        parent::__construct();
        require_once __DIR__ . "/../model/ExpenseModel.php";
    }

    //LIST
    public function list()
    {   
        $model = new ExpenseModel($this->connection);
        $list = $model->getlist();        
        $columns = array("employee_name","amount","account_chart","due_date","approved_by","status");
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
        
        $result =null;
        $expenseid = isset($_GET['id']) ? $_GET['id'] : null;
        
        $allstatus = array(
            array(
                "id" => "Pending",
                "name" => "Pending"
            ),
            array(
                "id" => "Approved",
                "name" => "Approved"
            )
        );

        if(isset($expenseid)){
            $model = new ExpenseModel($this->connection);
            $result = $model->getexpensebyid($expenseid);
        }

        echo $this->twig->render('finance-management/expense/edit.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,
            "allemployee" => $this->allemployee,
            "allchartaccounts" => $this->allchartaccounts,
            "allbranch" => $this->allbranch,
            "allstatus" => $allstatus,
            "result" => $result
        ));
    }

    public function save()
    {        
        $expenseid = isset($_POST['expenseid']) ? $_POST['expenseid'] : "";

        $filename = $_FILES['documents']['name'];    
        $destination = "documents/". $filename;          
        copy($_FILES['documents']['tmp_name'],$destination);

        $data = array(
            "employee_id" => (isset($_POST['employee_id']) ? $_POST['employee_id'] : ""),
            "amount" => (isset($_POST['amount']) ? $_POST['amount'] : ""),
            "chart_accounts" => (isset($_POST['chart_accounts']) ? $_POST['chart_accounts'] : ""),
            "description" => (isset($_POST['description']) ? $_POST['description'] : ""),
            "status" => (isset($_POST['status']) ? $_POST['status'] : "Pending"),
            "due_date" => (isset($_POST['due_date']) ? $_POST['due_date'] : null),
            "approved_by" => (isset($_POST['approved_by']) ? $_POST['approved_by'] : ""),
            "documents" => ($destination <> "documents/") ? $destination : $_POST['documents'],
            "approved_date" => (isset($_POST['approved_date']) ? $_POST['approved_date'] :  $_POST['status'] == "Approved" ? date('Y-m-d') : null),            
        );
        if($expenseid <> ""){
            $model = new ExpenseModel($this->connection);
            $result = $model->update($expenseid,$data);
        }else{
            $model = new GenericModel($this->connection);
            $result = $model->insert($data,"gpx_expense");
        }
        
        if($result == 1)
            header("Location: index.php?controller=expense&action=list");
       
    }

}

