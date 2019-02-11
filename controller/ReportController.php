<?php

class ReportController extends GenericController
{
    public function __construct()
    {
        parent::__construct();
        require_once __DIR__ . "/../model/ReportModel.php";
    }

    //LIST
    public function list()
    {       
        $list = null;
        $module = isset($_GET['module']) ? $_GET['module'] : "";
        $filter = isset($_GET['filter']) ? $_GET['filter'] : "";
        
        if($module == 'boxpurchase'){
            $this->genericfilter($module,$filter,"","");
        }
        else if($module == 'branchincentives'){
            $this->genericfilter($module,$filter,"","");
        } 
        else if($module == 'salesbybranch'){
            $this->filterbybranch($filter,"salesbybranch");
        }
        else if($module == 'salemp'){
            $this->filterbyemp($filter,"salemp");
        }
        else if($module == 'saledate'){
            $this->filterbydate($filter,"saledate");
        } 
        else if($module == 'boxdisposed'){
            $this->genericfilter($module, $filter,"","");
        }
        else if($module == 'reserve'){
            $this->genericfilter($module, $filter,"","");
        }
        else if($module == 'booked'){
            $this->genericfilter($module, $filter,"","");
        }
        else if($module == 'boxaging'){
            $this->genericfilter($module, $filter,"","");
        }
        else if($module == 'loads'){
            $this->genericfilter($module, $filter,"",""); 
        }
        else if($module == 'unl'){
             $this->genericfilter($module, $filter,"","");
        } 
        else if($module == 'barcodeseries'){
            $this->genericfilter($module, $filter,"","");
        }
        else if($module == 'packinglist'){
        $this->genericfilter($module, $filter,"","");
        }
        else if($module == 'loadunlexcept'){
            $this->genericfilter($module, $filter,"","");
        }   
    }

    public function  edit()
    {
    }

    public function save()
    {
       
    }


    public function filterbranch()
    {
        $branch = isset($_POST['branchname']) ? $_POST['branchname'] : "";
        //echo $_POST['branchname'];
        $index = new ReportModel($this->connection);

        if (isset($branch)) {
            $result = $index->salesreportbybranchname($branch);
        }
        
        if (isset($result)){ 
            header("Location:index.php?controller=report&action=list&module=salesbybranch&filter=bybranch&branchid=$branch");               
            //echo $branch;
        }
        else{                    
            header("Location:index.php?controller=report&action=list&module=salesbybranch&filter=default");
        }
        
    }

    public function filterbyemployee()
    {
        $employ = isset($_POST['employee']) ? $_POST['employee'] : "";
        //echo $_POST['branchname'];
        $index = new ReportModel($this->connection);

        if (isset($employ)) {
            $result = $index->reportbyemployee($employ);
        }
        
        if (isset($result)){ 
            header("Location:index.php?controller=report&action=list&module=salemp&filter=byemp&empid=$employ");               
        }
        else{                    
            header("Location:index.php?controller=report&action=list&module=salemp&filter=default");
        }
        
    }

    public function filterdatesales()
    {
        $daterange = explode('-',$_POST['daterange']);
        $mod = isset($_POST['module']) ? $_POST['module'] : "";
        $datefirst = date('Y-m-d',strtotime($daterange[0]));
        $datesec = date('Y-m-d',strtotime($daterange[1]));
        $index = new ReportModel($this->connection);
        
            if($mod == "salesreportbybranch"){
                if ((isset($datefirst)) && (isset($datesec))) {
                    $result = $index->salesbybranchdate($datefirst, $datesec);
                    if (isset($result)){ 
                        header("Location:index.php?controller=report&action=list&module=salesbybranch&filter=bydaterange&datefrom=$datefirst&dateto=$datesec");
                    }
                    else{                    
                        header("Location:index.php?controller=report&action=list&module=salesbybranch&filter=default");
                    }
                }
            }else if($mod == "salesreportbyemployee"){
                if ((isset($datefirst)) && (isset($datesec))) {
                    $result = $index->reportbydaterangeemployee($datefirst, $datesec);
                    if (isset($result)){ 
                        header("Location:index.php?controller=report&action=list&module=salemp&filter=bydaterange&datefrom=$datefirst&dateto=$datesec");
                    }
                    else{                    
                        header("Location:index.php?controller=report&action=list&module=salemp&filter=default");
                    }
                }
            }else if($mod == "salesreportbydate"){
                if ((isset($datefirst)) && (isset($datesec))) {
                    $result = $index->reportbydaterange($datefirst, $datesec);
                    if (isset($result)){ 
                        header("Location:index.php?controller=report&action=list&module=saledate&filter=bydaterange&datefrom=$datefirst&dateto=$datesec");
                    }
                    else{                    
                        header("Location:index.php?controller=report&action=list&module=saledate&filter=default");
                    }
                }
            }        
    }

