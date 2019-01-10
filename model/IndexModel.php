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

  //COUNT PENDING RESERVATION
  public function countPendingReservation($stat)
  {
    $query = $this->connection->prepare("SELECT COUNT(id) FROM gpx_reservation WHERE status = :stat");
    $query->execute(array(
      "stat" => $stat
    ));
    $result = $query->fetchColumn();
    return $result;
  }

  //COUNT BOOKING
  public function countBooking()
  {
    $query = $this->connection->prepare("SELECT COUNT(id) FROM gpx_booking");
    $query->execute();
    $result = $query->fetchColumn();
    return $result;
  }

  //COUNT BOOKING
  public function countCustomers($type)
  {
    $query = $this->connection->prepare("SELECT COUNT(id) FROM gpx_customer WHERE type = :ty");
    $query->execute(array("ty"=>$type));
    $result = $query->fetchColumn();
    return $result;
  }

  //GET BOOKING LIST
  public function getBookinglist()
    {
      $query = $this->connection->prepare("SELECT transaction_no,book_date FROM gpx_booking");
      $query->execute();
      $result = $query->fetchAll();
      return $result;        
    }
    
}
?>