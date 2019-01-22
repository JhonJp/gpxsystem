<?php
require_once __DIR__ . "/GenericModel.php";

class PartnerPortalModel extends GenericModel
{

  public function __construct($connection)
  {
    parent::__construct($connection);
  }

  //COUNT deliveries
  public function countdeliveries()
  {
    $query = $this->connection->prepare("SELECT COUNT(id) FROM gpx_delivery");
    $query->execute();
    $result = $query->fetchColumn();
    return $result;
  }

  //COUNT UNLOADED
  public function countUnloaded()
  {
    $query = $this->connection->prepare("SELECT COUNT(id) FROM gpx_unloading");
    $query->execute();
    $result = $query->fetchColumn();
    return $result;
  }

  //COUNT CUSTOMERS
  public function countCustomers($type)
  {
    $query = $this->connection->prepare("SELECT COUNT(id) FROM gpx_customer WHERE type = :ty");
    $query->execute(array("ty"=>$type));
    $result = $query->fetchColumn();
    return $result;
  }

  //INTRANSIT LIST
  public function getintransit()
  {        
      $query = $this->connection->prepare("
      SELECT gpi.createddate as date,
      gpi.truck_no as truck_number,
      GROUP_CONCAT(gpi.box_number,',') as box_number,
      gpi.destination_name as destination_name
      FROM gpx_partnerportal_intransit gpi
      GROUP BY gpi.truck_no
      ORDER BY gpi.id
      ");
      $query->execute();
      $result = $query->fetchAll();
      return $result;
  }

  //GET UNLOADING DATA
  public function getUnloads()
    {
        $query = $this->connection->prepare("SELECT gu.* ,
        COUNT(gubn.box_number) as qty,
        (SELECT GROUP_CONCAT(a.box_number) FROM gpx_unloading_box_number a WHERE a.unloading_id = gu.id) as box_number
        FROM gpx_unloading gu 
        LEFT JOIN gpx_unloading_box_number gubn ON gu.id = gubn.unloading_id
        GROUP BY gu.id
        ");
        $query->execute();
        $result = $query->fetchAll();
        $this->connection = null;
        return $result;
    }

    //GET DELIVERY DATA
    public function getDeliveries()
    {        
        $query = $this->connection->prepare("SELECT         
        
        CONCAT(gc1.firstname, ' ',gc1.lastname) as customer,        
        GROUP_CONCAT(gdbn.box_number) as box_number,
        CONCAT(gc2.firstname, ' ',gc2.lastname)  as receiver,
        gsd1.name as origin ,
        gsd2.name as destination,
        gds.name as status,

        gdbn.createddate as delivered_date
        FROM gpx_delivery_box_number gdbn
        JOIN gpx_delivery  gd ON gdbn.delivery_id = gd.id  
        JOIN gpx_delivery_status gds ON gds.id = gdbn.status
        LEFT JOIN gpx_source_destination gsd1 ON gdbn.origin = gsd1.id
        LEFT JOIN gpx_source_destination gsd2 ON gdbn.destination = gsd2.id
        LEFT JOIN gpx_customer gc1 ON gc1.account_no = gd.customer
        LEFT JOIN gpx_customer gc2 ON gc2.account_no = gdbn.receiver

        GROUP BY
        gdbn.receiver,
        gdbn.origin ,
        gdbn.destination,
        gds.name,
        gdbn.createddate
        ");
        $query->execute();    
        $result = $query->fetchAll();
        $this->connection = null; 
        return $result;
    }

    //LIST    
    public function getTickets()
    {  
        $query = $this->connection->prepare("SELECT gt.* , CONCAT(ge.firstname,' ',ge.lastname) as assigned_to
        , gty.name as ticket_type , gts.name as status , gtp.name as priority
        FROM gpx_tickets gt 
        JOIN gpx_tickets_type gty ON gty.id = gt.ticket_type
        JOIN gpx_tickets_status gts ON gts.id = gt.status
        JOIN gpx_tickets_priority gtp ON gtp.id = gt.priority
        JOIN gpx_employee ge ON ge.id = gt.assigned_to
        ");
        $query->execute();    
        $result = $query->fetchAll();
        $this->connection = null; 
        return $result;     
    }

  
    
}
?>