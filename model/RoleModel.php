<?php
require_once __DIR__ . "/GenericModel.php";

class RoleModel extends GenericModel
{

    public function __construct($connection)
    {
        parent::__construct($connection);
    }

    //LIST    
    public function getlist()
    {        
        $query = $this->connection->prepare("SELECT * FROM gpx_role");
        $query->execute();    
        $result = $query->fetchAll();
        $this->connection = null; 
        return $result;
    }

}
?>