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
            "count" => $index->getIntransitCount(),
            "data" => $index->getIntransitlist()
        ));
    }

    public function list(){
        $list = null;
        $module = isset($_GET['module']) ? $_GET['module'] : "";
        switch($module){
            case "intransit":
                $model = new PartnerPortalModel($this->connection);
                $list = $model->getintransit();

                $columns = array("loaded_date","container_no","eta");
                echo $this->twig->render('_generic_component/report/list_part.html', array(
                    "logindetails" =>  $_SESSION['logindetails'],
                    "breadcrumb" => $this->breadcrumb,
                    "list" => $list,        
                    "columns" => $columns,  
                    "module" => "PORTAL INTRANSIT INTERNATIONAL"              
                ));
                break;
            case "unloads":
                $model = new PartnerPortalModel($this->connection);
                $list = $model->getUnloads();       
                $columns = array("unload_date","time_start","time_end","container_number","arrival_time","qty","driver_and_plate_no",);
                
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
                $columns = array("date","destination","customer","receiver","box_number","driver_name","status");

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
            case "dist":
                $model = new PartnerPortalModel($this->connection);
                $list = $model->getdistlocal();         
                $columns = array("date","type","mode_of_shipment","destination","truck_number","driver_name","remarks","qty","eta");
                $moduledescription = "PORTAL DISTRIBUTION";
        
                echo $this->twig->render('_generic_component/report/list_part.html', array(
                    "logindetails" =>  $_SESSION['logindetails'],
                    "breadcrumb" => $this->breadcrumb,
                    "list" => $list,            
                    "columns" => $columns,  
                    "module" => $moduledescription                                            
                )); 
                break;
            default:
                break;
        }

    }

}

