<?php

class TicketController extends GenericController
{
    public function __construct()
    {
        parent::__construct();
        require_once __DIR__ . "/../model/TicketModel.php";
    }

    //LIST
    public function list()
    {     
        $model = new TicketModel($this->connection);
        $list = $model->getlist();        
        $columns = array("ticket_no","customer_name","description","ticket_type","status","assigned_to");
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
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        $result = null; 
        if(isset($id)){
            $model = new TicketModel($this->connection);
            $result = $model->getticketbyid($id);    
        }

        echo $this->twig->render('customer-management/ticket/edit.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,
            "allltickettype" => $this->alltickettype,
            "allcustomers" => $this->allcustomers,
            "allemployee" => $this->allemployee,
            "allpriority" => $this->allticketpriority,
            "allstatus" => $this->allticketstatus,
            "alltransactionsno" => $this->alltransactionsno,
            "result" => $result
        ));
    }

    public function save()
    {
        
        $ticketid = isset($_POST['ticketid']) ? $_POST['ticketid'] : "";

        $data = array(
                "ticket_no" => "T".$this->current_userid.date('mHis'),
                "ticket_type" => (isset($_POST['ticket_type']) ? $_POST['ticket_type'] : ""),
                "account_no" => (isset($_POST['account_no']) ? $_POST['account_no'] : ""),
                "status" => (isset($_POST['status']) ? $_POST['status'] : ""),
                "assigned_to" => (isset($_POST['assigned_to']) ? $_POST['assigned_to'] : ""),
                "description" => (isset($_POST['description']) ? $_POST['description'] : ""),
                "created_by" =>  $this->current_userid
        );

        if($ticketid <> ""){
            $model = new TicketModel($this->connection);
            $result = $model->update($ticketid,$data);
        }
        else{
            $model = new GenericModel($this->connection);
            $result = $model->insert($data,"gpx_tickets");
        }

        if($result == 1)
            header("Location: index.php?controller=ticket&action=list");
    }

}

