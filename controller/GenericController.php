<?php

//MODEL
require_once __DIR__ . "/../model/GenericModel.php";
require_once __DIR__ . "/../model/ReservationModel.php";
require_once __DIR__ . "/../model/CustomerModel.php";
require_once __DIR__ . "/../model/LoadingModel.php";
require_once __DIR__ . "/../model/UnloadingModel.php";
require_once __DIR__ . "/../model/WarehouseAcceptanceModel.php";
require_once __DIR__ . "/../model/DistributionModel.php";
require_once __DIR__ . "/../model/DeliveryModel.php";
require_once __DIR__ . "/../model/WarehouseInventoryModel.php";
require_once __DIR__ . "/../model/EmployeeModel.php";
require_once __DIR__ . "/../model/ChartaccountsModel.php";
require_once __DIR__ . "/../model/RoleModel.php";

class GenericController{
    
    protected $connector;
    protected $connection;
    protected $twig;
    protected $breadcrumb;
    
    //FOR DROPDOWN LIST
    protected $allboxtype;
    protected $allshipper;
    protected $allconsignee;
    protected $allcustomers;
    protected $allsalesdriver;
    protected $allbranch;
    protected $allchartaccounttype;
    protected $allemployee;
    protected $allchartaccounts;
    protected $allrole;
    protected $allreservationstatus;
    protected $current_userid;
    protected $alltransactionsno;
    protected $alltickettype;
    protected $allticketstatus;
    protected $allticketpriority;
    protected $allbookingtype;
    protected $allreservationno;
    protected $allprovince;
    protected $allsource;
    protected $alldestination;
    protected $allcurrency;


    public function __construct()
    {        
        //CURRENT USER
        if(isset($_SESSION["logindetails"])){
            $result = $_SESSION["logindetails"];        
            $this->current_userid = $result[0]['id'];
        }else{ 
            $this->current_userid = 1; //ADMIN 
        }
        //BREADCRUMB
        $this->breadcrumb = array(
            "controller" => (isset($_GET['controller']) ? $_GET['controller'] : ""),
            "action" => (isset($_GET['action']) ? $_GET['action'] : "")
        );

        //CONNECTOR
        require_once __DIR__ . "/../core/connector.php";
        $this->connector = new Connector();
        $this->connection = $this->connector->connection();

        $loader = new Twig_Loader_Filesystem('view');
        $this->twig = new Twig_Environment($loader, array('debug' => true));
        $this->twig->addExtension(new Twig_Extension_Debug());
        $this->alldropdown();    
    }

    public function run($action)
    {
        switch ($action) {
            case "list":
                $this->list();
                break;
            case "viewrec":
                $this->viewrec();
                break;
            case "filterbranch":
                $this->filterbranch();
                break;
            case "filterdate":
                $this->filterdate();
                break;
            case "filterdatesales":
                $this->filterdatesales();
                break;
            case "filterbyemployee":
                $this->filterbyemployee();
                break;
            case "edit":
                $this->edit();
                break;
            case "save":
                $this->save();
                break;
            case "update":
                $this->update();
                break;
            case "login":
                $this->login();
                break;
            case "logout":
                $this->logout();
                break;
            case "dashboard":
                $this->dashboard();
                break; 
            case "history":
                $this->history();
                break;  
            case "payment":
                $this->payment();
                break; 
            case "updatepayment":
                $this->updatepayment();
                break;     
            case "delete":
                $this->delete();
                break; 
            case "search":
                $this->search();
                break;  
            case "view":
                $this->view();
                break;
            ///////////////PARTNER PORTAL /////////////
            case "partnerdashboard":
                $this->partnerdashboard();
                break; 
            case "isn":
                $this->insertNotify();
                break;                  
            default:
                $this->list();
                break;
        }
    }

    public function alldropdown(){
        
        $model = new GenericModel($this->connection);        
        $this->allboxtype = $model->getallboxtype();

        $model = new GenericModel($this->connection);
        $model->customertype = "customer";
        $this->allshipper = $model->getallcustomerbytype();

        $model = new GenericModel($this->connection);
        $model->customertype = "receiver";
        $this->allconsignee = $model->getallcustomerbytype();
        
        $model = new GenericModel($this->connection);
        $this->allsalesdriver = $model->getallsalesdriver();

        $model = new GenericModel($this->connection);
        $this->allbranch = $model->getallbranch();

        $model = new GenericModel($this->connection);
        $this->allchartaccounttype = $model->getallchartaccounttype();

        $model = new EmployeeModel($this->connection);
        $this->allemployee = $model->getlist();

        $model = new ChartaccountsModel($this->connection);
        $this->allchartaccounts = $model->getlist();
        
        $model = new RoleModel($this->connection);
        $this->allrole = $model->getlist();

        $model = new GenericModel($this->connection);
        $this->allcustomers = $model->getallcustomers();

        $model = new GenericModel($this->connection);
        $this->allreservationstatus = $model->getallreservationstatus();

        $model = new GenericModel($this->connection);
        $this->alltransactionsno = $model->getalltransactionno();
        
        $model = new GenericModel($this->connection);
        $this->alltickettype = $model->getalltickettype();

        $model = new GenericModel($this->connection);
        $this->allticketstatus = $model->getallticketstatus();

        $model = new GenericModel($this->connection);
        $this->allticketpriority = $model->getallticketpriority();

        $model = new GenericModel($this->connection);
        $this->allbookingtype = $model->gettallbookingtype();

        $model = new GenericModel($this->connection);
        $this->allreservationno = $model->getallreservationno();

        $model = new GenericModel($this->connection);
        $this->allprovince = $model->getallprovince();

        $model = new GenericModel($this->connection);
        $this->allsource = $model->getallsource();

        $model = new GenericModel($this->connection);
        $this->alldestination = $model->getalldestination();
        
        $model = new GenericModel($this->connection);
        $this->allcurrency = $model->getallcurrency();
        
    }

    //LIST
    public function list()
    {   
        $moduledescription = "";
        switch($_GET['controller']){
            case 'loading':         
                $model = new LoadingModel($this->connection);
                $list = $model->getlist();  
                $columns = array("loaded_date","shipping_line","container_no","etd","eta","qty","box_number","loaders_name","branch");
                $moduledescription = "List of all loaded in container";
                break;   
            case 'unloading':
                $model = new UnloadingModel($this->connection);
                $list = $model->getlist();         
                $columns = array("unload_date","container_no","forwarder_name","time_start","time_end","arrival_time","qty","box_number");
                $moduledescription = "List of all unloaded";
				break;
            case 'warehouse_acceptance':
                $model = new WarehouseAcceptanceModel($this->connection);
                $list = $model->getlist();         
                $columns = array("accepted_date","warehouse_name","deliver_by","truck_no","accepted_by","qty","box_number");
                $moduledescription = "List of all accepted box numbers per warehouse";
				break;
            case 'delivery':
                $model = new DeliveryModel($this->connection);
                $list = $model->getlist();         
                $columns = array("delivered_date","destination","delivered_by","customer","receiver","box_number","received_by","status");
                $moduledescription = "List of all delivered box numbers";
				break;
            case 'warehouse_inventory':
                $model = new WarehouseInventoryModel($this->connection);
                $list = $model->getlist();         
                $columns = array("warehouse_name","manufacturer_name","box_type_and_quantity");   
                $moduledescription = "List of quantity per box type";             
                break;
        }
        echo $this->twig->render('_generic_component/list.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,
            "list" => $list,            
            "columns" => $columns,           
            "url" => $_SERVER['REQUEST_URI'],
            "moduledescription" => $moduledescription                                            
        ));        
    }

}

?>