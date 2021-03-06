<?php
require_once  __DIR__ . "/GenericModel.php";

class BoxAgingModel extends GenericModel
{

    public function __construct($connection)
    {
		parent::__construct($connection);
    }

    public function getlist()
    {        
        $query = $this->connection->prepare("SELECT gu.*,gu.unload_date as unloaded_date,
        gubox.box_number,
        CONCAT(gc.firstname,' ', gc.lastname) as receiver,
        gsd.name as destination,
        pr.provDesc as province,
        ct.citymunDesc as city,
        (SELECT gtnt.status FROM gpx_trackntrace gtnt
        WHERE gtnt.transaction_no = gubox.box_number
        ORDER BY gtnt.id DESC LIMIT 1) as last_status
        FROM gpx_unloading gu
        LEFT JOIN gpx_unloading_box_number gubox ON gubox.unloading_id = gu.id
        LEFT JOIN gpx_booking_consignee_box gbc ON gbc.box_number = gubox.box_number
        LEFT JOIN gpx_trackntrace gtnt ON gubox.box_number = gtnt.transaction_no
        LEFT JOIN gpx_customer gc ON gc.account_no = gbc.consignee
        LEFT JOIN refprovince pr ON gc.province = pr.provCode
        LEFT JOIN refcitymun ct ON gc.city = ct.citymunCode
        LEFT JOIN gpx_source_destination gsd ON gsd.id = gbc.destination_id
        WHERE gtnt.status = 'Unloaded'
        GROUP BY gubox.box_number
        ORDER BY gu.id");
        $query->execute();
        $result = $query->fetchAll();
        $this->connection = null; 
        return $result;
    }

    public function getfilterbydate($datefrom, $dateto)
    {        
        $query = $this->connection->prepare("SELECT gu.*,gu.unload_date as unloaded_date,
        gubox.box_number,
        CONCAT(gc.firstname,' ', gc.lastname) as receiver,
        gsd.name as destination,
        pr.provDesc as province,
        ct.citymunDesc as city,
        (SELECT gtnt.status FROM gpx_trackntrace gtnt
        WHERE gtnt.transaction_no = gubox.box_number AND gtnt.status != 'For Delivery'
        ORDER BY gtnt.id DESC LIMIT 1) as last_status
        FROM gpx_unloading gu
        JOIN gpx_unloading_box_number gubox ON gubox.unloading_id = gu.id
        JOIN gpx_booking_consignee_box gbc ON gbc.box_number = gubox.box_number
        JOIN gpx_source_destination gsd ON gsd.id = gbc.destination_id
        JOIN gpx_customer gc ON gc.account_no = gbc.consignee
        JOIN refprovince pr ON gc.province = pr.provCode
        JOIN refcitymun ct ON gc.city = ct.citymunCode
        JOIN gpx_trackntrace gtnt ON gubox.box_number = gtnt.transaction_no
        WHERE gtnt.status = 'Unloaded' AND gu.unload_date BETWEEN :datefrom AND :dateto
        OR gu.unload_date LIKE :like
        ORDER BY gu.id");
        $query->execute(array(
          "datefrom"=>$datefrom,
          "dateto"=>$dateto,
          "like"=>"%$datefrom%",
      ));
        $result = $query->fetchAll();
        $this->connection = null; 
        return $result;
    }

}
?>