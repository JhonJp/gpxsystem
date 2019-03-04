<?php
require_once __DIR__ . "/GenericModel.php";

class PartnerPortalModel extends GenericModel
{

  public function __construct($connection)
  {
    parent::__construct($connection);
  }

  //LIST    
  public function getrolelist()
  {        
      $query = $this->connection->prepare("SELECT gu.* , gr.name as role ,
      CONCAT(ge.firstname,' ',ge.lastname) as 'employee_name' 
      FROM gpx_users gu 
      JOIN gpx_employee ge ON gu.employee_id = ge.id
      JOIN gpx_role gr ON gr.id = gu.role_id
      WHERE gr.name LIKE '%Partner%'
      ORDER BY ge.firstname ASC
      ");
      $query->execute();    
      $result = $query->fetchAll();
      $this->connection = null; 
      return $result;
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
      SELECT gl.*, gl.loaded_date as loaded_date,
      gl.container_no as container_no,
      gl.eta as eta
      FROM gpx_loading gl
      ORDER BY gl.eta ASC
      ");
      $query->execute();
      $result = $query->fetchAll();
      return $result;
  }

  public function getIntransitlist()
    {
        $query = $this->connection->prepare("SELECT gl.* ,
        COUNT(glbn.box_number) as qty,
        (SELECT GROUP_CONCAT(a.box_number) FROM gpx_loading_box_number a WHERE a.loading_id = gl.id) as box_number
        FROM gpx_loading gl
        LEFT JOIN gpx_loading_box_number glbn ON gl.id = glbn.loading_id
        GROUP BY gl.id");
        $query->execute();
        $result = $query->fetchAll();
        $this->connection = null;
        return $result;
    }

    public function getIntransitCount()
    {
        $query = $this->connection->prepare("SELECT COUNT(id) FROM gpx_loading");
        $query->execute();
        $result = $query->fetchColumn();
        return $result;
    }

    //GET UNLOADING DATA ADMIN
  public function getUnloadsAdmin()
  {
      $query = $this->connection->prepare("SELECT gu.*,gu.container_no as container_number ,
      COUNT(gubn.box_number) as qty,
      CONCAT(gu.driver_name,' / ',gu.plate_no) as driver_and_plate_no,
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

  //GET UNLOADING DATA
  public function getUnloads($id)
    {
        $query = $this->connection->prepare("SELECT gu.*,gu.container_no as container_number ,
        COUNT(gubn.box_number) as qty,
        CONCAT(gu.driver_name,' / ',gu.plate_no) as driver_and_plate_no,
        (SELECT GROUP_CONCAT(a.box_number) FROM gpx_unloading_box_number a WHERE a.unloading_id = gu.id) as box_number
        FROM gpx_unloading gu 
        LEFT JOIN gpx_unloading_box_number gubn ON gu.id = gubn.unloading_id
        WHERE gu.createdby = :id
        GROUP BY gu.id
        ");
        $query->execute(array("id"=>$id));
        $result = $query->fetchAll();
        $this->connection = null;
        return $result;
    }

    //GET DELIVERY DATA
    public function getDeliveries()
    {        
        $query = $this->connection->prepare("SELECT 
        gd.id as id,
        CONCAT(gc1.firstname, ' ',gc1.lastname) as customer,        
        GROUP_CONCAT(gdbn.box_number) as box_number,
        CONCAT(gc2.firstname, ' ',gc2.lastname)  as receiver,
        gsd2.name as destination,
        CONCAT(gemp.firstname, ' ',gemp.lastname)  as driver_name,
        gds.name as status,
        gd.receivedby as received_by,
        gd.remarks as remarks,
        gdbn.createddate as date
        FROM gpx_delivery_box_number gdbn
        JOIN gpx_delivery gd ON gdbn.delivery_id = gd.id  
        JOIN gpx_delivery_status gds ON gds.id = gdbn.status
        LEFT JOIN gpx_source_destination gsd1 ON gdbn.origin = gsd1.id
        LEFT JOIN gpx_source_destination gsd2 ON gdbn.destination = gsd2.id
        LEFT JOIN gpx_customer gc1 ON gc1.account_no = gd.customer
        LEFT JOIN gpx_employee gemp ON gemp.id = gd.createdby
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

    //get distribution local
    public function getdistlocal($id)
    {
        $query = $this->connection->prepare("SELECT gd.*,gd.id as transaction_no, gd.createddate as date, gd.distribution_type as type,
        gd.destination_name as destination
        , COUNT(gdbn.box_number) as qty ,
        GROUP_CONCAT(gdbn.box_number) as box_number
        FROM gpx_distribution gd
        LEFT JOIN gpx_distribution_box_number gdbn ON gd.id = gdbn.distibution_id
        WHERE gd.id LIKE '%PARTD-%' AND gd.createdby = :id
        GROUP BY gd.id
        ");
        $query->execute(array("id"=>$id));
        $result = $query->fetchAll();
        $this->connection = null;
        return $result;
    }

    //get distribution local admin
    public function getdistlocaladmin()
    {
        $query = $this->connection->prepare("SELECT gd.*,gd.id as transaction_no, gd.createddate as date, gd.distribution_type as type,
        gd.destination_name as destination
        , COUNT(gdbn.box_number) as qty ,
        GROUP_CONCAT(gdbn.box_number) as box_number
        FROM gpx_distribution gd
        LEFT JOIN gpx_distribution_box_number gdbn ON gd.id = gdbn.distibution_id
        GROUP BY gd.id
        ");
        $query->execute();
        $result = $query->fetchAll();
        $this->connection = null;
        return $result;
    }

    //ALL BRANCH PARTNER
    public function getallbranchpartner(){  

        $query = $this->connection->prepare("SELECT * FROM gpx_branch WHERE type LIKE '%Partner%' ORDER BY name ASC");
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }

    //LIST    
    public function getTickets()
    {  
        $query = $this->connection->prepare("SELECT gt.* ,ge.branch, CONCAT(ge.firstname,' ',ge.lastname) as assigned_to
        , gty.name as ticket_type , gts.name as status,
         CONCAT(gc.firstname,' ', gc.lastname) as customer_name
        FROM gpx_tickets gt 
        JOIN gpx_tickets_type gty ON gty.id = gt.ticket_type
        JOIN gpx_tickets_status gts ON gts.id = gt.status
        JOIN gpx_customer gc ON gt.account_no = gc.account_no
        JOIN gpx_employee ge ON ge.id = gt.assigned_to
        JOIN gpx_branch gbranch ON gbranch.id = ge.branch
        WHERE gbranch.type LIKE '%Partner%'
        ");
        $query->execute();    
        $result = $query->fetchAll();
        return $result;     
    }

    public function getPartnerEmployee()
    {
        $query = $this->connection->prepare("SELECT ge.* , 
        CONCAT(ge.firstname, ' ', ge.lastname) as name  , gb.name as branch 
        FROM gpx_employee ge 
        LEFT JOIN gpx_branch gb ON ge.branch = gb.id
        WHERE gb.type LIKE '%Partner%'
        ORDER BY ge.firstname");
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }

    public function getPartnerDrivers()
    {
        $query = $this->connection->prepare("SELECT ge.* , 
        CONCAT(ge.firstname, ' ', ge.lastname) as name  , gb.name as branch 
        FROM gpx_employee ge 
        LEFT JOIN gpx_branch gb ON ge.branch = gb.id
        LEFT JOIN gpx_users gu ON ge.id = gu.employee_id
        LEFT JOIN gpx_role rl ON gu.role_id = rl.id
        WHERE gu.role_id = '6'
        ORDER BY ge.firstname");
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }

    //LIST ROLE PARTNER
    public function getPartnerRoles()
    {        
        $query = $this->connection->prepare("SELECT * FROM gpx_role WHERE name LIKE '%Partner%'");
        $query->execute();    
        $result = $query->fetchAll();
        return $result;
    }

    public function saveRole($data)
    {        
        $query = $this->connection->prepare("INSERT INTO gpx_users VALUES(:username,:password,:employee_id,:role_id)");
        $result = $query->execute(array(
            "username" => $data['username'],
            "password" => $data['password'],
            "employee_id" => $data['employee_id'],
            "role_id" => $data['role_id'],
        ));    
        return $result;
    }

    public function updateRole($data,$id)
    {        
        $query = $this->connection->prepare("UPDATE gpx_users 
        SET 
        username = :username,
        password = :password,
        employee_id = :employee_id,
        role_id = :role_id
        WHERE id = :id");
        $result = $query->execute(array(
            "id"=>$id,
            "username" => $data['username'],
            "password" => $data['password'],
            "employee_id" => $data['employee_id'],
            "role_id" => $data['role_id'],
        ));    
        $this->connection = null; 
        return $result;
    }

    public function checkusername($username)
    {        
        $query = $this->connection->prepare("SELECT count(*) as cnt FROM gpx_users WHERE username = :username");
        $query->execute(array("username"=>$username));    
        $result = $query->fetchColumn();
        $this->connection = null; 
        return $result;
    }

    public function getticketbyid($id)
    {   
        $query = $this->connection->prepare("SELECT * FROM gpx_tickets WHERE id = :id");
        $query->execute(array("id"=>$id));    
        $result = $query->fetchAll();
        return $result;     
    }
  

    //GET DATA BY LOADING ID
    public function getdetails($type, $id)
    {
        switch($type){
            case "intransit":
                $query = $this->connection->prepare("
                SELECT gl.*
                FROM gpx_loading gl
                WHERE gl.id = :transaction_no");
                $query->execute(
                    array(
                        "transaction_no" => $id,
                    )
                );
            break;
            case "dist":
                $query = $this->connection->prepare("SELECT gd.* , CONCAT(ge.firstname, ' ', ge.lastname) as createdby FROM gpx_distribution gd 
                LEFT JOIN gpx_employee ge ON gd.createdby = ge.id
                WHERE gd.id = :id");
                $query->execute(array("id"=>$id));
            break;
            case "deliver":
                $query = $this->connection->prepare("
                SELECT gd.*,
                CONCAT(gc.firstname,' ', gc.lastname) as customer,
                CONCAT(ge.firstname,' ', ge.lastname) as driver_name
                FROM gpx_delivery gd
                LEFT JOIN gpx_customer gc ON gc.account_no = gd.customer
                LEFT JOIN gpx_delivery_box_number gdbn ON gdbn.delivery_id = gd.id
                LEFT JOIN gpx_employee ge ON gd.createdby = ge.id
                WHERE gd.id = :id");
                $query->execute(array("id"=>$id));
            break;
            case "unloads":
                $query = $this->connection->prepare("
                SELECT gu.*
                FROM gpx_unloading gu
                WHERE gu.id = :transaction_no");
                $query->execute(
                    array(
                        "transaction_no" => $id,
                    )
                );
            break;
            default:
                $query = $this->connection->prepare("
                SELECT gl.*
                FROM gpx_loading gl
                WHERE gl.id = :transaction_no");
                $query->execute(
                    array(
                        "transaction_no" => $id,
                    )
                );
            break;
        }
        
        $result = $query->fetchAll();
        return $result;
    }

    public function getboxnumber($type, $id)
    {
        switch($type){
            case "intransit":
                $query = $this->connection->prepare("
                SELECT gwab.box_number as box_number
                FROM gpx_loading_box_number gwab
                WHERE
                loading_id = :transaction_no
                ");
                $query->execute(array("transaction_no" => $id));
            break;
            case "dist":
                $query = $this->connection->prepare("SELECT gb.name as boxtype , gdbn.box_number 
                FROM gpx_distribution_box_number gdbn
                LEFT JOIN gpx_boxtype gb ON gb.id = gdbn.boxtype_id
                WHERE gdbn.distibution_id = :id");
                $query->execute(array("id"=>$id));
            break;
            case "deliver":
                $query = $this->connection->prepare("
                SELECT gdbn.box_number as box_number,pr.provDesc as province,
                ct.citymunDesc as city,
                br.brgyDesc as barangay,
                CONCAT(gc.firstname,' ',gc.lastname) as receiver
                FROM gpx_delivery_box_number gdbn
                LEFT JOIN gpx_customer gc ON gc.account_no = gdbn.receiver
                LEFT JOIN refprovince pr ON pr.provCode = gc.province
                LEFT JOIN refcitymun ct ON ct.citymunCode = gc.city
                LEFT JOIN refbrgy br ON br.brgyCode = gc.barangay
                WHERE gdbn.delivery_id = :id");
                $query->execute(array("id"=>$id));
            break;
            case "unloads":
                $query = $this->connection->prepare("
                SELECT gwab.box_number as box_number
                FROM gpx_unloading_box_number gwab
                WHERE
                unloading_id = :transaction_no
                ");
                $query->execute(
                    array(
                        "transaction_no" => $id,
                    )
                );
            break;

            default:
                $query = $this->connection->prepare("
                SELECT gwab.box_number as box_number
                FROM gpx_loading_box_number gwab
                WHERE
                loading_id = :transaction_no
                ");
                $query->execute(
                    array(
                        "transaction_no" => $id,
                    )
                );
            break;
        }

        $result = $query->fetchAll();
        return $result;
    }

    //GET IMAGES
    public function getimages($type, $id)
    {   
        switch($type){
            case "intransit":break;
            case "unloads":
                $query = $this->connection->prepare("SELECT gai.id as id, gai.* 
                FROM gpx_unloading_boximage gai WHERE gai.transaction_no = :id");
                $query->execute(array("id" => $id));
            break;
            case "dist":
                $query = $this->connection->prepare("SELECT image 
                FROM gpx_all_image WHERE transaction_no = :id AND module = 'distribution'");
                $query->execute(array("id" => $id));
            break;
            case "deliver":
                $query = $this->connection->prepare("SELECT image 
                FROM gpx_all_image WHERE transaction_no = :id AND module = 'delivery'");
                $query->execute(array("id" => $id));
            break;
           default:break;
        }
        
        $result = $query->fetchAll();
        return $result;
    }
    
    //UPDATE DISTRIBUTION PARTNER
    public function updateDist($id,$data){
        $query = $this->connection->prepare("
        UPDATE gpx_distribution 
        SET 
        distribution_type=:type,
        mode_of_shipment=:mode,
        destination_name=:destname,
        truck_number=:truck,
        driver_name=:driver,
        remarks=:remarks,
        etd=:etd,
        eta=:eta
        WHERE id = :id");
        $result = $query->execute(array(
            "id" => $id,
            "type"=> $data['distribution_type'],
            "mode"=> $data['mode_of_shipment'],
            "destname"=> $data['destination_name'],
            "truck"=> $data['truck_number'],
            "driver"=> $data['driver_name'],
            "remarks"=> $data['remarks'],
            "etd"=> $data['etd'],
            "eta"=> $data['eta'],
        ));    
        $this->connection = null; 
        return $result; 
    }
}
?>