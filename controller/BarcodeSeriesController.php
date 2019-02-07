<?php

class BarcodeSeriesController extends GenericController
{
    public function __construct()
    {
        parent::__construct();
        require_once __DIR__ . "/../model/BarcodeSeriesModel.php";
    }

    //LIST
    public function list()
    {       
        $model = new BarcodeSeriesModel($this->connection);
        $list = $model->getlist();        
        $columns = array("date","branch","created_by","series","quantity");
        echo $this->twig->render('_generic_component/barcode_series/list.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,
            "list" => $list,            
            "columns" => $columns,   
            "moduledescription" => "List of barcode series",                                   
        )); 
    }

    //EDIT
    public function edit()
    {
        $id = isset($_GET['id']) ? $_GET['id'] : null;        
        $result = null;
        $model = new BarcodeSeriesModel($this->connection);
        $max = $model->getMax();
        $end = null;
        if($max == 0){
            $end = 0;
        }else{
            $end = $model->getEnd($max);
        }
        echo $this->twig->render('cargo-management/box-barcode/edit.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,
            "allboxtype" => $this->allboxtype,
            "seriesend" => $model->getEnd($max),
            "moduledescription" => "Add new barcode series",                 
        ));
    }

    public function save()
    {       
        $data = json_decode(utf8_encode($_POST['data']));     

        $model = new BarcodeSeriesModel($this->connection);
        $result = $model->saveSeries($data);
        //print_r($result);
    }

    public function view()
    {        
        $id = isset($_GET['d']) ? $_GET['d'] : null;
        $model = new BarcodeSeriesModel($this->connection);
        $columns = array("start","end","barcode");
        $data = $model->getData($id); 

        echo $this->twig->render('_generic_component/barcode_series/list.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,     
            "columns" => $columns,
            "list" => $data,     
            "moduledescription" => "Barcode Series",     
        ));
    }

    //view all barcode distributed
    public function viewbarcodedistributedbyid()
    {        
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        $model = new BarcodeSeriesModel($this->connection);
        $columns = array("boxnumber","status");
        $data = $model->getBarcodes($id); 

        echo $this->twig->render('_generic_component/barcode_series/list.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,     
            "columns" => $columns,
            "list" => $data,          
            "moduledescription" => "Barcodes Distributed to Driver",     
        ));
    }

    public function viewbarcodedist()
    {        
        $model = new BarcodeSeriesModel($this->connection);
        $columns = array("date","driver","created_by","branch");
        $data = $model->getBarcodesDist(); 

        echo $this->twig->render('_generic_component/barcode_series/list.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,     
            "columns" => $columns,
            "list" => $data,     
            "moduledescription" => "List of barcode distributed to driver",     
        ));
    }


}

