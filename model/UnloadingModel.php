<?php
require_once __DIR__ . "/GenericModel.php";

class UnloadingModel extends GenericModel
{

    public function __construct($connection)
    {
        parent::__construct($connection);
    }

    public function getlist()
    {
        $query = $this->connection->prepare("SELECT gu.*, gu.arrival_time as arrival_time,
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

    public function apisave($data)
    {
        try {
            $countdata = count($data['data']);

            for ($x = 0; $x < $countdata; $x++) {

                $id = $data['data'][$x]['id'];

                $check = $this->checkdata($id);

                if (count($check) == 0) {

                    $query = $this->connection->prepare("INSERT INTO gpx_unloading(
                    id,unload_date,container_no,forwarder_name,arrival_time,time_start,time_end,plate_no,driver_name,createdby)
                    VALUES (:id,:unload_date,:container_no,:forwarder_name,:arrival_time,:time_start,:time_end,:plate,:drivern,:createdby)");
                    $result = $query->execute(array(
                        "id" => $data['data'][$x]['id'],
                        "unload_date" => $data['data'][$x]['unload_date'],
                        "container_no" => $data['data'][$x]['unload_container'],
                        "forwarder_name" => $data['data'][$x]['unload_shipper'],
                        "arrival_time" => $data['data'][$x]['unload_eta'],
                        "time_start" => $data['data'][$x]['time_start'],
                        "time_end" => $data['data'][$x]['time_end'],
                        "plate" => $data['data'][$x]['plate_no'],
                        "drivern" => $data['data'][$x]['driver_name'],
                        "createdby" => $data['data'][$x]['createdby'],
                    ));

                    $countboxnumber = count($data['data'][$x]['unloading_boxes']);
                    for ($y = 0; $y < $countboxnumber; $y++) {

                        $query = $this->connection->prepare("INSERT INTO gpx_unloading_box_number(
                            box_number,unloading_id)
                            VALUES (:box_number,:unloading_id)");
                        $result = $query->execute(array(
                            "box_number" => $data['data'][$x]['unloading_boxes'][$y]['box_num'],
                            "unloading_id" => $data['data'][$x]['unloading_boxes'][$y]['transaction_no'],
                        ));

                        //////////TRACK N TRACE LOGS/////////
                        $logs = array(
                            "transaction_no" => $data['data'][$x]['unloading_boxes'][$y]['box_num'],
                            "status" => "Unloaded",
                            "dateandtime" => $data['data'][$x]['unload_date'],
                            "activity" => "Unloaded in Container",
                            "location" => $this->getlocationemployeebyid($data['data'][$x]['createdby']),
                            "qty" => "1",
                            "details" => "Box has been unloaded and arrived on ".date_format($data['data'][$x]['unload_eta'],"d/m/Y"),
                        );
                        $this->savetrackntrace($logs);

                        //////////UPDATE BOOKING STATUS//////////
                        $this->updateBookingStatus($data['data'][$x]['unloading_boxes'][$y]['box_num'],"4");
                    }

                    $countimagebox = count($data['data'][$x]['unloading_boxes_image']);
                    for ($y = 0; $y < $countboxnumber; $y++) {
                        $query = $this->connection->prepare("INSERT INTO gpx_unloading_boximage(transaction_no,box_number,image) 
                        VALUES (:transaction_no,:box_number,:image)");
                        $result = $query->execute(array(
                            "transaction_no" => $data['data'][$x]['unloading_boxes_image'][$y]['transaction_number'],
                            "box_number" => $data['data'][$x]['unloading_boxes_image'][$y]['boxnumber'],
                            "image" => $data['data'][$x]['unloading_boxes_image'][$y]['image'],
                        ));
                    }

                }
            }
        } catch (Exception $e) {
            $this->error_logs("Unloading - apisave", $e->getmessage());
        }
    }

    public function checkdata($id)
    {
        $query = $this->connection->prepare("SELECT * FROM gpx_unloading WHERE id = :id");
        $query->execute(array("id" => $id));
        $result = $query->fetchAll();
        return $result;
    }
}
