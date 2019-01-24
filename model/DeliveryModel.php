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

    public function apisave($data)
    {

        try {
            $countdata = count($data['data']);

            for ($x = 0; $x < $countdata; $x++) {

                $check = $this->checkdata($data['data'][$x]['id']);

                if (count($check) == 0) {

                    $query = $this->connection->prepare("INSERT INTO gpx_delivery(
                    id,transaction_no,createddate,createdby,customer)
                    VALUES (:id,:transaction_no,:createddate,:createdby,:customer)");
                    $result = $query->execute(array(
                        "id" => $data['data'][$x]['id'],
                        "transaction_no" => $data['data'][$x]['transaction_no'],
                        "createddate" => $data['data'][$x]['createddate'],
                        "createdby" => $data['data'][$x]['createdby'],
                        "customer" => $data['data'][$x]['customer'],
                    ));

                    $countboxnumber = count($data['data'][$x]['delivery_box']);

                    for ($y = 0; $y < $countboxnumber; $y++) {

                        $query2 = $this->connection->prepare("INSERT INTO gpx_delivery_box_number(
                        box_number,transaction_no,receiver,origin,destination,delivery_id,createddate,status,remarks)
                        VALUES (:box_number,:transaction_no,:receiver,:origin,:destination,:delivery_id,:createddate,:status,:remarks)");
                        $result = $query2->execute(array(
                            "box_number" => $data['data'][$x]['delivery_box'][$y]['box_number'],
                            "transaction_no" => $data['data'][$x]['transaction_no'],                            
                            "receiver" => $data['data'][$x]['delivery_box'][$y]['receiver'],                            
                            "origin" => $data['data'][$x]['delivery_box'][$y]['origin_id'],                            
                            "destination" => $data['data'][$x]['delivery_box'][$y]['destination_id'],                            
                            "delivery_id" => $data['data'][$x]['delivery_box'][$y]['delivery_id'],                            
                            "createddate" => $data['data'][$x]['delivery_box'][$y]['createddate'],                            
                            "status" => $data['data'][$x]['delivery_box'][$y]['status'],                            
                            "remarks" => $data['data'][$x]['delivery_box'][$y]['remarks'],                            
                        ));

                        ///////TRACK AND TRACE////////
                        $logs = array(
                            "transaction_no" => $this->gettransactionno($data['data'][$x]['delivery_box'][$y]['box_number']),
                            "status" => "Delivered",
                            "dateandtime" => $data['data'][$x]['createddate'],
                            "activity" => "Delivered",
                            "location" => $this->getcustomeraddresss($data['data'][$x]['delivery_box'][0]['receiver']),
                            "qty" => $countboxnumber,
                            "details" =>"Remarks: ".$data['data'][$x]['delivery_box'][0]['remarks']
                            
                        );
                        $this->savetrackntrace($logs);

                    }

                    //delivery_image
                    $deliverycountimages = count($data['data'][$x]['delivery_image']);
                    for ($y = 0; $y < $deliverycountimages; $y++) {

                    $query = $this->connection->prepare("INSERT INTO
                     gpx_delivery_image(
                            delivery_id,images)
                        VALUES (:delivery_id,:images)");
                        $result = $query->execute(array(
                            "delivery_id" => $data['data'][$x]['id'],
                            "images" => str_replace("\\","",$data['data'][$x]['delivery_image'][$y]['image']),
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

    public function checkdata($id)
    {

        $query = $this->connection->prepare("SELECT * FROM gpx_delivery WHERE id = :id");
        $query->execute(array("id" => $id));
        $result = $query->fetchAll();
        return $result;
    }

}
?>