<?php
require_once  __DIR__ . "/GenericModel.php";

class BoxContentModel extends GenericModel
{

    public function __construct($connection)
    {
		parent::__construct($connection);
    }

    public function getdescriptions()
    {        
        $query = $this->connection->prepare("SELECT *
         FROM gpx_boxcontents ORDER BY id");
        $query->execute();
        $result = $query->fetchAll();
        $this->connection = null; 
        return $result;
    }

    public function getcontentbyid($id)
    {        
        $query = $this->connection->prepare("SELECT * FROM gpx_boxcontents WHERE id = :id");
        $query->execute(array("id"=>$id));
        $result = $query->fetchAll();
        return $result;
    }

    public function update($id,$data){
        $query = $this->connection->prepare("UPDATE gpx_boxcontents 
        SET 
        name = :name,
        description = :description
        WHERE id = :id");
        $result = $query->execute(array(
            "id"=>$id,
            "name"=>$data['name'],
            "description"=>$data['description'],
        ));
        $this->connection = null; 
        return $result;
    }


}
?>