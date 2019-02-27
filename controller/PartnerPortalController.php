<?php

class PartnerPortalController extends GenericController
{
    public function __construct()
    {
        parent::__construct();
        require_once __DIR__ . "/../model/PartnerPortalModel.php";
        require_once __DIR__ . "/../model/TicketModel.php";
        require_once __DIR__ . "/../model/UserModel.php";
    }

    public function partnerdashboard()
    {   
        $index = new PartnerPortalModel($this->connection);
        $status = "1";
        echo $this->twig->render('/partner_portal/dashboard.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,
            "deliveries" => $index->countdeliveries(),
            "unloaded" => $index->countUnloaded(),
            "customer" => $index->countCustomers('customer'),
            "count" => $index->getIntransitCount(),
            "data" => $index->getIntransitlist()
        ));
    }

    public function list()
    {
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
                    "module" => "INTRANSIT INTERNATIONAL"              
                ));
                break;
            case "unloads":
                $model = new PartnerPortalModel($this->connection);
                $list = $model->getUnloads($_SESSION['logindetails'][0]['id']);       
                $columns = array("arrival_time","container_number","unload_date","time_start","time_end","qty","driver_and_plate_no",);
                
                echo $this->twig->render('_generic_component/report/list_part.html', array(
                    "logindetails" =>  $_SESSION['logindetails'],
                    "breadcrumb" => $this->breadcrumb,
                    "list" => $list,        
                    "columns" => $columns,  
                    "module" => "PORTAL UNLOADED"              
                ));
                break;
            case "deliver":
                $model = new PartnerPortalModel($this->connection);
                $list = $model->getDeliveries();
                $columns = array("date","destination","customer","receiver","box_number","driver_name","received_by","status");

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
                    $columns = array("ticket_no","customer_name","description","ticket_type","status","assigned_to");
                    echo $this->twig->render('_generic_component/report/list_part.html', array(
                        "logindetails" =>  $_SESSION['logindetails'],
                        "breadcrumb" => $this->breadcrumb,
                        "list" => $list,        
                        "columns" => $columns,  
                        "module" => "PORTAL TICKET",
                        "url" => $_SERVER['REQUEST_URI'],                 
                    ));
                break;
            case "dist":
                $model = new PartnerPortalModel($this->connection);
                $list = $model->getdistlocal($_SESSION['logindetails'][0]['id']);         
                $columns = array("date","transaction_no","type","mode_of_shipment","destination","truck_number","driver_name","qty","etd","eta");
                $moduledescription = "PORTAL DISTRIBUTION";
        
                echo $this->twig->render('_generic_component/report/list_part.html', array(
                    "logindetails" =>  $_SESSION['logindetails'],
                    "breadcrumb" => $this->breadcrumb,
                    "list" => $list,            
                    "columns" => $columns,  
                    "module" => $moduledescription                                            
                )); 
                break;
            case "savenewticket":
                $this->savenewticket();
                break;
            case "editticket":
                $this->editticket();
                break;
            case "newticket":
                $id = isset($_GET['id']) ? $_GET['id'] : null;
                $result = null;
                $empmodel = new PartnerPortalModel($this->connection);
                $employee = $empmodel->getPartnerEmployee(); 
                if(isset($id)){
                    $model = new TicketModel($this->connection);
                    $result = $model->getticketbyid($id);    
                }

                echo $this->twig->render('partner_portal/newticket.html', array(
                    "logindetails" =>  $_SESSION['logindetails'],
                    "breadcrumb" => $this->breadcrumb,
                    "allltickettype" => $this->alltickettype,
                    "allcustomers" => $this->allcustomers,
                    "allemployee" => $employee,
                    "allpriority" => $this->allticketpriority,
                    "allstatus" => $this->allticketstatus,
                    "alltransactionsno" => $this->alltransactionsno,
                    "result" => $result
                ));
                break;
            default:
                break;
        }

    }

    public function edit()
    {        
        $module = isset($_GET['mode']) ? $_GET['mode'] : null;
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        $result = null;     
        $branch = null;
        $drivers = null; 
        $part = new PartnerPortalModel($this->connection); 

        switch($module){
            case "newemp":
                $branch = $part->getallbranchpartner($id);   
                if (isset($id)) {
                    $model = new EmployeeModel($this->connection);
                    $result = $model->getemployeebyid($id);
                }
                echo $this->twig->render('partner_portal/newemployee.html', array(
                    "logindetails" =>  $_SESSION['logindetails'],
                    "breadcrumb" => $this->breadcrumb,
                    "allbranch" => $branch,
                    "result" => $result
                ));
            break;
            case "dist":
                $drivers = $part->getPartnerDrivers(); 
                $result = $part->getdetails($module,$id); 
                $box_numbers = $part->getboxnumber($module,$id);
                $image = $part->getimages($module,$id); 
                echo $this->twig->render('partner_portal/edit/editdist.html', array(
                    "logindetails" =>  $_SESSION['logindetails'],
                    "breadcrumb" => $this->breadcrumb,     
                    "result" => $result,   
                    "box_numbers" => $box_numbers,     
                    "images" => $image,     
                    "drivers" => $drivers,     
                ));
            break;
            default:
                $branch = $part->getallbranchpartner($id);   
                    if (isset($id)) {
                        $model = new EmployeeModel($this->connection);
                        $result = $model->getemployeebyid($id);
                    }
                    echo $this->twig->render('partner_portal/newemployee.html', array(
                        "logindetails" =>  $_SESSION['logindetails'],
                        "breadcrumb" => $this->breadcrumb,
                        "allbranch" => $branch,
                        "result" => $result
                    ));
            break;

        }
    }

    public function save()
    {
        $id = isset($_POST['id']) ? $_POST['id'] : null;

        $data = array(
            "firstname" => (isset($_POST['firstname']) ? $_POST['firstname'] : ""),
            "lastname" => (isset($_POST['lastname']) ? $_POST['lastname'] : ""),
            "middlename" => (isset($_POST['middlename']) ? $_POST['middlename'] : ""),
            "birthdate" => (isset($_POST['birthdate']) ? $_POST['birthdate'] : null),
            "mobile" => (isset($_POST['mobile']) ? $_POST['mobile'] : ""),
            "position" => (isset($_POST['position']) ? $_POST['position'] : ""),
            "phone" => (isset($_POST['phone']) ? $_POST['phone'] : ""),
            "gender" => (isset($_POST['gender']) ? $_POST['gender'] : ""),            
            "email" => (isset($_POST['email']) ? $_POST['email'] : ""),         
            "fathersname" => (isset($_POST['fathersname']) ? $_POST['fathersname'] : ""),
            "mothersname" => (isset($_POST['mothersname']) ? $_POST['mothersname'] : ""),
            "civilstatus" => (isset($_POST['civilstatus']) ? $_POST['civilstatus'] : ""),
            "religion" => (isset($_POST['religion']) ? $_POST['religion'] : ""),
            "branch" => (isset($_POST['branch']) ? $_POST['branch'] : ""),            
            "house_number_street" => (isset($_POST['house_number_street']) ? $_POST['house_number_street'] : ""),            
            "barangay" => (isset($_POST['barangay']) ? $_POST['barangay'] : ""),
            "city" => (isset($_POST['city']) ? $_POST['city'] : ""),

            "elementary" => (isset($_POST['elementary']) ? $_POST['elementary'] : ""),
            "elementaryyeargraduated" => (isset($_POST['elementaryyeargraduated']) ? $_POST['elementaryyeargraduated'] : ""),
            "highschool" => (isset($_POST['highschool']) ? $_POST['highschool'] : ""),
            "highschooleargraduated" => (isset($_POST['highschooleargraduated']) ? $_POST['highschooleargraduated'] : ""),
            "college" => (isset($_POST['college']) ? $_POST['college'] : ""),
            "collegeyeargraduated" => (isset($_POST['collegeyeargraduated']) ? $_POST['collegeyeargraduated'] : ""),

            "company1" => (isset($_POST['company1']) ? $_POST['company1'] : ""),
            "position1" => (isset($_POST['position1']) ? $_POST['position1'] : ""),
            "date_from1" => (isset($_POST['date_from1']) ? $_POST['date_from1'] : ""),
            "date_to1" => (isset($_POST['date_to1']) ? $_POST['date_to1'] : ""),

            "company2" => (isset($_POST['company2']) ? $_POST['company2'] : ""),
            "position2" => (isset($_POST['position2']) ? $_POST['position2'] : ""),
            "date_from2" => (isset($_POST['date_from2']) ? $_POST['date_from2'] : ""),
            "date_to2" => (isset($_POST['date_to2']) ? $_POST['date_to2'] : ""),

            "createdby" => $this->current_userid
        );

        if ($id <> "") {
            //UPDATE
            $model = new EmployeeModel($this->connection);
            $result = $model->update($data,$id);
        } else {
            //INSERT
            $model = new EmployeeModel($this->connection);
            $result = $model->insert($data,"gpx_employee");
        }
        print_r($result);
    }

    public function newrole()
    {
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        $error = isset($_GET['error']) ? $_GET['error'] : null;
        $empmodel = new PartnerPortalModel($this->connection);
        $result = null;
        $employee = $empmodel->getPartnerEmployee();
        $role = $empmodel->getPartnerRoles();

        if (isset($id)) {
            $model = new UserModel($this->connection);
            $result = $model->getuserbyid($id);
        }

        echo $this->twig->render('partner_portal/newuser.html', array(
            "logindetails" => $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,
            "allemployee" => $employee,
            "allrole" => $role,
            "result" => $result,
            "error" => $error
        ));
    }

    public function saverole()
    {
        $id = isset($_POST['id']) ? $_POST['id'] : "";
        $result = 0;
        $data = array(
            "username" => (isset($_POST['username']) ? $_POST['username'] : ""),
            "password" => (isset($_POST['pass']) ? $_POST['pass'] : ""),
            "employee_id" => (isset($_POST['employee_id']) ? $_POST['employee_id'] : ""),
            "role_id" => (isset($_POST['role_id']) ? $_POST['role_id'] : ""),
        );
       
        if ($id <> "") {
            $model = new PartnerPortalModel($this->connection);
            $result = $model->updateRole($data, $id);
        } else {
            $model = new PartnerPortalModel($this->connection);
            if ($model->checkusername($data['username']) == 0) {
                $model = new GenericModel($this->connection);
                $result = $model->insert($data, USERS);
            }
        }

        if ($result == 1)
            header("Location: index.php?controller=partnerportal&action=userrole");
        else
            header("Location: index.php?controller=partnerportal&action=userrole&error=true");
    }

    //LIST
    public function listusr()
    {
        $model = new PartnerPortalModel($this->connection);
        $list = $model->getrolelist();
        $columns = array("employee_name", "username", "role");
        echo $this->twig->render('_generic_component/report/list_part.html', array(
            "logindetails" => $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,
            "list" => $list,
            "columns" => $columns,
            "url" => $_SERVER['REQUEST_URI'],
            "module" => "USERS AND ROLES",
        ));
    }

    // SAVE NEW TICKET
    public function savenewticket()
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
            header("Location: index.php?controller=partnerportal&action=list&module=tickets");
    }

    public function editticket()
    {
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        $result = null; 
        $empmodel = new PartnerPortalModel($this->connection);
        $employee = $empmodel->getPartnerEmployee(); 
        if(isset($id)){
            $model = new PartnerPortalModel($this->connection);
            $result = $model->getticketbyid($id);    
        }

        echo $this->twig->render('partner_portal/newticket.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,
            "allltickettype" => $this->alltickettype,
            "allcustomers" => $this->allcustomers,
            "allemployee" => $employee,
            "allpriority" => $this->allticketpriority,
            "allstatus" => $this->allticketstatus,
            "alltransactionsno" => $this->alltransactionsno,
            "result" => $result
        ));
    }

    public function view()
    {
        $image = "";
        $module = isset($_GET['module']) ? $_GET['module'] : "";
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        $model = new PartnerPortalModel($this->connection);
        $result = $model->getdetails($module,$id); 
        $box_numbers = $model->getboxnumber($module,$id);
        switch($module){
            case "intransit":
                echo $this->twig->render('partner_portal/view/viewintransit.html', array(
                    "logindetails" =>  $_SESSION['logindetails'],
                    "breadcrumb" => $this->breadcrumb,     
                    "result" => $result,   
                    "box_numbers" => $box_numbers,    
                ));
            break;
            case "unloads":
                $image = $model->getimages($module,$id); 
                echo $this->twig->render('partner_portal/view/viewunload.html', array(
                    "logindetails" =>  $_SESSION['logindetails'],
                    "breadcrumb" => $this->breadcrumb,     
                    "result" => $result,   
                    "box_numbers" => $box_numbers,     
                    "images" => $image,     
                ));
            break;
            case "deliver":
                $image = $model->getimages($module,$id); 
                echo $this->twig->render('partner_portal/view/viewdelivery.html', array(
                    "logindetails" =>  $_SESSION['logindetails'],
                    "breadcrumb" => $this->breadcrumb,     
                    "result" => $result,   
                    "box_numbers" => $box_numbers,     
                    "images" => $image,     
                ));
            break;
            case "dist":
                $image = $model->getimages($module,$id); 
                echo $this->twig->render('partner_portal/view/viewdist.html', array(
                    "logindetails" =>  $_SESSION['logindetails'],
                    "breadcrumb" => $this->breadcrumb,     
                    "result" => $result,   
                    "box_numbers" => $box_numbers,     
                    "images" => $image,     
                ));
            break;
            default:
                echo $this->twig->render('partner_portal/view/viewintransit.html', array(
                    "logindetails" =>  $_SESSION['logindetails'],
                    "breadcrumb" => $this->breadcrumb,     
                    "result" => $result,   
                    "box_numbers" => $box_numbers,    
                ));
            break;
            
        }


    }

    public function saveDist()
    {
        date_default_timezone_set("Asia/Singapore");
        $id = isset($_POST['id']) ? $_POST['id'] : "";
        $daterange = explode('-',$_POST['eta_etd']);
        $datefirst = date('Y-m-d',strtotime($daterange[0]));
        $datesec = date('Y-m-d',strtotime($daterange[1]));
        $data = array(
            "distribution_type" => (isset($_POST['distribution_type']) ? $_POST['distribution_type'] : ""),
            "destination_name" => (isset($_POST['destination_name']) ? $_POST['destination_name'] : ""),
            "driver_name" => (isset($_POST['driver_name']) ? $_POST['driver_name'] : ""),
            "truck_number" => (isset($_POST['truck_number']) ? $_POST['truck_number'] : ""),
            "mode_of_shipment" => (isset($_POST['mode_of_shipment']) ? $_POST['mode_of_shipment'] : ""),
            "etd" => $datesec." ".date("h:i:sa"),
            "eta" => $datefirst." ".date("h:i:sa"),
            "remarks" => (isset($_POST['remarks']) ? $_POST['remarks'] : ""),
        );

        if($id <> ""){
            $model = new PartnerPortalModel($this->connection);
            $result = $model->updateDist($id,$data);
        }
        if($result == 1)
            header("Location: index.php?controller=partnerportal&action=list&module=dist");
    }


}

