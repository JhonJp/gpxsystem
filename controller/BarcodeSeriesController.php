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
        $columns = array("boxtype","series");
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

        echo $this->twig->render('cargo-management/box-barcode/edit.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,
            "allboxtype" => $this->allboxtype,
            "moduledescription" => "Add new barcode series",                 
        ));
    }

    public function save()
    {
        $model = new BarcodeSeriesModel($this->connection);
        $boxtype = isset($_POST['boxtype']) ? $_POST['boxtype'] : null;
        $qty = isset($_POST['qty']) ? $_POST['qty'] : null;
        $start = isset($_POST['seriesstart']) ? $_POST['seriesstart'] : null;
        $end = isset($_POST['seriesend']) ? $_POST['seriesend'] : null;
       //echo $start;
        $this->$model->saveSeries($boxtype,$start,$end,$qty);
        header('Location:index.php?controller=barcodeseries&action=list');
        
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

}

