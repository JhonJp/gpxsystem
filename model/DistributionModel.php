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
        WHERE driver_name = :driver_name AND distribution_type = 'Direct' 
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

                    $query = $this->connection->prepare("INSERT INTO gpx_distribution(
                    id,distribution_type,destination_name,truck_number,driver_name,remarks,createddate,createdby)
                    VALUES (:id,:distribution_type,:destination_name,:truck_number,:driver_name,:remarks,:createddate,:createdby)");
                    $result = $query->execute(array(
                        "id" => $data['data'][$x]['id'],
                        "distribution_type" => $data['data'][$x]['type'],
                        "destination_name" => $data['data'][$x]['name'],
                        "truck_number" => $data['data'][$x]['truck_no'],
                        "driver_name" => $data['data'][$x]['driver_name'],
                        "remarks" => $data['data'][$x]['remarks'],
                        "createddate" => $data['data'][$x]['created_date'],
                        "createdby" => $data['data'][$x]['created_by'],
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
                    }
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

}
