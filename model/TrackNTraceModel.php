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
        $query = $this->connection->prepare("SELECT * FROM gpx_trackntrace 
        WHERE transaction_no = :transaction_no
        ORDER BY id DESC
        ");
        $query->execute(array("transaction_no"=>$transaction_no));    
        $result = $query->fetchAll();
        $this->connection = null; 
        return $result;     
    }

}
?>