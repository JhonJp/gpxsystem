<?php

class PartnerPortalController extends GenericController
{
    public function __construct()
    {
        parent::__construct();
        require_once __DIR__ . "/../model/PartnerPortalModel.php";
    }

    public function partnerdashboard()
    {   
        $index = new PartnerPortalModel($this->connection);
        $status = "1";
        echo $this->twig->render('/partner_portal/dashboard.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "deliveries" => $index->countdeliveries(),
            "unloaded" => $index->countUnloaded(),
            "customer" => $index->countCustomers('customer'),
        ));
    }

    public function list(){
        $list = null;
        $module = isset($_GET['module']) ? $_GET['module'] : "";
        switch($module){
            case "intransit":
                $model = new PartnerPortalModel($this->connection);
                $list = $model->getintransit();

                $columns = array("date","truck_number","destination_name","box_number");

                echo $this->twig->render('_generic_component/report/list_part.html', array(
                    "logindetails" =>  $_SESSION['logindetails'],
                    "breadcrumb" => $this->breadcrumb,
                    "list" => $list,        
                    "columns" => $columns,  
                    "module" => "PORTAL INTRANSIT "              
                ));
                break;
            case "unloads":
                $model = new PartnerPortalModel($this->connection);
                $list = $model->getUnloads();       
                $columns = array("unload_date","container_no","forwarder_name","arrival_time","qty","box_number");
                
                echo $this->twig->render('_generic_component/report/list_part.html', array(
                    "logindetails" =>  $_SESSION['logindetails'],
                    "breadcrumb" => $this->breadcrumb,
                    "list" => $list,        
                    "columns" => $columns,  
                    "module" => "PORTAL UNLOADED "              
                ));
                break;
            case "deliver":
                $model = new PartnerPortalModel($this->connection);
                $list = $model->getDeliveries();
                $columns = array("customer","receiver","box_number","delivered_date","status");

                echo $this->twig->render('_generic_component/report/list_part.html', array(
                    "logindetails" =>  $_SESSION['logindetails'],
                    "breadcrumb" => $this->breadcrumb,
                    "list" => $list,        
                    "columns" => $columns,  
                    "module" => "PORTAL DELIVERY"              
                ));
                break;
            case "tickets":
                $model = new PartnerPortalModel($this->connection);
                $list = $model->getTickets();        
                $columns = array("ticket_no","transaction_no","ticket_type","account_no","priority","status","assigned_to");
                echo $this->twig->render('_generic_component/report/list_part.html', array(
                    "logindetails" =>  $_SESSION['logindetails'],
                    "breadcrumb" => $this->breadcrumb,
                    "list" => $list,        
                    "columns" => $columns,  
                    "module" => "PORTAL TICKET"                 
                ));
                break;
            default:
                break;
        }

    }

}

