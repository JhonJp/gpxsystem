<?php 

require_once "global.php";

class Database
{
  // DB Connect
  public function connect()
  {
    $connection = null;

    try {
      $connection = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_DATABASE, DB_USER, DB_PASS);
      $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
      echo 'Connection Error: ' . $e->getMessage();
    }

    return $connection;
  }
}