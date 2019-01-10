<?php
require_once __DIR__ . "/GenericModel.php";

class IndexModel extends GenericModel
{

  public function __construct($connection)
  {
    parent::__construct($connection);
  }

  public function delete($id, $table)
  {
    $query = $this->connection->prepare("DELETE FROM ".$table." WHERE id = :id");
    $result = $query->execute(array(
      "id" => $id
    ));    
    $this->connection = null;
    return $result;
  }

}
?>