<?php
require_once __DIR__ . "/GenericModel.php";

class InTransitModel extends GenericModel
{

    public function __construct($connection)
    {
        parent::__construct($connection);
    }

    //LIST    
    public function getlist()
    {
        $query = $this->connection->prepare("SELECT * ,COUNT(box_number) as qty , 
        GROUP_CONCAT(box_number) as box_number FROM gpx_intransit        
        GROUP BY container_no");
        $query->execute();    
        $result = $query->fetchAll();
        return $result;       
    }

    public function getdetailsbyid($container_no){

        $query = $this->connection->prepare("SELECT * FROM gpx_loading where container_no = :container_no");
        $query->execute(array("container_no"=>$container_no));    
        $result = $query->fetchAll();
        return $result; 
    }

    public function gethistory($container_no){
        
        $query = $this->connection->prepare("SELECT * FROM gpx_intransit_history where container_no = :container_no");
        $query->execute(array("container_no"=>$container_no));    
        $result = $query->fetchAll();
        return $result; 
    }

    public function updateLoading($container_no,$eta,$etd){

        $query = $this->connection->prepare("INSERT INTO gpx_intransit_history(container_no,date_from,date_to)
        VALUES(:container_no,(SELECT eta FROM  gpx_loading WHERE container_no = :container_no LIMIT 1),:date_to)");
        $result = $query->execute(array(
            "container_no"=>$container_no,
            "date_to"=>$eta,
        )); 

        $query = $this->connection->prepare("UPDATE gpx_loading 
        SET eta = :eta , etd = :etd
        where container_no = :container_no");
        $result = $query->execute(array(
            "container_no"=>$container_no,
            "eta"=>$eta,
            "etd"=>$etd,
        ));   
        
        $query = $this->connection->prepare("UPDATE gpx_trackntrace 
        SET dateandtime = :dateandtime 
        where location = :location");
        $result = $query->execute(array(
            "location"=>$container_no,
            "dateandtime"=>$eta,
        ));  

        
        $query = $this->connection->prepare("UPDATE gpx_intransit 
        SET eta = :eta , etd = :etd
        where container_no = :container_no");
        $result = $query->execute(array(
            "container_no"=>$container_no,
            "eta"=>$eta,
            "etd"=>$etd,
        ));   
        
        
    }
    
}
?>