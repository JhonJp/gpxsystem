<?php

class GenericModel{

    public $table;
    public $customertype;
 
    public function __construct($connection) {
		$this->connection = $connection;        
    }

    //LOGIN FUNCTION
    public function login($username, $password)
    {       
        $query = $this->connection->prepare("SELECT * FROM gpx_users gu JOIN gpx_employee ge ON gu.employee_id = ge.id 
                                            WHERE username = :username AND password = :password");
        $query->execute(array(
            "username" => $username,            
            "password" => $password            
        ));
        $result = $query->fetchAll();
        $this->connection = null; 
        return $result;
    }

    //LOGIN FUNCTION
    public function customerlogin($username, $password)
    {       
        $query = $this->connection->prepare("
        SELECT * FROM gpx_customer_password gu JOIN gpx_customer gc
        ON gu.account_no = gc.account_no 
        WHERE gu.account_no = :username AND password = :password");
        $query->execute(array(
            "username" => $username,            
            "password" => $password            
        ));
        $result = $query->fetchAll();
        return $result;
    }

    //LOGIN FUNCTION count
    public function countlogin($username, $password)
    {       
        $query = $this->connection->prepare("
        SELECT COUNT(gc.account_no) FROM gpx_customer_password gu JOIN gpx_customer gc
        ON gu.account_no = gc.account_no 
        WHERE gu.account_no = :username AND password = :password");
        $query->execute(array(
            "username" => $username,            
            "password" => $password            
        ));
        $result = $query->fetchColumn();
        return $result;
    }

    //ERROR MESSAGE
    public function error_logs($module,$error_logs){    
        $query = $this->connection->prepare("INSERT INTO gpx_error_logs(module,error_logs) VALUES (:module,:error_logs)");
        $result = $query->execute(array(
            "module" => $module,
            "error_logs" => $error_logs
        ));
    }
    

    //GET USER ID 
    public function getuserlogin(){
        $result = $_SESSION["logindetails"];
        return $result[0]['id'];
    }

    //ALL SALES DRIVER
    public function getallsalesdriver(){                
        $query = $this->connection->prepare("SELECT ge.* , 
        CONCAT(firstname,' ',lastname) as name FROM gpx_employee ge
        JOIN gpx_users gu ON ge.id = gu.employee_id 
        WHERE gu.role_id = 2 ORDER BY ge.firstname");
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }

    //ALL BRANCH
    public function getallbranch(){  

        $query = $this->connection->prepare("SELECT * FROM gpx_branch ORDER BY name ASC");
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }

    //ALL DELIVERY STATUS
    public function getAllDeliveryStatus(){  

        $query = $this->connection->prepare("SELECT * FROM gpx_delivery_status ORDER BY id ASC");
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }

    //ALL DELIVERY SUB STATUS
    public function getAllDeliverySubStatus(){  

        $query = $this->connection->prepare("SELECT * FROM gpx_delivery_substatus ORDER BY id ASC");
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }

    //ALL CUSTOMER (By Type)
    public function getallcustomerbytype()
    {
        $query = $this->connection->prepare("SELECT * , 
        CONCAT(firstname,' ',lastname) as name FROM gpx_customer 
        WHERE type = :type ORDER BY firstname");
        $query->execute(array(
            "type" => $this->customertype
        ));
        $result = $query->fetchAll();
        return $result;
    }
	
	//ALL CUSTOMER
    public function getallcustomers()
    {
        $query = $this->connection->prepare("
        SELECT * , CONCAT(firstname,' ',lastname) as name
        FROM gpx_customer");        
		$query->execute();
        $result = $query->fetchAll();
        return $result;
    }

    //ALL CUSTOMER BY BRANCH
    public function getallcustomersbybranch($id)
    {
        $query = $this->connection->prepare("
        SELECT gc.* , CONCAT(gc.firstname,' ',gc.lastname) as name
        FROM gpx_customer gc
        JOIN gpx_employee ge ON ge.id = gc.createdby
        JOIN gpx_branch gb ON gb.id = ge.branch
        WHERE ge.branch = :branch");        
		$query->execute(array("branch"=>$id));
        $result = $query->fetchAll();
        return $result;
    }

    //ALL BOX TYPE
    public function getallboxtype()
    {
        $query = $this->connection->prepare("SELECT id, CONCAT(name,' (' ,size_length,' x ',size_width, ' x ',size_height,')')  as name
        ,depositprice FROM gpx_boxtype ORDER BY name");
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }

    //ALL CHART ACCOUNT TYPE
    public function getallchartaccounttype()
    {
        $query = $this->connection->prepare("SELECT * FROM gpx_chartaccounts_type ORDER BY name");
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }

    //ALL RESERVATION STATUS
    public function getallreservationstatus()
    {
        $query = $this->connection->prepare("SELECT * FROM gpx_reservation_status ORDER BY id");
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }

    //ALL TRANSACTION NO
    public function getalltransactionno()
    {
        $query = $this->connection->prepare("SELECT transaction_no as id , transaction_no as name FROM gpx_booking");
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }

    //ALL TICKET TYPE
    public function getalltickettype()
    {
        $query = $this->connection->prepare("SELECT * FROM gpx_tickets_type ORDER BY NAME");
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }

    //ALL TICKET STATUS
    public function getallticketstatus()
    {
        $query = $this->connection->prepare("SELECT * FROM gpx_tickets_status ORDER BY id");
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }

    //ALL TICKET PRIORITY
    public function getallticketpriority()
    {
        $query = $this->connection->prepare("SELECT * FROM gpx_tickets_priority	 ORDER BY NAME");
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }

    //ALL BOOKING TYPE
    public function gettallbookingtype()
    {
        $query = $this->connection->prepare("SELECT * FROM gpx_booking_type ORDER BY NAME");
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }
    
    //ALL RESERVATION NO
    public function getallreservationno()
    {
        $query = $this->connection->prepare("SELECT reservation_no as id,reservation_no as name FROM gpx_reservation");
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }

    //ALL PROVINCE
    public function getallprovince()
    {
        $query = $this->connection->prepare("SELECT provCode as id , provDesc as name FROM refprovince ORDER BY provDesc");
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }

    //ALL PROVINCE ALL FIELDS
    public function getallprovincetojson()
    {
        $query = $this->connection->prepare("SELECT * FROM refprovince");
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }

    //ALL CITY
    public function getallcity($provCode)
    {
        $query = $this->connection->prepare("SELECT citymunCode as id , citymunDesc as name FROM refcitymun WHERE provCode = :provCode ORDER BY citymunDesc");
        $query->execute(array("provCode"=>$provCode));
        $result = $query->fetchAll();
        return $result;
    }

    //ALL CITY ALL FIELDS
    public function getallcityjs()
    {
        $query = $this->connection->prepare("SELECT * FROM refcitymun");
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }

    //ALL BARANGAY
    public function getallbarangay($citymunCode)
    {
        $query = $this->connection->prepare("SELECT brgyCode as id , brgyDesc as name FROM refbrgy WHERE citymunCode = :citymunCode ORDER BY brgyDesc");
        $query->execute(array("citymunCode"=>$citymunCode));
        $result = $query->fetchAll();
        return $result;
    }

    //ALL BARANGAY ALL FIELDS
    public function getallbarangayjs()
    {
        $query = $this->connection->prepare("SELECT * FROM refbrgy LIMIt 100");
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }


    //ALL SOURCE LOCATION
    public function getallsource()
    {
        $query = $this->connection->prepare("SELECT * FROM gpx_source_destination WHERE type = 'source'");
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }

    //ALL EXPENSE TYPE
    public function getallexpensetypes()
    {
        $query = $this->connection->prepare("SELECT * FROM gpx_expense_type");
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }

    //ALL DESTINATION LOCATION
    public function getalldestination()
    {
        $query = $this->connection->prepare("SELECT * FROM gpx_source_destination WHERE type = 'destination'");
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }


    //ALL CURRENCY
    public function getallcurrency()
    {
        $query = $this->connection->prepare("SELECT * FROM gpx_currency ORDER BY currency_code");
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }

    //ALL EMPLOYEE
    public function gettallemployee()
    {
        $query = $this->connection->prepare("SELECT ge.*, gr.name as role 
        FROM gpx_employee ge
        JOIN gpx_users gu ON ge.id = gu.employee_id 
        JOIN gpx_role gr ON gr.id = gu.role_id
        ");
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }

    //SAVE TRACK N TRACE
    public function savetrackntrace($data)
    {
        $query = $this->connection->prepare("INSERT INTO gpx_trackntrace(transaction_no,status,dateandtime,
        activity,location,qty,details)
        VALUES(:transaction_no,:status,:dateandtime,:activity,:location,:qty,:details)");
        $result = $query->execute(
            array(
                "transaction_no" => $data['transaction_no'],
                "status" => $data['status'],
                "dateandtime" => $data['dateandtime'],
                "activity" => $data['activity'],
                "location" => $data['location'],
                "qty" => $data['qty'],
                "details" => $data['details'],
            )
        );
    }

    public function insert($data,$table){

        $columnString = implode(',', array_keys($data));
        $valueString = implode(',', array_fill(0, count($data), '?'));
        $query = $this->connection->prepare("INSERT INTO ". $table ." ({$columnString}) VALUES ({$valueString})");
        $result = $query->execute(array_values($data));
        return $result;

    }

    public function deleteSalaryCompensate($id, $table)
  {
    $query = $this->connection->prepare("DELETE FROM ".$table." WHERE id = :id");
    $result = $query->execute(array(
      "id" => $id
    ));    
    return $result;
  }

    ///
    public function maintenanceUpdate($data,$table,$id){

        $query = $this->connection->prepare("UPDATE {$table}  SET name = :name , description = :description WHERE id = :id");
        $result = $query->execute(
            array(
                "name" => $data['name'],
                "description" => $data['description'],
                "id" => $id,
            )
        );
        return $result;
    }

    //ALL MAINTENANCE LIST
    public function getmaintenancelist($table)
    {
        $query = $this->connection->prepare("SELECT * FROM ".$table." ORDER BY name");
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }

    //ALL BOX NUMBER SERIES
    public function getboxnumberseries()
    {
        $query = $this->connection->prepare("SELECT * FROM gpx_barcode_series");
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }

    //ALL BOX NUMBER SERIES
    public function getboxnumbers($id)
    {
        $query = $this->connection->prepare("SELECT * FROM gpx_barcode_series WHERE createdby = :id");
        $query->execute(array("id"=>$id,));
        $result = $query->fetchAll();
        return $result;
    }

    //ALL BOX NUMBER DISTRIBUTED
    public function getboxnumbersbyDriver($id)
    {
        $query = $this->connection->prepare("
        SELECT gbd.*, GROUP_CONCAT(gdbn.boxnumber) as box_number FROM gpx_barcode_distribution gbd 
        JOIN gpx_barcode_distribution_number gdbn ON gbd.transaction_no = gdbn.transaction_no
        WHERE gbd.driver_id = :id");
        $query->execute(array("id"=>$id,));
        $result = $query->fetchAll();
        return $result;
    }

    //ALL BOX NUMBER DISTRIBUTED TO DRIVER
    public function getdistributedtoDriver($id)
    {
        $query = $this->connection->prepare("
        SELECT gbd.*, GROUP_CONCAT(gdbn.boxnumber) as box_number FROM gpx_barcode_distribution gbd 
        JOIN gpx_barcode_distribution_number gdbn ON gbd.transaction_no = gdbn.transaction_no
        WHERE gbd.createdby = :id");
        $query->execute(array("id"=>$id,));
        $result = $query->fetchAll();
        return $result;
    }

    //ALL BOX NUMBER DISTRIBUTED TO DRIVER
    public function getcountdisttodriver($id)
    {
        $query = $this->connection->prepare("
        SELECT COUNT(gdbn.boxnumber) as box_number FROM gpx_barcode_distribution gbd 
        JOIN gpx_barcode_distribution_number gdbn ON gbd.transaction_no = gdbn.transaction_no
        WHERE gbd.createdby = :id");
        $query->execute(array("id"=>$id,));
        $result = $query->fetchColumn();
        return $result;
    }

    //ALL BOX NUMBER DISTRIBUTED
    public function getcountbydriver($id)
    {
        $query = $this->connection->prepare("
        SELECT COUNT(gbd.driver_id) FROM gpx_barcode_distribution gbd 
        WHERE gbd.driver_id = :id");
        $query->execute(array("id"=>$id,));
        $result = $query->fetchColumn();
        return $result;
    }

    public function getcountdirects()
    {
        $query = $this->connection->prepare("
        SELECT COUNT(gbd.distribution_type) FROM gpx_distribution gbd 
        WHERE gbd.distribution_type = 'Direct'");
        $query->execute();
        $result = $query->fetchColumn();
        return $result;
    }

    //ALL BOX RATES
    public function getboxrates()
    {
        $query = $this->connection->prepare("SELECT * FROM gpx_boxrate");
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }

    //ALL RATES NSB 
    public function getnsbrates()
    {
        $query = $this->connection->prepare("SELECT * FROM gpx_nsbrate");
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }

    //ALL WAREHOUSE 
    public function getwarehouse()
    {
        $query = $this->connection->prepare("SELECT * FROM gpx_warehouse");
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }

    public function getmaintenancelistbyid($table,$id)
    {
        $query = $this->connection->prepare("SELECT * FROM ".$table." WHERE id = :id ORDER BY name");
        $query->execute(array("id"=>$id));
        $result = $query->fetchAll();
        return $result;
    }

    //GET LOCATION EMPLOYEE BY ID
    public function getlocationemployeebyid($id)
    {
        $query = $this->connection->prepare("SELECT b.name 
        FROM gpx_employee a 
        JOIN gpx_branch b ON a.branch = b.id 
        WHERE a.id = :id LIMIT 1");
        $query->execute(array("id"=>$id));
        $result = $query->fetchColumn();
        return $result;
    }

    //GET CUSTOMER ADDRESS 
    public function getcustomeraddresss($account_no)
    {
        $query = $this->connection->prepare("SELECT  
        CONCAT(citymunDesc,' ',provDesc) as address       
        FROM gpx_customer a
        LEFT JOIN refcitymun b ON a.city = b.citymunCode
        LEFT JOIN refprovince c ON a.province = b.provCode
        WHERE a.account_no = :account_no
        LIMIT 1
        ");
        $query->execute(array("account_no"=>$account_no));
        $result = $query->fetchColumn();
        return $result;
    }


    //GET DISTRIBUTION //SALES DRIVER
    public function getdistributionsalesdriver()
    {
        $query = $this->connection->prepare("SELECT gd.* ,gdbn.boxtype_id , COUNT(gdbn.box_number) as qty ,
        GROUP_CONCAT(gdbn.box_number) as box_number,
        GROUP_CONCAT(gdbn.boxtype_id) as boxtype_id
        FROM gpx_distribution gd
        LEFT JOIN gpx_distribution_box_number gdbn ON gd.id = gdbn.distibution_id
        WHERE gd.distribution_type = 'Sales Driver' AND gd.status != '0' 
        GROUP BY gd.id 
        ");
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }

    //GET DISTRIBUTION //HUB
    public function getdistributionhub()
    {
        $query = $this->connection->prepare("SELECT gd.* ,gdbn.boxtype_id , COUNT(gdbn.box_number) as qty ,
        GROUP_CONCAT(gdbn.box_number) as box_number,
        GROUP_CONCAT(gdbn.boxtype_id) as boxtype_id
        FROM gpx_distribution gd
        LEFT JOIN gpx_distribution_box_number gdbn ON gd.id = gdbn.distibution_id
        WHERE gd.distribution_type = 'Partner - Hub'
        GROUP BY gd.id 
        ");
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }

    //GET DISTRIBUTION //AREA
    public function getdistributionarea()
    {
        $query = $this->connection->prepare("SELECT gd.* ,gdbn.boxtype_id , COUNT(gdbn.box_number) as qty ,
        GROUP_CONCAT(gdbn.box_number) as box_number,
        GROUP_CONCAT(gdbn.boxtype_id) as boxtype_id
        FROM gpx_distribution gd
        LEFT JOIN gpx_distribution_box_number gdbn ON gd.id = gdbn.distibution_id
        WHERE gd.distribution_type = 'Partner - Area'
        GROUP BY gd.id 
        ");
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }

    //GET DISTRIBUTION //BRANCH
    public function getdistributionbranch()
    {
        $query = $this->connection->prepare("SELECT gd.* ,gdbn.boxtype_id , COUNT(gdbn.box_number) as qty ,
        GROUP_CONCAT(gdbn.box_number) as box_number,
        GROUP_CONCAT(gdbn.boxtype_id) as boxtype_id
        FROM gpx_distribution gd
        LEFT JOIN gpx_distribution_box_number gdbn ON gd.id = gdbn.distibution_id
        WHERE gd.distribution_type = 'GP - Branch'
        GROUP BY gd.id 
        ");
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }

    //GET DISTRIBUTION //DIRECT
    public function getdistributiondirect()
    {
        $query = $this->connection->prepare("SELECT gd.* ,gdbn.boxtype_id , COUNT(gdbn.box_number) as qty ,
        GROUP_CONCAT(gdbn.box_number) as box_number,
        GROUP_CONCAT(gdbn.boxtype_id) as boxtype_id
        FROM gpx_distribution gd
        LEFT JOIN gpx_distribution_box_number gdbn ON gd.id = gdbn.distibution_id
        WHERE gd.distribution_type = 'Direct'
        GROUP BY gd.id 
        ");
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }

    //GET DEPOSIT PRICE
    public function getdepositprice($id)
    {
        $query = $this->connection->prepare("SELECT depositprice FROM gpx_boxtype WHERE id = :id");
        $query->execute(array("id"=>$id));
        $result = $query->fetchColumn();
        return $result;
    }

    public function updateBookingStatus($box_number,$status)
    {
        $query = $this->connection->prepare("SELECT transaction_no FROM gpx_booking_consignee_box WHERE box_number = :box_number LIMIT 1");
        $query->execute(array("box_number" => $box_number));
        $transaction_no = $query->fetchColumn();

        $query = $this->connection->prepare("UPDATE gpx_booking SET booking_status = :status 
        WHERE transaction_no = :transaction_no");
        $result = $query->execute(array(
            "transaction_no" => $transaction_no,
            "status" => $status,
        ));

        $query = $this->connection->prepare("UPDATE gpx_booking_consignee_box SET status = :status 
        WHERE box_number = :box_number");
        $result = $query->execute(array(
            "box_number" => $box_number,
            "status" => $status,
        ));

    }

    public function gettransactionno($box_number)
    {
        $query = $this->connection->prepare("SELECT transaction_no 
        FROM gpx_booking_consignee_box 
        WHERE box_number = :box_number LIMIT 1");
        $query->execute(array("box_number"=>$box_number));
        $result = $query->fetchColumn();
        return $result;
    }



    
}


?>