    public function filterbybranch($filter, $mode){
        if(($filter == "default") && ($mode == "salesbybranch")){
            $model = new ReportModel($this->connection);
            $list = $model->salesreportbybranch();
            $branches = $model->getallbranch();
            $columns = array("date","branch","employee_name","total_amount");
            $filters = array("BY BRANCH","BY DATE RANGE");

            echo $this->twig->render('_generic_component/report/list_salesreport.html', array(
                "logindetails" =>  $_SESSION['logindetails'],
                "breadcrumb" => $this->breadcrumb,
                "list" => $list,        
                "columns" => $columns,  
                "module" => "SALES REPORT (BY BRANCH)",
                "filters" => $filters,
                "branches" => $branches               
            ));  
        }else if(($mode == "salesbybranch") && ($filter == "bybranch")){
            $branchname = isset($_GET['branchid']) ? $_GET['branchid'] : "";
            $model = new ReportModel($this->connection);
            $list = $model->salesreportbybranchname($branchname);
            $branches = $model->getallbranch();
            $columns = array("date","branch","employee_name","total_amount");
            $filters = array("BY BRANCH","BY DATE RANGE");

            echo $this->twig->render('_generic_component/report/list_salesreport.html', array(
                "logindetails" =>  $_SESSION['logindetails'],
                "breadcrumb" => $this->breadcrumb,
                "list" => $list,        
                "columns" => $columns,  
                "module" => "SALES REPORT (BY BRANCH)",
                "filters" => $filters,
                "branches" => $branches               
            ));
        }
        else if(($mode == "salesbybranch") && ($filter == "bydaterange")){
            $datefrom = isset($_GET['datefrom']) ? $_GET['datefrom'] : "";
            $dateto = isset($_GET['dateto']) ? $_GET['dateto'] : "";

            //echo $datefrom." ".$dateto;
            $model = new ReportModel($this->connection);
            $list = $model->salesbybranchdate($datefrom, $dateto);
            $branches = $model->getallbranch();
            $columns = array("date","branch","employee_name","total_amount");
            $filters = array("BY BRANCH","BY DATE RANGE");

            echo $this->twig->render('_generic_component/report/list_salesreport.html', array(
                "logindetails" =>  $_SESSION['logindetails'],
                "breadcrumb" => $this->breadcrumb,
                "list" => $list,        
                "columns" => $columns,  
                "module" => "SALES REPORT (BY BRANCH)",
                "filters" => $filters,
                "branches" => $branches               
            ));
        }
    }

    public function filterbydate($filter, $mode){

        if(($filter == "default") && ($mode == "saledate")){
            $model = new ReportModel($this->connection);
            $list = $model->salesreportbydate();
            $branches = $model->getallbranch();
            $columns = array("date","box_type","qty", "total_amount");
            $filters = array("BY DATE RANGE");

            echo $this->twig->render('_generic_component/report/list_salesreport.html', array(
                "logindetails" =>  $_SESSION['logindetails'],
                "breadcrumb" => $this->breadcrumb,
                "list" => $list,        
                "columns" => $columns,  
                "module" => "SALES REPORT (BY DATE)",
                "filters" => $filters,
                "branches" => $branches           
            )); 
        }else if (($filter == "bydaterange") && ($mode == "saledate")) {
            $datefrom = isset($_GET['datefrom']) ? $_GET['datefrom'] : "";
            $dateto = isset($_GET['dateto']) ? $_GET['dateto'] : "";
            
            $model = new ReportModel($this->connection);
            $list = $model->reportbydaterange($datefrom, $dateto);
            $branches = $model->getallbranch();
            $columns = array("date","box_type","qty", "total_amount");
            $filters = array("BY DATE RANGE");

            echo $this->twig->render('_generic_component/report/list_salesreport.html', array(
                "logindetails" =>  $_SESSION['logindetails'],
                "breadcrumb" => $this->breadcrumb,
                "list" => $list,        
                "columns" => $columns,  
                "module" => "SALES REPORT (BY DATE)",
                "filters" => $filters,
                "branches" => $branches           
            )); 
        } 
    }

