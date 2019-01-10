<?php
require_once  __DIR__ . "/GenericModel.php";

class BoxTypeModel extends GenericModel
{

    public function __construct($connection)
    {
		parent::__construct($connection);
    }

    public function getlist()
    {        
        $query = $this->connection->prepare("SELECT *,
         CONCAT(size_length,' x ' ,size_width , ' x ' , size_height) as l_w_h 
         FROM gpx_boxtype ORDER BY name");
        $query->execute();
        $result = $query->fetchAll();
        $this->connection = null; 
        return $result;
    }

    public function getboxtypebydid($id)
    {        
        $query = $this->connection->prepare("SELECT * FROM gpx_boxtype WHERE id = :id");
        $query->execute(array("id"=>$id));
        $result = $query->fetchAll();
        $this->connection = null; 
        return $result;
    }

    public function update($id,$data)
    {        
        $query = $this->connection->prepare("UPDATE gpx_boxtype 
        SET 
        name = :name,
        depositprice = :depositprice,        
        size_length = :size_length,
        size_width = :size_width,
        size_height = :size_height,
        description = :description
        WHERE id = :id");
        $result = $query->execute(array(
            "id"=>$id,
            "name"=>$data['name'],
            "depositprice"=>$data['depositprice'],
            "size_length"=>$data['size_length'],
            "size_width"=>$data['size_width'],
            "size_height"=>$data['size_height'],
            "description"=>$data['description']
        ));
        $this->connection = null; 
        return $result;
    }


}
?>