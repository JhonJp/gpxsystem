<?php
require_once __DIR__ . "/GenericModel.php";

class IncidentReportModel extends GenericModel
{

    public function __construct($connection)
    {
        parent::__construct($connection);
    }

    //LIST    
    public function getlist()
    {        
        $query = $this->connection->prepare("SELECT * 
        FROM gpx_incident");
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }

    public function getincidentreportbyid($id){

        $query = $this->connection->prepare("SELECT * 
        FROM gpx_incident WHERE id = :id");
        $query->execute(array("id" => $id));
        $result = $query->fetchAll();
        return $result;

    }

    public function getimages($id)
    {
        $query = $this->connection->prepare("SELECT * 
        FROM gpx_incident_images WHERE incident_id = :id");
        $query->execute(array("id" => $id));
        $result = $query->fetchAll();
        return $result;
    }

    public function apisave($data)
    {
        try {
            $countdata = count($data['data']);

            for ($x = 0; $x < $countdata; $x++) {
               
                if (!($data['data'][$x]['incident_type'] == "") || ($data['data'][$x]['reason'] == "")) {

                    $query = $this->connection->prepare("INSERT INTO gpx_incident(
                   id, module,incident_type,box_number,reason,createddate,createdby)
                    VALUES (:id,:module,:incident_type,:box_number,:reason,:createddate,:createdby)");
                    $result = $query->execute(array(
                        "id" => $data['data'][$x]['id'],
                        "module" => $data['data'][$x]['module'],
                        "incident_type" => $data['data'][$x]['incident_type'],
                        "box_number" => $data['data'][$x]['box_number'],
                        "reason" => $data['data'][$x]['reason'],
                        "createddate" => $data['data'][$x]['created_date'],
                        "createdby" => $data['data'][$x]['createdby']
                    ));
                    
                    $countboxnumber = count($data['data'][$x]['images']);
                    for ($y = 0; $y < $countboxnumber; $y++) {

                        $query = $this->connection->prepare("INSERT INTO gpx_incident_images(
                            incident_id,images)
                            VALUES (:incident_id,:images)");
                        $result = $query->execute(array(
                            "incident_id" => $data['data'][$x]['id'],
                            "images" => $data['data'][$x]['images'][$y]['incident_image'],
                        ));
                    }
                    
                }
            }
        } catch (Exception $e) {
            $this->error_logs("Incident Report - apisave", $e->getmessage());
        }
    }

}
?>