    public function filterbyemp($filter, $mode){
        if(($filter == "default") && ($mode == "salemp")){
            $model = new ReportModel($this->connection);
            $list = $model->salesreportbyemployee();
            $emp = $model->getallsalesdriver();
            $columns = array("date","employee_name","box_type","qty", "total_amount");
            $filters = array("BY EMPLOYEE","BY DATE RANGE");
            echo $this->twig->render('_generic_component/report/list_salesreport.html', array(
                "logindetails" =>  $_SESSION['logindetails'],
                "breadcrumb" => $this->breadcrumb,
                "list" => $list,        
                "columns" => $columns,  
                "module" => "SALES REPORT (BY EMPLOYEE)",
                "filters" => $filters,
                "employees" => $emp,            
            ));
        }else if(($mode == "salemp") && ($filter == "byemp")){
            $employee = isset($_GET['empid']) ? $_GET['empid'] : "";
            $model = new ReportModel($this->connection);
            $list = $model->reportbyemployee($employee);
            $emp = $model->getallsalesdriver();
            $columns = array("date","employee_name","box_type","qty", "total_amount");
            $filters = array("BY EMPLOYEE","BY DATE RANGE");
            echo $this->twig->render('_generic_component/report/list_salesreport.html', array(
                "logindetails" =>  $_SESSION['logindetails'],
                "breadcrumb" => $this->breadcrumb,
                "list" => $list,        
                "columns" => $columns,  
                "module" => "SALES REPORT (BY EMPLOYEE)",
                "filters" => $filters,
                "employees" => $emp             
            ));
        }else if(($mode == "salemp") && ($filter == "bydaterange")){
            $datefrom = isset($_GET['datefrom']) ? $_GET['datefrom'] : "";
            $dateto = isset($_GET['dateto']) ? $_GET['dateto'] : "";
            $model = new ReportModel($this->connection);
            $list = $model->reportbydaterangeemployee($datefrom, $dateto);
            $emp = $model->getallsalesdriver();
            $columns = array("date","employee_name","box_type","qty", "total_amount");
            $filters = array("BY EMPLOYEE","BY DATE RANGE");
            echo $this->twig->render('_generic_component/report/list_salesreport.html', array(
                "logindetails" =>  $_SESSION['logindetails'],
                "breadcrumb" => $this->breadcrumb,
                "list" => $list,        
                "columns" => $columns,  
                "module" => "SALES REPORT (BY EMPLOYEE)",
                "filters" => $filters,
                "employees" => $emp             
            ));
        }
    }


    //GENERIC FILTER BY DATE REPORTS
    public function filterdate(){
        $mode = isset($_POST['datamode']) ? $_POST['datamode'] : "";
        $daterange = explode('-',$_POST['daterange']);
        $datefirst = date('Y-m-d',strtotime($daterange[0]));
        $datesec = date('Y-m-d',strtotime($daterange[1]));
        //echo $mode;
        if($mode == "reservationreport"){
            $this->genericfilter("reserve","bydaterange",$datefirst, $datesec);
        }else if($mode == "bookingreport"){
            $this->genericfilter("booked","bydaterange",$datefirst, $datesec);
        }else if($mode == "loadingreport"){
            $this->genericfilter("loads","bydaterange",$datefirst, $datesec);
        }else if($mode == "unloadingreport"){
            $this->genericfilter("unl","bydaterange",$datefirst, $datesec);
        }else if($mode == "boxpurchasereport"){
            $this->genericfilter("boxpurchase","bydaterange",$datefirst, $datesec);
        }else if($mode == "boxdisposedreport"){
            $this->genericfilter("boxdisposed","bydaterange",$datefirst, $datesec);
        }else if($mode == "packinglist"){
            $this->genericfilter("packinglist","bydaterange",$datefirst, $datesec);
        }
        else if($mode == "loadingunloadingexception"){
            $this->genericfilter("loadunlexcept","bydaterange",$datefirst, $datesec);
        }
        else if($mode == "boxaging"){
            $this->genericfilter("boxaging","bydaterange",$datefirst, $datesec);
        }
        else if($mode == "barcodeseries"){
            $this->genericfilter("barcodeseries","bydaterange",$datefirst, $datesec);
        }else{
            header("Location:index.php?controller=report&action=list&module=reserve&filter=default");
        }
    }

