<?php

class TracknTraceController extends GenericController
{
    public function __construct()
    {
        parent::__construct();
        require_once __DIR__ . "/../model/TrackNTraceModel.php";
        require_once __DIR__ . "/../model/GenericModel.php";
    }

    //LIST
    public function search()
    {       
        $result = null;
        $model = new TrackNTraceModel($this->connection);
        $genericmodel = new GenericModel($this->connection);
        
        $boxnumber = isset($_GET['boxnumber']) ? $_GET['boxnumber'] : "";
        $transaction_no = $genericmodel->gettransactionno($boxnumber);
        
        if($transaction_no == ""){
            $transaction_no = isset($_POST['transaction_no']) ? $_POST['transaction_no'] : "";        
        }
        if($transaction_no == ""){
            $transaction_no = isset($_GET['transaction_no']) ? $_GET['transaction_no'] : "";
        }   
        //SEARCH
        if($transaction_no != ""){
            $result = $model->getdatabytransactionid($transaction_no);
        }
        echo $this->twig->render('cargo-management/trackntrace/search.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,
            "url" => $_SERVER['REQUEST_URI'],                 
            "result" => $result,
            "transaction_no" => $transaction_no
        ));    
    }


}

