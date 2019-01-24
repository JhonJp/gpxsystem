<?php
require_once __DIR__ . "/GenericModel.php";

class BarcodeSeriesModel extends GenericModel
{

    public function __construct($connection)
    {
        parent::__construct($connection);
    }

    public function getlist()
    {
        $query = $this->connection->prepare("
        SELECT box.name as boxtype,
        bar.id as id,
        CONCAT(bar.series_start,' - ',bar.series_end) AS series
        FROM gpx_barcode_series bar
        JOIN gpx_boxtype box ON box.id = bar.boxtype_id
        ORDER BY bar.id DESC
        ");
        $query->execute();
        $result = $query->fetchAll();
        $this->connection = null;
        return $result;
    }

    public function getData($id)
    {
        $query = $this->connection->prepare("
        SELECT 
        bar.id as id,
        bar.series_start as start,
        bar.series_end as end
        FROM gpx_barcode_series bar
        WHERE bar.id = :id
        ");
        $query->execute(array("id"=>$id));
        $result = $query->fetchAll();
        $this->connection = null;
        return $result;
    }

    public function saveSeries($boxtype,$start,$end,$qty)
    {
        $query = $this->connection->prepare("
        INSERT INTO gpx_barcode_series(boxtype_id,quantity,series_start,series_end)
        VALUES (:id,:q,:ss,:se)
        ");
        $query->execute(array(
            "id"=>$boxtype,
        "q"=>$qty,
        "ss"=>$start,
        "se"=>$end)
        );
    }
}

?>