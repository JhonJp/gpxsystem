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
        $receiver = null;
        $sender = null;
        $hardport = null;
        $model = new TrackNTraceModel($this->connection);
        $genericmodel = new GenericModel($this->connection);
        
        $boxnumber = isset($_GET['boxnumber']) ? $_GET['boxnumber'] : "";
        //$transaction_no = $genericmodel->gettransactionno($boxnumber);
        
        if($boxnumber == ""){
            $boxnumber = isset($_POST['boxnumber']) ? $_POST['boxnumber'] : "";        
        }
        if($boxnumber == ""){
            $boxnumber = isset($_GET['boxnumber']) ? $_GET['boxnumber'] : "";
        }   
        //SEARCH
        if($boxnumber != ""){
            $result = $model->getdatabytransactionid($boxnumber);
            $mess = $model->getMessagesByTransaction($boxnumber);
            $receiver = $model->getReceiver($boxnumber);
            $hardport = $model->checkHardPort($boxnumber);
            $sender = $model->getSender($this->getTransByBoxnumber($boxnumber));
        }
        echo $this->twig->render('cargo-management/trackntrace/search.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,
            "url" => $_SERVER['REQUEST_URI'],                 
            "result" => $result,
            "messages" => $mess,
            "receiver" => $receiver,
            "sender" => $sender,
            "hardport" => $hardport,
            "transaction_no" => $boxnumber
        ));    
    }

    public function insertNotify(){
        $genericmodel = new GenericModel($this->connection);
        $msg = isset($_POST['message']) ? $_POST['message'] : "";
        $trans = isset($_POST['trans']) ? $_POST['trans'] : "";
       
        $by = $genericmodel->getuserlogin();
        $createddate = date('Y/m/d H:i:s');

        $index = new TrackNTraceModel($this->connection);
        
        if($trans != ""){
            if (isset($msg)) {
                $result = $index->insertMessage($trans,$msg,$by,$createddate);
            }
            
            if (isset($result)){ 
                header("Location:index.php?controller=trackntrace&action=search&boxnumber=".$trans);               
            }
            else{                    
                header("Location:index.php?controller=trackntrace&action=search");
            }
        }else{
            header("Location:index.php?controller=index&action=dashboard");
        }
    }

    public function getTransByBoxnumber($boxnumber){
        $query = $this->connection->prepare("
        SELECT transaction_no 
        FROM gpx_booking_consignee_box gbcb
        WHERE gbcb.box_number = :boxnumber        
        ");
        $query->execute(array("boxnumber"=>$boxnumber));    
        $result = $query->fetchColumn(); 
        return $result; 
    }

}

