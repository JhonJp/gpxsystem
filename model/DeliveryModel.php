<?php
require_once  __DIR__ . "/GenericModel.php";

class DeliveryModel extends GenericModel
{

    public function __construct($connection)
    {
		parent::__construct($connection);
    }

    public function getlist()
    {        
        $query = $this->connection->prepare("SELECT gd.id,        
        CONCAT(gc1.firstname, ' ',gc1.lastname) as customer,        
        GROUP_CONCAT(gdbn.box_number) as box_number,
        CONCAT(gc2.firstname, ' ',gc2.lastname)  as receiver,
        gsd1.name as origin ,
        gsd2.name as destination,
        gds.name as status,
        gd.receivedby as received_by,
        CONCAT(gemp.firstname, ' ',gemp.lastname)  as delivered_by,
        gdbn.createddate as delivered_date
        FROM gpx_delivery_box_number gdbn
        JOIN gpx_delivery  gd ON gdbn.delivery_id = gd.id  
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
        gds.name
        ");
        $query->execute();    
        $result = $query->fetchAll();
        $this->connection = null; 
        return $result;
    }

    public function apisave($data)
    {

        try {
            $countdata = count($data['data']);

            for ($x = 0; $x < $countdata; $x++) {

                $check = $this->checkdata($data['data'][$x]['id']);

                if (count($check) == 0) {

                    $query = $this->connection->prepare("INSERT INTO gpx_delivery(
                    id,transaction_no,createddate,createdby,customer,receivedby,relationship,remarks,signature)
                    VALUES (:id,:transaction_no,:createddate,:createdby,:customer,:receivedby,:relationship,:remarks,:signature)");
                    $result = $query->execute(array(
                        "id" => $data['data'][$x]['id'],
                        "transaction_no" => $data['data'][$x]['transaction_no'],
                        "createddate" => $data['data'][$x]['createddate'],
                        "createdby" => $data['data'][$x]['createdby'],
                        "customer" => $data['data'][$x]['customer'],
                        "receivedby" => $data['data'][$x]['receivedby'],
                        "relationship" => $data['data'][$x]['relationship'],
                        "remarks" => $data['data'][$x]['remarks'],
                        "signature" => $data['data'][$x]['signature'],
                    ));

                    $countboxnumber = count($data['data'][$x]['delivery_box']);

                    for ($y = 0; $y < $countboxnumber; $y++) {

                        $query2 = $this->connection->prepare("INSERT INTO gpx_delivery_box_number(
                        box_number,transaction_no,receiver,origin,destination,delivery_id,createddate,status)
                        VALUES (:box_number,:transaction_no,:receiver,:origin,:destination,:delivery_id,:createddate,:status)");
                        $result = $query2->execute(array(
                            "box_number" => $data['data'][$x]['delivery_box'][$y]['box_number'],
                            "transaction_no" => $data['data'][$x]['transaction_no'],                            
                            "receiver" => $data['data'][$x]['delivery_box'][$y]['receiver'],                            
                            "origin" => $data['data'][$x]['delivery_box'][$y]['origin_id'],                            
                            "destination" => $data['data'][$x]['delivery_box'][$y]['destination_id'],                            
                            "delivery_id" => $data['data'][$x]['delivery_box'][$y]['delivery_id'],                            
                            "createddate" => $data['data'][$x]['delivery_box'][$y]['createddate'],                            
                            "status" => $data['data'][$x]['delivery_box'][$y]['status'],                            
                        ));

                        $timestamp = strtotime($data['data'][$x]['createddate']);
                        $newformatdate = date('d/M/Y', $timestamp);

                        ///////TRACK AND TRACE////////
                        $logs = array(
                            "transaction_no" => $data['data'][$x]['delivery_box'][$y]['box_number'],
                            "status" => "Delivered",
                            "dateandtime" => $data['data'][$x]['createddate'],
                            "activity" => "Delivered",
                            "location" => $this->getcustomeraddresss($data['data'][$x]['delivery_box'][0]['receiver']),
                            "qty" => $countboxnumber,
                            "details" =>"Box has been delivered on ".$newformatdate.". Remarks: ".$data['data'][$x]['remarks']
                            
                        );
                        $this->savetrackntrace($logs);

                    }

                    //INSERT IMAGE ATTACHMENT
                    $countimage = count($data['data'][$x]['delivery_image']);
                    for ($image = 0; $image < $countimage; $image++) {

                        $query2 = $this->connection->prepare("INSERT INTO gpx_all_image(module,transaction_no,image) VALUES (:module,:trans,:image)");
                        $result = $query2->execute(array(
                            "module" => $data['data'][$x]['delivery_image'][$image]['module'],
                            "trans" => $data['data'][$x]['id'],                            
                            "image" => $data['data'][$x]['delivery_image'][$image]['image'],                        
                        ));
                    }
                    //////////UPDATE BOOKING STATUS//////////
                    $this->updateBookingStatus( $data['data'][$x]['delivery_box'][0]['box_number'],"6");

                }
            }

        } catch (Exception $e) {
            $this->error_logs("Delivery - apisave", $e->getMessage());
        }

    }

    public function getdeliverydata($transaction_no)
    {
        $query = $this->connection->prepare("SELECT
        gbcb.* ,
        CONCAT(gc1.firstname, ' ', gc1.lastname) as receiver
        FROM gpx_booking_consignee_box gbcb
        LEFT JOIN gpx_customer gc1 ON gbcb.consignee = gc1.account_no
        LEFT JOIN gpx_reservation_status grs  ON grs.id = gc1.account_no
        WHERE
        transaction_no = :transaction_no
        ");
        $query->execute(
            array(
                "transaction_no" => $transaction_no,
            )
        );
        $result = $query->fetchAll();
        return $result;
    }

    //GET DELIVERY IMAGES
    public function getimages($trans)
    {
        $query = $this->connection->prepare("SELECT image 
        FROM gpx_all_image WHERE transaction_no = :id AND module = 'delivery'");
        $query->execute(array("id" => $trans));
        $result = $query->fetchAll();
        return $result;
    }

    public function checkdata($id)
    {
        $query = $this->connection->prepare("
        SELECT gd.*,
        CONCAT(gc.firstname,' ',gc.lastname) as sender
        FROM gpx_delivery gd
        LEFT JOIN gpx_customer gc ON gc.account_no = gd.customer
        WHERE gd.id = :id");
        $query->execute(array("id" => $id));
        $result = $query->fetchAll();
        return $result;
    }

    public function getdeliveryboxnumber($id){
        $query = $this->connection->prepare("
        SELECT gdbn.*,CONCAT(gc.firstname,' ',gc.lastname) as receiver
        FROM gpx_delivery_box_number gdbn
        LEFT JOIN gpx_delivery gd ON gd.id = gdbn.delivery_id
        LEFT JOIN gpx_customer gc ON gc.account_no = gdbn.receiver
        WHERE gdbn.delivery_id = :id");
        $query->execute(array("id" => $id));
        $result = $query->fetchAll();
        return $result;
    }

    public function getdeliveries($id){
        $query = $this->connection->prepare("SELECT * FROM gpx_delivery
        WHERE createdby = :id");
        $query->execute(array("id" => $id));
        $result = $query->fetchAll();
        return $result;
    }

    public function getdeliveryboxes(){
        $query = $this->connection->prepare("SELECT * FROM gpx_delivery_box_number");
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }

    public function getproof(){
        $query = $this->connection->prepare("SELECT * FROM gpx_delivery_image");
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }

    public function getundelivered(){
        $query = $this->connection->prepare("
        SELECT gd.*,
        GROUP_CONCAT(gdbn.box_number) as box_number
        FROM gpx_delivery gd
        LEFT JOIN gpx_delivery_box_number ON gd.id = gdbn.delivery_id
        WHERE gdbn.status = '2'");
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }

}
?>