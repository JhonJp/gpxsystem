<?php
require_once __DIR__ . "/GenericModel.php";

class LoadingModel extends GenericModel
{

    public function __construct($connection)
    {
        parent::__construct($connection);
    }

    public function getlist()
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

    public function apisave($data)
    {
        try {
            $countdata = count($data['data']);

            for ($x = 0; $x < $countdata; $x++) {

                $id = $data['data'][$x]['id'];

                $check = $this->checkdata($id);

                if (count($check) == 0) {

                    $query = $this->connection->prepare("INSERT INTO gpx_loading(
                    id,loaded_date,shipping_name,container_no,eta,etd,createdby)
                    VALUES (:id,:loaded_date,:shipping_name,:container_no,:eta,:etd,:createdby)");
                    $result = $query->execute(array(
                        "id" => $data['data'][$x]['id'],
                        "loaded_date" => $data['data'][$x]['load_date'],
                        "shipping_name" => $data['data'][$x]['load_shipper'],
                        "container_no" => $data['data'][$x]['load_container'],
                        "eta" => $data['data'][$x]['load_eta'],
                        "etd" => $data['data'][$x]['load_etd'],
                        "createdby" => $data['data'][$x]['createdby'],
                    ));

                    $countboxnumber = count($data['data'][$x]['loading_boxes']);
                    for ($y = 0; $y < $countboxnumber; $y++) {

                        $query = $this->connection->prepare("INSERT INTO gpx_loading_box_number(
                            box_number,loading_id)
                            VALUES (:box_number,:warehouse_acceptance_id)");
                        $result = $query->execute(array(
                            "box_number" => $data['data'][$x]['loading_boxes'][$y]['box_num'],
                            "warehouse_acceptance_id" => $data['data'][$x]['loading_boxes'][$y]['transaction_no'],
                        ));

                        //////////UPDATE BOOKING STATUS//////////
                        $this->updateBookingStatus($data['data'][$x]['loading_boxes'][0]['box_num'], "3");

                        ///////////IN TRANSIT/////////
                        $this->save_instransit(
                            $data['data'][$x]['load_container'],
                            $data['data'][$x]['load_eta'],
                            $data['data'][$x]['load_etd'],
                            $data['data'][$x]['loading_boxes'][$y]['box_num']
                        );

                        ///////////TRACK N TRACE LOGS/////////
                        $logs = array(
                            "transaction_no" => $this->gettransactionno($data['data'][$x]['loading_boxes'][$y]['box_num']),
                            "status" => "Loaded",
                            "dateandtime" => $data['data'][$x]['load_date'],
                            "activity" => "Loaded In Container",
                            "location" => $this->getlocationemployeebyid($data['data'][$x]['createdby']),
                            "qty" => $countboxnumber,
                            "details" => "Container number is ".$data['data'][$x]['load_container'].". Shipper name is ".$data['data'][$x]['load_shipper'],
                        );
                        $this->savetrackntrace($logs);

                        ///////////TRACK N TRACE LOGS/////////
                        $logss = array(
                            "transaction_no" => $this->gettransactionno($data['data'][$x]['loading_boxes'][0]['box_num']),
                            "status" => "In-Transit",
                            "dateandtime" => $data['data'][0]['load_etd'],
                            "activity" => "In-Transit International",
                            "location" =>  $data['data'][$x]['load_shipper'],
                            "qty" => $countboxnumber,
                            "details" => "Estimated date of arrival ".$data['data'][$x]['load_eta'],
                        );
                        $this->savetrackntrace($logss);

                        ///////////TRACK N TRACE LOGS/////////


                    }

                    //////////UPDATE BOOKING STATUS//////////
                    $this->updateBookingStatus($data['data'][$x]['loading_boxes'][0]['box_num'], "7");

                    
                }
            }
        } catch (Exception $e) {
            $this->error_logs("Loading - apisave", $e->getmessage());
        }
    }

    public function checkdata($id)
    {
        $query = $this->connection->prepare("SELECT * FROM gpx_loading WHERE id = :id");
        $query->execute(array("id" => $id));
        $result = $query->fetchAll();
        return $result;
    }

    public function save_instransit($container_no, $eta, $etd, $box_number)
    {
        $query = $this->connection->prepare("INSERT INTO gpx_intransit(container_no,eta,etd,box_number)
            VALUES (:container_no,:eta,:etd,:box_number)");
        $result = $query->execute(array(
            "container_no" => $container_no,
            "eta" => $eta,
            "etd" => $etd,
            "box_number" => $box_number,
        ));

    }

}
