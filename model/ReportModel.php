<?php
require_once __DIR__ . "/GenericModel.php";

class ReportModel extends GenericModel
{

    public function __construct($connection)
    {
        parent::__construct($connection);
    }
    
    //BOX PURCHASE REPORT
    public function getboxpurchasereport()
    {        
        $query = $this->connection->prepare("SELECT gwi.createddate as date
        , gwi.id, 
        gw.name as warehouse_name ,
        gwi.manufacturer_name ,
        gb.name as box_type,
        gwi.quantity as qty
        FROM gpx_warehouse_inventory_report gwi
        JOIN gpx_warehouse gw ON gw.id = gwi.warehouse_id
        JOIN gpx_boxtype gb ON gb.id = gwi.boxtype_id
        GROUP BY 
        gw.name,
        gwi.manufacturer_name
        ");
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }

    //BRANCH INCENTIVE REPORT
    public function getbranchincentivereport()
    {        
        $query = $this->connection->prepare("SELECT ");
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }

}
?>