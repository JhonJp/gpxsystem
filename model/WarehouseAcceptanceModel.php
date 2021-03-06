<?php
require_once __DIR__ . "/GenericModel.php";

class WarehouseAcceptanceModel extends GenericModel
{

    public function __construct($connection)
    {
        parent::__construct($connection);
    }

    public function getlist()
    {
        $query = $this->connection->prepare("SELECT gwa.id,
        gw.name  as warehouse_name, gwa.truck_no ,
        CONCAT(ged.firstname , ' ' , ged.lastname) as deliver_by ,
        ged.id as driver_id,
        CONCAT(gea.firstname , ' ' , gea.lastname) as accepted_by ,
        gwa.createddate as accepted_date,
        COUNT(gwab.box_number) as qty,
        (SELECT GROUP_CONCAT(bt.boxtype) FROM gpx_booking_consignee_box bt
        LEFT JOIN gpx_warehouse_acceptance_box_number wa ON bt.box_number = wa.box_number) as box_type,
        (SELECT GROUP_CONCAT(a.box_number) FROM gpx_warehouse_acceptance_box_number a
        WHERE a.warehouse_acceptance_id = gwa.id) as box_number
        FROM gpx_warehouse_acceptance gwa
        JOIN gpx_warehouse gw ON gwa.warehouse_id = gw.id
        JOIN gpx_warehouse_acceptance_box_number gwab ON gwab.warehouse_acceptance_id = gwa.id
        JOIN gpx_employee ged ON gwa.delivered_by = ged.id
        JOIN gpx_employee gea ON gwa.createdby = gea.id
        GROUP BY gwa.id
        ");
        $query->execute();
        $result = $query->fetchAll();
        $this->connection = null;
        return $result;
    }

    public function apisave($data)
    {
        $quant = 1;
        try {
            $countdata = count($data['data']);

            for ($x = 0; $x < $countdata; $x++) {

                $id = $data['data'][$x]['transaction_no'];

                $check = $this->checkdata($id);

                if (count($check) == 0) {

                    $query = $this->connection->prepare("INSERT INTO gpx_warehouse_acceptance(
                    id,warehouse_id,delivered_by,truck_no,createddate,createdby)
                    VALUES (:id,:warehouse_id,:delivered_by,:truck_no,:createddate,:createdby)");
                    $result = $query->execute(array(
                        "id" => $data['data'][$x]['transaction_no'],
                        "warehouse_id" => $data['data'][$x]['warehouse_id'],
                        "delivered_by" => $data['data'][$x]['salesdriver_id'],
                        "truck_no" => $data['data'][$x]['truck_no'],
                        "createddate" => $data['data'][$x]['createddate'],
                        "createdby" => $data['data'][$x]['createdby'],
                    ));

                    $countboxnumber = count($data['data'][$x]['acceptance_box']);
                    for ($y = 0; $y < $countboxnumber; $y++) {

                        $query = $this->connection->prepare("INSERT INTO gpx_warehouse_acceptance_box_number(
                            box_number,warehouse_acceptance_id)
                            VALUES (:box_number,:warehouse_acceptance_id)");
                        $result = $query->execute(array(
                            "box_number" => $data['data'][$x]['acceptance_box'][$y]['boxnumber'],
                            "warehouse_acceptance_id" => $data['data'][$x]['acceptance_box'][$y]['transaction_no'],
                        ));

                        //////////UPDATE BOOKING STATUS//////////
                        $this->updateBookingStatus($data['data'][$x]['acceptance_box'][$y]['boxnumber'],"2");

                        ///////////TRACK N TRACE LOGS/////////
                        $logs = array(
                            "transaction_no" => $data['data'][$x]['acceptance_box'][$y]['boxnumber'],
                            "status" => "Accepted",
                            "dateandtime" => $data['data'][$x]['createddate'],
                            "activity" => "Warehouse Accepted",
                            "location" => $this->getlocationemployeebyid($data['data'][$x]['createdby']),
                            "qty" => "1",
                            "details" => "Accepted at ".$this->getlocationemployeebyid($data['data'][$x]['createdby'])
                        );
                        $this->savetrackntrace($logs);

                    }
                    
                }

            }
        } catch (Exception $e) {
            $this->error_logs("WarehouseAcceptance - apisave", $e->getmessage());
        }
    }

    public function checkdata($id)
    {
        $query = $this->connection->prepare("SELECT * FROM gpx_warehouse_acceptance WHERE id = :id");
        $query->execute(array("id" => $id));
        $result = $query->fetchAll();
        return $result;
    }

    public function getdetails($transaction_no)
    {
        $query = $this->connection->prepare("
        SELECT gwa.*, gw.name as warehouse,
        CONCAT(ge.firstname,' ',ge.lastname) as driver 
        FROM gpx_warehouse_acceptance gwa
        LEFT JOIN gpx_employee ge ON ge.id = gwa.delivered_by
        LEFT JOIN gpx_warehouse gw ON gw.id = gwa.warehouse_id
        WHERE gwa.id = :transaction_no");
        $query->execute(
            array(
                "transaction_no" => $transaction_no,
            )
        );
        $result = $query->fetchAll();
        return $result;
    }

    public function getboxnumber($transaction_no)
    {
        $query = $this->connection->prepare("
        SELECT gwab.box_number as box_number
        FROM gpx_warehouse_acceptance_box_number gwab
        WHERE
        warehouse_acceptance_id = :transaction_no
        ");
        $query->execute(
            array(
                "transaction_no" => $transaction_no,
            )
        );
        $result = $query->fetchAll();
        return $result;
    }

    

}
