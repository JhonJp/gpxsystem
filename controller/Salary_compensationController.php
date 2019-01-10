<?php

class Salary_compensationController extends GenericController
{
    public function __construct()
    {
        parent::__construct();
        require_once __DIR__ . "/../model/SalarycompensationModel.php";
    }

    //LIST
    public function list()
    {     
        $model = new SalarycompensationModel($this->connection);
        $list = $model->getlist();        
        $columns = array("employee","account_chart","date_from","date_to","amount");
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
        $id = isset($_GET['id']) ? $_GET['id']  : "";
        if(isset($id)){
            $model = new SalarycompensationModel($this->connection);
            $result = $model->getsalarybyid($id);
        }

        echo $this->twig->render('finance-management/salary-compensation/edit.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,
            "allemployee" => $this->allemployee,
            "allchartaccounts" => $this->allchartaccounts,
            "result" => $result
        ));
    }

    public function save()
    {
        $getid = $_POST['id'];
        if($this->checkID($getid) == 0){
            $daterange = explode('-',$_POST['daterange']);
            $data = array(
                "employee_id" => (isset($_POST['employee_id']) ? $_POST['employee_id'] : ""),
                "chart_accounts" => (isset($_POST['chart_accounts']) ? $_POST['chart_accounts'] : ""),
                "date_from" => date('Y-m-d',strtotime($daterange[0])),
                "date_to" => date('Y-m-d',strtotime($daterange[1])),
                "amount" => (isset($_POST['amount']) ? $_POST['amount'] : ""),
                "description" => (isset($_POST['description']) ? $_POST['description'] : ""),
                "createdby" => $this->current_userid
            );
            
            $model = new GenericModel($this->connection);
            $result = $model->insert($data,SALARYCOMPENSATION);
            if($result == 1)
                header("Location: index.php?controller=salary_compensation&action=list");
        }else{
            $daterange = explode('-',$_POST['daterange']);
            $data = array(
                "employee_id" => (isset($_POST['employee_id']) ? $_POST['employee_id'] : ""),
                "chart_accounts" => (isset($_POST['chart_accounts']) ? $_POST['chart_accounts'] : ""),
                "date_from" => date('Y-m-d',strtotime($daterange[0])),
                "date_to" => date('Y-m-d',strtotime($daterange[1])),
                "amount" => (isset($_POST['amount']) ? $_POST['amount'] : ""),
                "description" => (isset($_POST['description']) ? $_POST['description'] : ""),
                "createdby" => $this->current_userid
            );
            
            $model = new GenericModel($this->connection);
            $result = $model->deleteSalaryCompensate($getid,"gpx_salary_compensation");
            $result = $model->insert($data,"gpx_salary_compensation");
            if($result == 1)
                header("Location: index.php?controller=salary_compensation&action=list");
        }    
       
    }


  public function checkID($id)
    {
        $result = 0;
        $query = $this->connection->prepare("SELECT * FROM gpx_salary_compensation
        WHERE id = :id");
        $query->execute(array(
            "id" => $id,
        ));
        $result = $query->fetchAll();
        return count($result);
    }

}

