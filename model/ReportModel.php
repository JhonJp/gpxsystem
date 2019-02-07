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
        $query = $this->connection->prepare("
        SELECT 
        gwi.manufacturer_name as manufacturer_name,
        gb.name as box_type,
        gwi.createddate as date,
        gw.name as warehouse_name,
        SUM(gwi.quantity) as qty,
        SUM(gwi.price) as amount
        FROM gpx_warehouse_inventory gwi
        JOIN gpx_warehouse gw ON gwi.warehouse_id = gw.id
        JOIN gpx_boxtype gb ON gwi.boxtype_id = gb.id
        GROUP BY gwi.manufacturer_name
        ");
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }

    //BOX PURCHASE REPORT OVERRIDE
    public function boxpurchasereportbydate($datefrom, $dateto)
    {        
        $query = $this->connection->prepare("
        SELECT 
        gwi.manufacturer_name as manufacturer_name,
        gb.name as box_type,
        gwi.createddate as date,
        gw.name as warehouse_name,
        SUM(gwi.quantity) as qty,
        SUM(gwi.price) as amount
        FROM gpx_warehouse_inventory gwi
        JOIN gpx_warehouse gw ON gwi.warehouse_id = gw.id
        JOIN gpx_boxtype gb ON gwi.boxtype_id = gb.id
        WHERE gwi.createddate BETWEEN :first AND :second
        GROUP BY gwi.manufacturer_name
        ");
        $query->execute(array("first"=>$datefrom,"second"=>$dateto));
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
        COUNT(gbookbox.transaction_no) as qty,
        COUNT(gbookbox.transaction_no) as total
        FROM gpx_employee gemp
        JOIN gpx_branch gbranch ON gbranch.id = gemp.branch
        JOIN gpx_booking gwbook ON gwbook.createdby = gemp.id
        JOIN gpx_booking_consignee_box gbookbox ON gbookbox.transaction_no = gwbook.transaction_no
        JOIN gpx_payment gpayment ON gpayment.transaction_no = gwbook.transaction_no
        WHERE gpayment.paymentterm = 'Full'
        GROUP BY 
        gemp.id,
        gemp.branch
        ");
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }

    //SALES REPORT BY BRANCH
    public function salesreportbybranch()
    {        
        $query = $this->connection->prepare("
        SELECT 
        gbranch.name as branch,
         gb.transaction_no as transaction_number,
          CONCAT(ge.firstname, ' ',ge.lastname) as employee_name,
           SUM(gpay.total_amount) as total_amount,
           gpay.createddate as date
            FROM gpx_booking gb 
            LEFT JOIN gpx_employee ge ON gb.createdby = ge.id 
            LEFT JOIN gpx_branch gbranch ON ge.branch = gbranch.id 
            LEFT JOIN gpx_payment gpay ON gb.transaction_no = gpay.transaction_no 
            
            GROUP BY gpay.createddate
        ");
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }

    //OVERRIDE SALES REPORT BY BRANCH
    public function salesreportbybranchname($branch)
    {        
        $query = $this->connection->prepare("
        SELECT 
        gbranch.name as branch,
         gb.transaction_no as transaction_number,
          CONCAT(ge.firstname, ' ',ge.lastname) as employee_name,
           SUM(gpay.total_amount) as total_amount,
           gpay.createddate as date
            FROM gpx_booking gb 
            JOIN gpx_employee ge ON gb.createdby = ge.id 
            JOIN gpx_branch gbranch ON ge.branch = gbranch.id 
            JOIN gpx_payment gpay ON gb.transaction_no = gpay.transaction_no 
            WHERE gbranch.id = :branch
            GROUP BY gpay.createddate
        ");
        $query->execute(array("branch"=>$branch));
        $result = $query->fetchAll();
        return $result;
    }

    //OVERRIDE SALES REPORT BY DATE RANGE
    public function salesbybranchdate($first, $second)
    {        
        $query = $this->connection->prepare("
        SELECT 
        gbranch.name as branch,
         gb.transaction_no as transaction_number,
          CONCAT(ge.firstname, ' ',ge.lastname) as employee_name,
           SUM(gpay.total_amount) as total_amount,
           gpay.createddate as date
            FROM gpx_booking gb 
            JOIN gpx_employee ge ON gb.createdby = ge.id 
            JOIN gpx_branch gbranch ON ge.branch = gbranch.id 
            JOIN gpx_payment gpay ON gb.transaction_no = gpay.transaction_no 
            WHERE gpay.createddate BETWEEN :first AND :second
            OR gpay.createddate LIKE :like
            GROUP BY gpay.createddate
        ");
        $query->execute(array("first"=>$first,"second"=>$second,"like"=>"%$first%"));
        $result = $query->fetchAll();
        return $result;
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

    //SALES REPORT BY EMPLOYEE
    public function salesreportbyemployee()
    {        
        $query = $this->connection->prepare("
        SELECT 
        CONCAT(ge.firstname, ' ',ge.lastname) as employee_name,
        gb.transaction_no as transaction_number,
        gb.boxtype as box_type,
        gpay.createddate as date,
        COUNT(gb.boxtype) as qty,
        gpay.total_amount as total_amount
        FROM gpx_booking_consignee_box gb 
        LEFT JOIN gpx_payment gpay ON gb.transaction_no = gpay.transaction_no 
        LEFT JOIN gpx_employee ge ON gpay.createdby = ge.id
        
        GROUP BY gb.transaction_no
        ");
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }

    //LIST OF RESERVATIONS
    public function getreserves()
    {
        $query = $this->connection->prepare("SELECT
        gr.id ,
        gr.reservation_no,
        CONCAT(ge.firstname, ' ',ge.lastname) as assigned_to,
        CONCAT(gc.firstname, ' ',gc.lastname) as customer,
        SUM(grb.quantity) as qty ,
        CONCAT(SUM(grb.deposit)) as deposit ,
        gr.createddate as date,
        grs.name as status
        FROM gpx_reservation gr
        JOIN gpx_reservation_boxtype grb ON gr.reservation_no = grb.reservation_no
        LEFT JOIN gpx_customer gc ON gc.account_no = gr.account_no
        JOIN gpx_employee ge ON gr.assigned_to = ge.id
        LEFT JOIN gpx_reservation_status grs ON grs.id = gr.status
        GROUP BY gr.id
        ORDER BY gr.createddate DESC

        ");
        $query->execute();
        $result = $query->fetchAll();
        $this->connection = null;
        return $result;
    }

    //LIST OF RESERVATIONS OVERRIDE
    public function getreservesbydate($datefrom, $dateto)
    {
        $query = $this->connection->prepare("SELECT
        gr.id ,
        gr.reservation_no,
        CONCAT(ge.firstname, ' ',ge.lastname) as assigned_to,
        CONCAT(gc.firstname, ' ',gc.lastname) as customer,
        SUM(grb.quantity) as qty ,
        CONCAT(SUM(grb.deposit)) as deposit ,
        gr.createddate as date,
        grs.name as status
        FROM gpx_reservation gr
        JOIN gpx_reservation_boxtype grb ON gr.reservation_no = grb.reservation_no
        LEFT JOIN gpx_customer gc ON gc.account_no = gr.account_no
        JOIN gpx_employee ge ON gr.assigned_to = ge.id
        LEFT JOIN gpx_reservation_status grs ON grs.id = gr.status
        WHERE gr.createddate BETWEEN :datefrom AND :dateto
        OR gr.createddate LIKE :like
        GROUP BY gr.id
        ORDER BY gr.createddate DESC

        ");
        $query->execute(array("datefrom"=>$datefrom, "dateto"=>$dateto,"like"=>"%$datefrom%"));
        $result = $query->fetchAll();
        $this->connection = null;
        return $result;
    }

    //SALES REPORT BY EMPLOYEE BY DATE RANGE OVERRIDE
    public function reportbydaterangeemployee($datefirst, $datesec)
    {        
        $query = $this->connection->prepare("
        SELECT 
        CONCAT(ge.firstname, ' ',ge.lastname) as employee_name,
        gb.transaction_no as transaction_number,
        gb.boxtype as box_type,
        gpay.createddate as date,
        COUNT(gb.boxtype) as qty,
        gpay.total_amount as total_amount
        FROM gpx_booking_consignee_box gb 
        LEFT JOIN gpx_payment gpay ON gb.transaction_no = gpay.transaction_no 
        LEFT JOIN gpx_employee ge ON gpay.createdby = ge.id
        WHERE gpay.createddate BETWEEN :first AND :second
        OR gpay.createddate LIKE :like
        GROUP BY gb.transaction_no
        ");
        $query->execute(array("first"=>$datefirst,"second"=>$datesec,"like"=>"%$datefirst%"));
        $result = $query->fetchAll();
        return $result;
    }

    public function reportbyemployee($empid)
    {        
        $query = $this->connection->prepare("
        SELECT 
        CONCAT(ge.firstname, ' ',ge.lastname) as employee_name,
        gb.transaction_no as transaction_number,
        gb.boxtype as box_type,
        gpay.createddate as date,
        COUNT(gb.boxtype) as qty,
        gpay.total_amount as total_amount
        FROM gpx_booking_consignee_box gb 
        JOIN gpx_payment gpay ON gb.transaction_no = gpay.transaction_no 
        JOIN gpx_employee ge ON gpay.createdby = ge.id
        WHERE ge.id = :empid
        GROUP BY gb.transaction_no
        ");
        $query->execute(array("empid"=>$empid));
        $result = $query->fetchAll();
        return $result;
    }

    //SALES REPORT BY DATE
    public function salesreportbydate()
    {        
        $query = $this->connection->prepare("
        SELECT
        gb.boxtype as box_type,
        gpay.createddate as date,
        COUNT(gb.boxtype) as qty,
        gpay.total_amount as total_amount
        FROM gpx_booking_consignee_box gb 
        LEFT JOIN gpx_payment gpay ON gb.transaction_no = gpay.transaction_no 
        LEFT JOIN gpx_employee ge ON gpay.createdby = ge.id
        
        GROUP BY gb.transaction_no
        ");
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }

    //SALES REPORT BY DATE OVERRIDE
    public function reportbydaterange($datefrom, $dateto)
    {        
        $query = $this->connection->prepare("
        SELECT
        gb.boxtype as box_type,
        gpay.createddate as date,
        COUNT(gb.boxtype) as qty,
        gpay.total_amount as total_amount
        FROM gpx_booking_consignee_box gb 
        LEFT JOIN gpx_payment gpay ON gb.transaction_no = gpay.transaction_no 
        LEFT JOIN gpx_employee ge ON gpay.createdby = ge.id
        WHERE gpay.createddate BETWEEN :first AND :second
        OR gpay.createddate LIKE :like
        GROUP BY gb.transaction_no
        ");
        $query->execute(array("first"=>$datefrom,"second"=>$dateto,"like"=>"%$datefrom%"));
        $result = $query->fetchAll();
        return $result;
    }

    //BOX DISPOSED REPORT
    public function boxdisposed()
    {        
        $query = $this->connection->prepare("
        SELECT gpd.id as distribution_id,
        gpd.destination_name as destination,
        gpd.truck_number as truck_number,
        gpd.createddate as date,
        CONCAT(ge.firstname, ' ', ge.lastname) as released_by,
        gbn.name as boxtype,
        COUNT(gpdb.box_number) as box_quantity

        FROM gpx_distribution gpd
        LEFT JOIN gpx_distribution_box_number gpdb ON gpd.id = gpdb.distibution_id
        LEFT JOIN gpx_boxtype gbn ON gbn.id = gpdb.boxtype_id
        LEFT JOIN gpx_employee ge ON ge.id = gpd.createdby
        WHERE gpd.id LIKE '%GPDIST-%'

        GROUP BY gpd.id, gbn.id
        ");
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }

    //BOX DISPOSED REPORT OVERRIDE BY DATE RANGE
    public function boxdisposedbydate($datefrom, $dateto)
    {        
        $query = $this->connection->prepare("
        SELECT gpd.id as distribution_id,
        gpd.destination_name as destination,
        gpd.truck_number as truck_number,
        gpd.createddate as date,
        CONCAT(ge.firstname, ' ', ge.lastname) as released_by,
        gbn.name as boxtype,
        COUNT(gpdb.box_number) as box_quantity

        FROM gpx_distribution gpd
        LEFT JOIN gpx_distribution_box_number gpdb ON gpd.id = gpdb.distibution_id
        LEFT JOIN gpx_boxtype gbn ON gbn.id = gpdb.boxtype_id
        LEFT JOIN gpx_employee ge ON ge.id = gpd.createdby
        WHERE gpd.id LIKE '%GPDIST-%' AND gpd.createddate BETWEEN :datefrom AND :dateto
        OR gpd.createddate LIKE :like
        GROUP BY gpd.id
        ");
        $query->execute(array("datefrom"=>$datefrom,"dateto"=>$dateto,"like"=>"%$datefrom%"));
        $result = $query->fetchAll();
        return $result;
    }

    //LIST BOOKING
    public function getlistbooking()
    {
        $query = $this->connection->prepare("
        SELECT gb.transaction_no , CONCAT(gc.firstname,' ',gc.lastname) as customer ,
        gb.book_date as date , gbs.name as status , COUNT(gbcb.box_number) as qty,gpay.total_amount as amount_paid 
        FROM
        gpx_booking gb
        LEFT JOIN gpx_customer gc ON gb.customer = gc.account_no
        JOIN gpx_booking_status gbs ON gb.booking_status = gbs.id
        LEFT JOIN gpx_booking_consignee_box gbcb ON gb.transaction_no = gbcb.transaction_no
        JOIN gpx_payment gpay ON gb.transaction_no = gpay.transaction_no
        GROUP BY gb.transaction_no
        ");
        $query->execute();
        $result = $query->fetchAll();
        $this->connection = null;
        return $result;
    }

    //LIST BOOKING BY DATE RANGE
    public function getbookedbydate($datefrom, $dateto)
    {
        $query = $this->connection->prepare("
        SELECT gb.transaction_no , CONCAT(gc.firstname,' ',gc.lastname) as customer ,
        gb.book_date as date , gbs.name as status , COUNT(gbcb.box_number) as qty,gpay.total_amount as amount_paid 
        FROM
        gpx_booking gb
        LEFT JOIN gpx_customer gc ON gb.customer = gc.account_no
        JOIN gpx_booking_status gbs ON gb.booking_status = gbs.id
        LEFT JOIN gpx_booking_consignee_box gbcb ON gb.transaction_no = gbcb.transaction_no
        JOIN gpx_payment gpay ON gb.transaction_no = gpay.transaction_no
        WHERE gb.book_date BETWEEN :datefrom AND :dateto
        OR gb.book_date LIKE :like
        GROUP BY gb.transaction_no
        ");
        $query->execute(array("datefrom"=>$datefrom,"dateto"=>$dateto,"like"=>"%$datefrom%"));
        $result = $query->fetchAll();
        $this->connection = null;
        return $result;
    }

    //get loading list
    public function getloadinglist()
    {
        $query = $this->connection->prepare("SELECT gl.id,
        gl.loaded_date as date,
        gl.shipping_name as shipping_name,
        gl.container_no as container_no,
        gl.eta as eta,
        gl.etd as etd,
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

    //get loading list by date range
    public function loadbydate($datefrom, $dateto)
    {
        $query = $this->connection->prepare("SELECT 
        gl.id,
        gl.loaded_date as date,
        gl.shipping_name as shipping_name,
        gl.container_no as container_no,
        gl.eta as eta,
        gl.etd as etd,
        COUNT(glbn.box_number) as qty,
        (SELECT GROUP_CONCAT(a.box_number) FROM gpx_loading_box_number a WHERE a.loading_id = gl.id) as box_number
        FROM gpx_loading gl
        LEFT JOIN gpx_loading_box_number glbn ON gl.id = glbn.loading_id
        WHERE gl.loaded_date BETWEEN :datefrom AND :dateto
        OR gl.loaded_date LIKE :like
        GROUP BY gl.id");
        $query->execute(array("datefrom"=>$datefrom,"dateto"=>$dateto,"like"=>"%$datefrom%"));
        $result = $query->fetchAll();
        $this->connection = null;
        return $result;
    }

    //get unloading content
    public function getunllist()
    {
        $query = $this->connection->prepare("SELECT 
        gu.id,gu.unload_date as date,
        gu.container_no as container_no,
        gu.forwarder_name as forwarder_name,
        gu.arrival_time as arrival_time,
        COUNT(gubn.box_number) as qty,
        (SELECT GROUP_CONCAT(gcb.box_content) FROM gpx_unloading_box_number gub
        LEFT JOIN gpx_booking_consignee_box gcb ON gcb.box_number = gub.box_number
        WHERE gub.unloading_id = gu.id) as box_content,
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

    //get unloading content override by date filter
    public function getunlbydate($datefrom, $dateto)
    {
        $query = $this->connection->prepare("SELECT 
        gu.id,gu.unload_date as date,
        gu.container_no as container_no,
        gu.forwarder_name as forwarder_name,
        gu.arrival_time as arrival_time,
        COUNT(gubn.box_number) as qty,
        (SELECT GROUP_CONCAT(gcb.box_content) FROM gpx_unloading_box_number gub
        LEFT JOIN gpx_booking_consignee_box gcb ON gcb.box_number = gub.box_number
        WHERE gub.unloading_id = gu.id) as box_content,
        (SELECT GROUP_CONCAT(a.box_number) FROM gpx_unloading_box_number a WHERE a.unloading_id = gu.id) as box_number
        FROM gpx_unloading gu 
        LEFT JOIN gpx_unloading_box_number gubn ON gu.id = gubn.unloading_id
        WHERE gu.unload_date BETWEEN :datefrom AND :dateto
        OR gu.unload_date LIKE :like
        GROUP BY gu.id
        ");
        $query->execute(array("datefrom"=>$datefrom, "dateto"=>$dateto,"like"=>"%$datefrom%"));
        $result = $query->fetchAll();
        $this->connection = null;
        return $result;
    }

    //ALL BRANCH
    public function getallbranch(){  

        $query = $this->connection->prepare("SELECT * FROM gpx_branch ORDER BY name ASC");
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }

    // GET BOOKING WITH CONSIGNEES
    public function getbookingwithconsignee(){
        $query = $this->connection->prepare("
        SELECT gbc.box_number as box_number,
        gb.book_date as book_date,
        gbc.box_content as description,
        CONCAT(gc.firstname,' ',gc.lastname) as name_of_sender
        FROM gpx_booking gb
        LEFT JOIN gpx_booking_consignee_box gbc ON gbc.transaction_no = gb.transaction_no
        LEFT JOIN gpx_customer gc ON gc.account_no = gb.customer
        ORDER BY gb.id ASC
        ");
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }

    // GET BOOKING WITH CONSIGNEES FILTER BY DATE
    public function getbookingwithconsigneebydate($datefrom,$dateto){
        $query = $this->connection->prepare("
        SELECT gbc.box_number as box_number,
        gb.book_date as book_date,
        gbc.box_content as description,
        CONCAT(gc.firstname,' ',gc.lastname) as name_of_sender
        FROM gpx_booking gb
        LEFT JOIN gpx_booking_consignee_box gbc ON gbc.transaction_no = gb.transaction_no
        LEFT JOIN gpx_customer gc ON gc.account_no = gb.customer
        WHERE gb.book_date BETWEEN :datefrom AND :dateto
        OR gb.book_date LIKE :like
        ORDER BY gb.id
        ");
        $query->execute(array(
            "datefrom"=>$datefrom,
            "dateto"=>$dateto,
            "like"=>"%$datefrom%"));
        $result = $query->fetchAll();
        return $result;
    }

    
}
?>