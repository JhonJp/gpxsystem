<?php
require_once __DIR__ . "/GenericModel.php";

class BranchModel extends GenericModel
{

    public function __construct($connection)
    {
        parent::__construct($connection);
    }

    //LIST    
    public function getlist()
    {        
        $query = $this->connection->prepare("SELECT * FROM gpx_branch ORDER BY name");
        $query->execute();
        $result = $query->fetchAll();
        $this->connection = null; 
        return $result;
    }

    public function getbranchbyid($id)
    {        
        $query = $this->connection->prepare("SELECT * FROM gpx_branch WHERE id = :id");
        $query->execute(array("id"=>$id));
        $result = $query->fetchAll();
        $this->connection = null; 
        return $result;
    }

    public function update($id,$data)
    {        
        $query = $this->connection->prepare("UPDATE gpx_branch 
        SET 
        name = :name,
        address = :address,        
        type = :type

        WHERE id = :id");
        $result = $query->execute(array(
            "id"=>$id,
            "name"=>$data['name'],
            "address"=>$data['address'],
            "type"=>$data['type']
        ));
        $this->connection = null; 
        return $result;
    }


}
?>