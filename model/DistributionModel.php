<?php
require_once __DIR__ . "/GenericModel.php";

class DistributionModel extends GenericModel
{

    public function __construct($connection)
    {
        parent::__construct($connection);
    }

    public function getlist()
    {
        $query = $this->connection->prepare("SELECT gd.* , gd.distribution_type as type,
        gd.destination_name as destination
        , COUNT(gdbn.box_number) as qty ,
        GROUP_CONCAT(gdbn.box_number) as box_number
        FROM gpx_distribution gd
        LEFT JOIN gpx_distribution_box_number gdbn ON gd.id = gdbn.distibution_id
        LEFT JOIN gpx_employee gemp ON gemp.id = gdbn.distibution_id
        WHERE gd.id LIKE '%GPDIST-%'
        GROUP BY gd.id
        ");
        $query->execute();
        $result = $query->fetchAll();
        $this->connection = null;
        return $result;
    }

    public function getdetails($id){
        $query = $this->connection->prepare("SELECT gd.* , CONCAT(ge.firstname, ' ', ge.lastname) as createdby FROM gpx_distribution gd 
        LEFT JOIN gpx_employee ge ON gd.createdby = ge.id
        WHERE gd.id = :id");
        $query->execute(array("id"=>$id));
        $result = $query->fetchAll();
        return $result;
    }

    public function getdistributionbydriver($name = null)
    {
        $query = $this->connection->prepare("SELECT gd.* , gd.distribution_type as type,
        gd.destination_name as destination
        , COUNT(gdbn.box_number) as qty ,
        GROUP_CONCAT(gdbn.box_number) as box_number
        FROM gpx_distribution gd
        LEFT JOIN gpx_distribution_box_number gdbn ON gd.id = gdbn.distibution_id
        WHERE driver_name = :driver_name AND distribution_type = 'Direct' AND gd.status = '1'  
        GROUP BY gd.id
        ");
        $query->execute(array("driver_name" => $name));
        $result = $query->fetchAll();
        return $result;
    }

    public function getboxnumber($id){

        $query = $this->connection->prepare("SELECT gb.name as boxtype , gdbn.box_number 
        FROM gpx_distribution_box_number gdbn
        LEFT JOIN gpx_boxtype gb ON gb.id = gdbn.boxtype_id
        WHERE gdbn.distibution_id = :id");
        $query->execute(array("id"=>$id));
        $result = $query->fetchAll();
        return $result;
    }

    public function apisave($data)
    {

        try {
            $countdata = count($data['data']);
            for ($x = 0; $x < $countdata; $x++) {
                $check = $this->checkdata($data['data'][$x]['id']);
                if (count($check) == 0) {
                    $dist_type = $data['data'][$x]['type'];
                    $query = $this->connection->prepare("INSERT INTO gpx_distribution(
                    id,distribution_type,mode_of_shipment,destination_name,truck_number,driver_name,remarks,eta,createddate,createdby,status)
                    VALUES (:id,:distribution_type,:mode_of_shipment,:destination_name,:truck_number,:driver_name,:remarks,:eta,:createddate,:createdby,:status)");
                    $result = $query->execute(array(
                        "id" => $data['data'][$x]['id'],
                        "distribution_type" => $data['data'][$x]['type'],
                        "mode_of_shipment" => $data['data'][$x]['mode_of_shipment'],
                        "destination_name" => $data['data'][$x]['name'],
                        "truck_number" => $data['data'][$x]['truck_no'],
                        "driver_name" => $data['data'][$x]['driver_name'],
                        "remarks" => $data['data'][$x]['remarks'],
                        "eta" => $data['data'][$x]['eta'],
                        "createddate" => $data['data'][$x]['created_date'],
                        "createdby" => $data['data'][$x]['created_by'],
                        "status" => $data['data'][$x]['acceptance_status'],
                    ));

                    $countboxnumber = count($data['data'][$x]['distribution_box']);

                    for ($y = 0; $y < $countboxnumber; $y++) {

                        $query2 = $this->connection->prepare("INSERT INTO gpx_distribution_box_number(
                        box_number,distibution_id,warehouse_inventory_id,boxtype_id)
                        VALUES (:box_number,:distibution_id,:warehouse_inventory_id,:boxtype_id)");
                        $result = $query2->execute(array(
                            "box_number" => $data['data'][$x]['distribution_box'][$y]['boxnumber'],
                            "distibution_id" => $data['data'][$x]['distribution_box'][$y]['distribution_id'],
                            "warehouse_inventory_id" => $data['data'][$x]['distribution_box'][$y]['inventory_id'],
                            "boxtype_id" => $data['data'][$x]['distribution_box'][$y]['boxtype_id'],
                        ));

                        if(($dist_type == "Partner - Hub") 
                         || ($dist_type == "Partner - Area")){

                            ///////////IN TRANSIT LOCAL/////////
                            $this->save_instransit_local(
                                $data['data'][$x]['truck_no'],
                                $data['data'][$x]['name'],
                                $data['data'][$x]['created_date'],
                                $data['data'][$x]['distribution_box'][$y]['boxnumber']
                            );
                            
                           //////////TRACK N TRACE LOGS/////////
                            $logs = array(
                                "transaction_no" => $data['data'][$x]['distribution_box'][$y]['boxnumber'],
                                "status" => "In Transit",
                                "dateandtime" => $data['data'][$x]['created_date'],
                                "activity" => "In Transit ".$data['data'][$x]['mode_of_shipment'],
                                "location" => $this->getlocationemployeebyid($data['data'][$x]['created_by']),
                                "qty" => "1",
                                "details" => "In Transit ".$data['data'][$x]['mode_of_shipment'].", ".", ETA ".date_format($data['data'][$x]['eta'], "d/m/Y")
                            );
                            $this->savetrackntrace($logs); 
                        }else if($dist_type == "Direct") {

                            ///////////IN TRANSIT LOCAL/////////
                            $this->save_instransit_local(
                                $data['data'][$x]['truck_no'],
                                $data['data'][$x]['name'],
                                $data['data'][$x]['created_date'],
                                $data['data'][$x]['distribution_box'][$y]['boxnumber']
                            );

                            //////////TRACK N TRACE LOGS/////////
                            $logs = array(
                                "transaction_no" => $data['data'][$x]['distribution_box'][$y]['boxnumber'],
                                "status" => "For Delivery",
                                "dateandtime" => $data['data'][$x]['created_date'],
                                "activity" => "For Delivery",
                                "location" => $this->getlocationemployeebyid($data['data'][$x]['created_by']),
                                "qty" => "1",
                                "details" => "Truck number is ".$data['data'][$x]['truck_no']
                            );
                            $this->savetrackntrace($logs); 
                        }
                        
                    }

                    
                }else{
                    $this->updateDistStat($data['data'][$x]['id']);
                }
            }

        } catch (Exception $e) {
            $this->error_logs("Distribution - apisave", $e->getMessage());
        }

    }

    public function checkdata($id)
    {
        $query = $this->connection->prepare("SELECT * FROM gpx_distribution WHERE id = :id");
        $query->execute(array("id" => $id));
        $result = $query->fetchAll();
        return $result;
    }

    public function save_instransit_local($trucknumber, $destination, $date, $box_number)
    {
        $query = $this->connection->prepare("
        INSERT INTO gpx_partnerportal_intransit(truck_no,destination_name,createddate,box_number) 
        VALUES (:truck,:destination,:createddate,:boxnumber)");
        $result = $query->execute(array(
            "truck" => $trucknumber,
            "destination" => $destination,
            "createddate" => $date,
            "boxnumber" => $box_number,
        ));

    }

    public function updateDistStat($id)
    {
        $query = $this->connection->prepare("
        UPDATE gpx_distribution SET status='1' WHERE id = :id");
        $result = $query->execute(array(
            "id" => $id,
        ));
    }

}
