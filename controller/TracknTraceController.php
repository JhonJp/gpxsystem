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
        $mess = null;
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
            $mess = $model->getMessagesByTransaction($transaction_no);
        }
        echo $this->twig->render('cargo-management/trackntrace/search.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,
            "url" => $_SERVER['REQUEST_URI'],                 
            "result" => $result,
            "messages" => $mess,
            "transaction_no" => $transaction_no
        ));    
    }

    public function insertNotify(){
        $genericmodel = new GenericModel($this->connection);
        $msg = isset($_POST['message']) ? $_POST['message'] : "";
        $trans = isset($_POST['trans']) ? $_POST['trans'] : "";
       
        $by = $genericmodel->getuserlogin();
        $createddate = date('Y/m/d H:i:s');

        $index = new TrackNTraceModel($this->connection);

        if (isset($msg)) {
            $result = $index->insertMessage($trans,$msg,$by,$createddate);
        }
        
        if (isset($result)){ 
            header("Location:index.php?controller=trackntrace&action=search&transaction_no=".$trans);               
            //echo $branch;
        }
        else{                    
            header("Location:index.php?controller=index&action=dashboard");
        }
    }


}

