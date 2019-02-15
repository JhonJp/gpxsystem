<?php

class MaintenanceController extends GenericController
{
    public function __construct()
    {
        parent::__construct();
        require_once __DIR__ . "/../model/GenericModel.php";
        require_once __DIR__ . "/../model/TicketModel.php";
    }


    public function list()
    {
        $module = isset($_GET['module']) ? $_GET['module'] : "";
        switch($module){
            CASE "accounttype" :
                $model = new GenericModel($this->connection);
                $list = $model->getmaintenancelist("gpx_chartaccounts_type");
                $controller = "Account Type"; 
                break;
            CASE "ticket" :
                $model = new GenericModel($this->connection);
                $list = $model->getmaintenancelist("gpx_tickets_type");
                $controller = "Ticket";
                break;
            CASE "undelivered" :
                $model = new GenericModel($this->connection);
                $list = $model->getmaintenancelist("gpx_delivery_substatus");
                $controller = "Undelivered Reasons"; 
                break;
            default : 
                $list = null;
                $controller = "";
                break;
        }   

        $breadcrumb = array(
            "controller" => $controller,
            "module" => $module,
            "action" => "list"
        );

        $columns = array("name","description");
        echo $this->twig->render('_generic_component/list.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "breadcrumb" => $breadcrumb,
            "list" => $list,            
            "columns" => $columns,  
            "url" => $_SERVER['REQUEST_URI'],                   
        ));  
    }

    public function edit()
    {
        $module = isset($_GET['module']) ? $_GET['module'] : "";
        $id = isset($_GET['id']) ? $_GET['id'] : "";
        switch($module){
            CASE "accounttype" :
                $model = new GenericModel($this->connection);
                $result = $model->getmaintenancelistbyid("gpx_chartaccounts_type",$id);
                $controller = "Account Type";
                break;
            CASE "ticket" :
                $model = new GenericModel($this->connection);
                $result = $model->getmaintenancelistbyid("gpx_tickets_type",$id);
                $controller = "Ticket";
                break;
            CASE "undelivered" :
                $model = new GenericModel($this->connection);
                $result = $model->getmaintenancelistbyid("gpx_delivery_substatus",$id);
                $controller = "Undelivered Reasons";
                break;
            default : 
                $list = null;
                $controller = "";
                $result = null;
                break;
        } 

        $breadcrumb = array(
            "controller" => $controller,
            "module" => $module,
            "action" => "edit"            
        );

        echo $this->twig->render('settings/maintenance/edit.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "breadcrumb" => $breadcrumb,
            "result" => $result
        ));
    }

    public function save(){

        $module = isset($_POST['module']) ? $_POST['module'] : "";
        $id = isset($_POST['id']) ? $_POST['id'] : "";

        switch($module){
            CASE "accounttype" :
                $table = "gpx_chartaccounts_type";                
                break;
            CASE "ticket" :
                $table = "gpx_tickets_type";                
                break;
            CASE "undelivered" :
                $table = "gpx_delivery_substatus";                
                break;
            default : 
                $table = "";
                break;
        } 

        $data = array(
            "name" => (isset($_POST['name']) ? $_POST['name'] : ""),
            "description" => (isset($_POST['description']) ? $_POST['description'] :  $_POST['name']),
        );
        
        if($id <> ""){
            $model = new GenericModel($this->connection);
            $result = $model->maintenanceUpdate($data,$table,$id);
        }
        else{
            $model = new GenericModel($this->connection);
            $result = $model->insert($data,$table);
        }   
        if($result == 1)
            header("Location: index.php?controller=maintenance&action=list&module={$module}");
    }


}

