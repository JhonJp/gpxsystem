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
        $query = $this->connection->prepare("SELECT 
        gemp.id, gbranch.id, gemp.branch,gpayment.paymentterm,
        gbranch.name as branch,
        CONCAT(gemp.firstname, ' ',gemp.lastname)  as employee_name, 
        COUNT(gbookbox.transaction_no) as total_sales,
        SUM(gpayment.total_amount) as sales,
        COUNT(gbookbox.transaction_no) as qty,
        COUNT(gbookbox.transaction_no) as extra,
        SUM(gpayment.total_amount) as incentive,
        COUNT(gbookbox.transaction_no) as total
        FROM gpx_employee gemp
        JOIN gpx_branch gbranch ON gbranch.id = gemp.branch
        JOIN gpx_booking gwbook ON gwbook.createdby = gemp.id
        JOIN gpx_booking_consignee_box gbookbox ON gbookbox.transaction_no = gwbook.transaction_no
        LEFT JOIN gpx_payment gpayment ON gpayment.transaction_no = gwbook.transaction_no
        WHERE gpayment.paymentterm = 'Full'
        GROUP BY 
        gemp.id,
        gemp.branch
        ");
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }

    
}
?>