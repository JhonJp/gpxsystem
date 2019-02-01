<?php
require_once __DIR__ . "/GenericModel.php";

class TrackNTraceModel extends GenericModel
{

    public function __construct($connection)
    {
        parent::__construct($connection);
    }

    //LIST    
    public function getdatabytransactionid($transaction_no)
    {        
        $query = $this->connection->prepare("
        SELECT 
        gtnt.transaction_no as transaction_no,
        gtnt.dateandtime as dateandtime,
        gtnt.status as status,
        gtnt.details as details,
        CONCAT(gc.firstname,' ', gc.lastname) as sender,
        gtnt.qty as qty,
        gtnt.activity as activity,
        gtnt.location as location
        FROM gpx_trackntrace gtnt
        LEFT JOIN gpx_booking gb ON gtnt.transaction_no = gb.transaction_no
        LEFT JOIN gpx_customer gc ON gc.account_no = gb.customer
        WHERE gtnt.transaction_no = :transaction_no
        ORDER BY gtnt.id DESC
        ");
        $query->execute(array("transaction_no"=>$transaction_no));    
        $result = $query->fetchAll(); 
        return $result;     
    }

    public function getReceiver($boxnumber)
    {        
        $query = $this->connection->prepare("
        SELECT CONCAT(gc.firstname,' ', gc.lastname) as receiver, gbcb.consignee as cons 
        FROM gpx_booking_consignee_box gbcb
        LEFT JOIN gpx_customer gc ON gbcb.consignee = gc.account_no
        WHERE gbcb.box_number = :boxnumber        
        ");
        $query->execute(array("boxnumber"=>$boxnumber));    
        $result = $query->fetchAll(); 
        return $result;     
    }

    public function checkHardPort($boxnumber)
    {        
        $query = $this->connection->prepare("
        SELECT gbcb.hardport 
        FROM gpx_booking_consignee_box gbcb
        WHERE gbcb.box_number = :boxnumber        
        ");
        $query->execute(array("boxnumber"=>$boxnumber));    
        $result = $query->fetchColumn(); 
        return $result;     
    }

    public function getSender($transaction_no)
    {        
        $query = $this->connection->prepare("
        SELECT CONCAT(gc.firstname,' ', gc.lastname) as sender, gc.account_no as accnt 
        FROM gpx_booking gb
        LEFT JOIN gpx_customer gc ON gb.customer = gc.account_no
        WHERE gb.transaction_no = :transaction_no        
        ");
        $query->execute(array("transaction_no"=>$transaction_no));    
        $result = $query->fetchAll(); 
        return $result;     
    }

    public function insertMessage($trans_no,$msg,$by,$d)
    {
        $query = $this->connection->prepare("INSERT INTO gpx_tntmessages(transaction_no,message,createdby,createddate) 
        VALUES (:trans,:message,:by,:date)");
        $result = $query->execute(array(
            "trans" => $trans_no,
            "message" => $msg,
            "by" => $by,
            "date" => $d,
        ));
        return $result;
    }

    //GET MESSAGES
    public function getMessagesByTransaction($transaction_no){
        $query = $this->connection->prepare("SELECT gm.*,
        CONCAT(gemp.firstname,' ', gemp.lastname) as name
        FROM gpx_tntmessages gm
        JOIN gpx_employee gemp ON gemp.id = gm.createdby
        WHERE gm.transaction_no = :trans
        ORDER BY gm.id DESC
        ");
        $query->execute(array("trans"=>$transaction_no));    
        $result = $query->fetchAll();
        return $result;
    }


}
?>