    //GENERIC FILTERS MODULE REPORTS
    public function genericfilter($module, $filterby, $datefrom, $dateto){
        switch($module){
            case "boxpurchase":
                if ($filterby == "bydaterange"){
                    $model = new ReportModel($this->connection);
                    $list = $model->boxpurchasereportbydate($datefrom, $dateto);

                    $columns = array("date","warehouse_name","manufacturer_name","box_type","qty","amount");

                    echo $this->twig->render('_generic_component/report/list_gen.html', array(
                        "logindetails" =>  $_SESSION['logindetails'],
                        "breadcrumb" => $this->breadcrumb,
                        "list" => $list,        
                        "columns" => $columns,  
                        "module" => "BOX PURCHASE REPORT"              
                    ));
                }else if ($filterby == "default"){
                    $model = new ReportModel($this->connection);
                    $list = $model->getboxpurchasereport();

                    $columns = array("date","warehouse_name","manufacturer_name","box_type","qty","amount");

                    echo $this->twig->render('_generic_component/report/list_gen.html', array(
                        "logindetails" =>  $_SESSION['logindetails'],
                        "breadcrumb" => $this->breadcrumb,
                        "list" => $list,        
                        "columns" => $columns,  
                        "module" => "BOX PURCHASE REPORT"              
                    ));
                }
                break;
            case "loadunlexcept":
                if ($filterby == "bydaterange"){
                    $model = new ReportModel($this->connection);
                    $list = $model->getexceptions();

                    $columns = array("container_no","loaded_qty","unloaded_qty","with_descripancy");

                    echo $this->twig->render('_generic_component/report/list_gen.html', array(
                        "logindetails" =>  $_SESSION['logindetails'],
                        "breadcrumb" => $this->breadcrumb,
                        "list" => $list,        
                        "columns" => $columns,  
                        "module" => "LOADING UNLOADING EXCEPTION"              
                    ));
                }else if ($filterby == "default"){
                    $model = new ReportModel($this->connection);
                    $list = $model->getexceptions();

                    $columns = array("container_no","loaded_qty","unloaded_qty","with_descripancy");

                    echo $this->twig->render('_generic_component/report/list_gen.html', array(
                        "logindetails" =>  $_SESSION['logindetails'],
                        "breadcrumb" => $this->breadcrumb,
                        "list" => $list,        
                        "columns" => $columns,  
                        "module" => "LOADING UNLOADING EXCEPTION"              
                    ));
                }
                break;
            case "boxaging":
                if ($filterby == "bydaterange"){
                    $model = new BoxAgingModel($this->connection);
                    $list = $model->getfilterbydate($datefrom, $dateto);

                    $columns = array("unloaded_date","box_number","receiver","destination","last_status","age");

                    echo $this->twig->render('_generic_component/report/list_gen.html', array(
                        "logindetails" =>  $_SESSION['logindetails'],
                        "breadcrumb" => $this->breadcrumb,
                        "list" => $list,        
                        "columns" => $columns,  
                        "module" => "BOX AGING"              
                    ));
                }else if ($filterby == "default"){
                    $model = new BoxAgingModel($this->connection);
                    $list = $model->getlist();

                    $columns = array("unloaded_date","box_number","receiver","destination","last_status","age");

                    echo $this->twig->render('_generic_component/report/list_gen.html', array(
                        "logindetails" =>  $_SESSION['logindetails'],
                        "breadcrumb" => $this->breadcrumb,
                        "list" => $list,        
                        "columns" => $columns,  
                        "module" => "BOX AGING"              
                    ));
                }
                break;
            case "unl":
                if ($filterby == "bydaterange"){
                    $model = new ReportModel($this->connection);
                    $list = $model->getunlbydate($datefrom, $dateto);
                    $columns = array("date","container_no","forwarder_name","arrival_time","qty","box_number");
                
                    echo $this->twig->render('_generic_component/report/list_gen.html', array(
                        "logindetails" =>  $_SESSION['logindetails'],
                        "breadcrumb" => $this->breadcrumb,
                        "list" => $list,        
                        "columns" => $columns,  
                        "module" => "UNLOADING REPORT"           
                    ));
                }else if ($filterby == "default"){
                    $model = new ReportModel($this->connection);
                    $list = $model->getunllist();
                    $columns = array("date","container_no","forwarder_name","arrival_time","qty","box_number","box_content");
                
                    echo $this->twig->render('_generic_component/report/list_gen.html', array(
                        "logindetails" =>  $_SESSION['logindetails'],
                        "breadcrumb" => $this->breadcrumb,
                        "list" => $list,        
                        "columns" => $columns,  
                        "module" => "UNLOADING REPORT"           
                    ));
                }
                break;
            case "loads":
                if ($filterby == "bydaterange"){
                    $model = new ReportModel($this->connection);
                    $list = $model->loadbydate($datefrom, $dateto);
                    $columns = array("date","shipping_name","container_no","etd","eta","qty","box_number");
                
                    echo $this->twig->render('_generic_component/report/list_gen.html', array(
                        "logindetails" =>  $_SESSION['logindetails'],
                        "breadcrumb" => $this->breadcrumb,
                        "list" => $list,        
                        "columns" => $columns,  
                        "module" => "LOADING REPORT"           
                    ));
                }else if ($filterby == "default"){
                    $model = new ReportModel($this->connection);
                    $list = $model->getloadinglist();
                    $columns = array("date","shipping_name","container_no","etd","eta","qty","box_number");
                
                    echo $this->twig->render('_generic_component/report/list_gen.html', array(
                        "logindetails" =>  $_SESSION['logindetails'],
                        "breadcrumb" => $this->breadcrumb,
                        "list" => $list,        
                        "columns" => $columns,  
                        "module" => "LOADING REPORT"           
                    ));
                }
                break;
            case "booked":
                if ($filterby == "bydaterange"){
                    $model = new ReportModel($this->connection);
                    $list = $model->getbookedbydate($datefrom, $dateto);        
                    $columns = array("date","transaction_no","customer","status","qty","amount_paid");
                    echo $this->twig->render('_generic_component/report/list_gen.html', array(
                        "logindetails" =>  $_SESSION['logindetails'],
                        "breadcrumb" => $this->breadcrumb,
                        "list" => $list,            
                        "columns" => $columns,   
                        "module" => "BOOKING REPORT",                                   
                    ));
                }else if ($filterby == "default"){
                    $model = new ReportModel($this->connection);
                    $list = $model->getlistbooking();        
                    $columns = array("date","transaction_no","customer","status","qty","amount_paid");
                    echo $this->twig->render('_generic_component/report/list_gen.html', array(
                        "logindetails" =>  $_SESSION['logindetails'],
                        "breadcrumb" => $this->breadcrumb,
                        "list" => $list,            
                        "columns" => $columns,   
                        "module" => "BOOKING REPORT",                                   
                    ));
                }
                break;
            case "reserve":
                if ($filterby == "bydaterange"){
                    $model = new ReportModel($this->connection);
                    $list = $model->getreservesbydate($datefrom, $dateto);        
                    $columns = array("date","reservation_no","customer","qty","deposit","assigned_to","status");
                    echo $this->twig->render('_generic_component/report/list_gen.html', array(
                        "logindetails" =>  $_SESSION['logindetails'],
                        "breadcrumb" => $this->breadcrumb,
                        "list" => $list,            
                        "columns" => $columns, 
                        "module" => "RESERVATION REPORT"                  
                    ));
                }else if ($filterby == "default"){
                    $model = new ReportModel($this->connection);
                    $list = $model->getreserves();        
                    $columns = array("date","reservation_no","customer","qty","deposit","assigned_to","status");
                    echo $this->twig->render('_generic_component/report/list_gen.html', array(
                        "logindetails" =>  $_SESSION['logindetails'],
                        "breadcrumb" => $this->breadcrumb,
                        "list" => $list,            
                        "columns" => $columns, 
                        "module" => "RESERVATION REPORT"                  
                    ));
                }
                break;
            case "boxdisposed":
                if ($filterby == "bydaterange"){
                    $model = new ReportModel($this->connection);
                    $list = $model->boxdisposedbydate($datefrom, $dateto);
                    $columns = array("date","released_by","destination","truck_number","boxtype","box_quantity");
                
                    echo $this->twig->render('_generic_component/report/list_gen.html', array(
                        "logindetails" =>  $_SESSION['logindetails'],
                        "breadcrumb" => $this->breadcrumb,
                        "list" => $list,        
                        "columns" => $columns,  
                        "module" => "BOX DISPOSED REPORT"           
                    ));
                }else if ($filterby == "default"){
                    $model = new ReportModel($this->connection);
                    $list = $model->boxdisposed();
                    $columns = array("date","released_by","destination","truck_number","boxtype","box_quantity");
                
                    echo $this->twig->render('_generic_component/report/list_gen.html', array(
                        "logindetails" =>  $_SESSION['logindetails'],
                        "breadcrumb" => $this->breadcrumb,
                        "list" => $list,        
                        "columns" => $columns,  
                        "module" => "BOX DISPOSED REPORT"           
                    )); 
                }
                break;
            case "branchincentives":
                if ($filterby == "default"){
                    $model = new ReportModel($this->connection);
                    $list = $model->getbranchincentivereport();
                    $columns = array("branch","employee_name","total_sales","five_percent","extra","sales","qty","box_quota","incentive","salary","deductions","total");
                
                    echo $this->twig->render('_generic_component/report/list_gen.html', array(
                        "logindetails" =>  $_SESSION['logindetails'],
                        "breadcrumb" => $this->breadcrumb,
                        "list" => $list,        
                        "columns" => $columns,  
                        "module" => "BRANCH INCENTIVES REPORT"              
                    )); 
                }else{
                    $model = new ReportModel($this->connection);
                    $list = $model->getbranchincentivereport();
                    $columns = array("branch","employee_name","total_sales","five_percent","extra","sales","qty","box_quota","incentive","salary","deductions","total");
                
                    echo $this->twig->render('_generic_component/report/list_gen.html', array(
                        "logindetails" =>  $_SESSION['logindetails'],
                        "breadcrumb" => $this->breadcrumb,
                        "list" => $list,        
                        "columns" => $columns,  
                        "module" => "BRANCH INCENTIVES REPORT"              
                    ));
                }
                break;
            case "barcodeseries":
                if ($filterby == "bydaterange"){
                    $model = new BarcodeSeriesModel($this->connection);
                    $list = $model->getlistbydate($datefrom, $dateto);        
                    $columns = array("date","branch","created_by","series","quantity");
                    echo $this->twig->render('_generic_component/report/list_gen.html', array(
                        "logindetails" =>  $_SESSION['logindetails'],
                        "breadcrumb" => $this->breadcrumb,
                        "list" => $list,            
                        "columns" => $columns,   
                        "module" => "BARCODE SERIES",                                   
                    ));
                }else if ($filterby == "default"){
                    $model = new BarcodeSeriesModel($this->connection);
                    $list = $model->getlist();        
                    $columns = array("date","branch","created_by","series","quantity");
                    echo $this->twig->render('_generic_component/report/list_gen.html', array(
                        "logindetails" =>  $_SESSION['logindetails'],
                        "breadcrumb" => $this->breadcrumb,
                        "list" => $list,            
                        "columns" => $columns,   
                        "module" => "BARCODE SERIES",                                   
                    ));
                }
                break;
            case "packinglist":
                if ($filterby == "bydaterange"){
                    $model = new ReportModel($this->connection);
                    $list = $model->getbookingwithconsigneebydate($datefrom,$dateto);        
                    $columns = array("book_date","name_of_sender","box_number","description");
                    echo $this->twig->render('_generic_component/report/list_gen.html', array(
                        "logindetails" =>  $_SESSION['logindetails'],
                        "breadcrumb" => $this->breadcrumb,
                        "list" => $list,            
                        "columns" => $columns,   
                        "module" => "PACKING LIST",                                   
                    ));
                }else if ($filterby == "default"){
                    $model = new ReportModel($this->connection);
                    $list = $model->getbookingwithconsignee();        
                    $columns = array("book_date","name_of_sender","box_number","description");
                    echo $this->twig->render('_generic_component/report/list_gen.html', array(
                        "logindetails" =>  $_SESSION['logindetails'],
                        "breadcrumb" => $this->breadcrumb,
                        "list" => $list,            
                        "columns" => $columns,   
                        "module" => "PACKING LIST",                                   
                    ));
                }
                break;
        }
    }

}

