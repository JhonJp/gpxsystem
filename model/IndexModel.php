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

  //COUNT FOR ACCEPTANCE
  public function countForAcceptance($stat)
  {
    $query = $this->connection->prepare("SELECT COUNT(id) FROM gpx_booking WHERE booking_status = :stat");
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
      $query = $this->connection->prepare("SELECT 
      gpbook.transaction_no as transaction_no,
      gpbook.book_date as book_date,
      gpay.transaction_no,
      SUM(gpay.total_amount) as total_amount
       FROM gpx_booking gpbook
       JOIN gpx_payment gpay ON gpay.transaction_no = gpbook.transaction_no
       GROUP BY 
       gpbook.transaction_no
       ");
      $query->execute();
      $result = $query->fetchAll();
      return $result;        
    }

    
}


?>