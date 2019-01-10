<?php

class RemittanceController extends GenericController
{
    public function __construct()
    {
        parent::__construct();
        require_once __DIR__ . "/../model/RemittanceModel.php";
    }

    //LIST
    public function list()
    {       
        $model = new RemittanceModel($this->connection);
        $list = $model->getlist();        
        $columns = array("branch","remitted_by","bank","amount","verified_by");
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
        $result = array(
            array(            
            "verified_by" => $this->current_userid
            )
        );

        if(isset($_GET['id'])){
            $model = new RemittanceModel($this->connection);
            $result = $model->getremittancebyid($_GET['id']);
        }

        echo $this->twig->render('finance-management/remittance/edit.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,
            "allemployee" => $this->allemployee,
            "allchartaccounts" => $this->allchartaccounts,
            "allbranch" => $this->allbranch,
            "result" => $result
        ));
    }

    public function save()
    {
        $id = isset($_POST['id']) ? $_POST['id'] : "" ;

        $data = array(
            "branch_source" => (isset($_POST['branch_source']) ? $_POST['branch_source'] : ""),
            "chart_accounts" => (isset($_POST['chart_accounts']) ? $_POST['chart_accounts'] : ""),
            "bank" => (isset($_POST['bank']) ? $_POST['bank'] : ""),
            "remitted_by" => (isset($_POST['remitted_by']) ? $_POST['remitted_by'] : ""),
            "remitted_amount_sales_driver" => (isset($_POST['remitted_amount_sales_driver']) ? $_POST['remitted_amount_sales_driver'] : ""),
            "remitted_amount_oic" => (isset($_POST['remitted_amount_oic']) ? $_POST['remitted_amount_oic'] : ""),
            "verified_by" => (isset($_POST['verified_by']) ? $_POST['verified_by'] : ""),
            "description" => (isset($_POST['description']) ? $_POST['description'] : ""),
            "createdby" => $this->current_userid
        );
        if($id <> ""){
            $model = new RemittanceModel($this->connection);
            $result = $model->update($data,$id);
        }
        else{
            $model = new GenericModel($this->connection);
            $result = $model->insert($data,REMITTANCE);
        }
        if($result == 1)
            header("Location: index.php?controller=remittance&action=list");
    }

}

