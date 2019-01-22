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
        GROUP BY gtnt.transaction_no, gtnt.status
        ORDER BY gtnt.id DESC
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