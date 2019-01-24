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
        SELECT bar.createddate as date,
        bar.id,
        bar.quantity as quantity,
        CONCAT(gemp.firstname,' ',gemp.lastname) as user,
        gb.name as branch,
        CONCAT(bar.series_start,' - ', bar.series_end) as series
        FROM gpx_barcode_series bar
        LEFT JOIN gpx_employee gemp ON gemp.id = bar.createdby
        LEFT JOIN gpx_branch gb ON gb.id = gemp.branch
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

    public function saveSeries($data)
    {
        try {
            $quantity = $data->data[0]->quantity;
            $series_start = $data->data[0]->series_start;
            $series_end = $data->data[0]->series_end;
            $by = $this->getuserlogin();
            $createddate = date('Y/m/d H:i:s');
            if($series_start == null ){
                $series_start = 0;
            }
            $query = $this->connection->prepare("
            INSERT INTO gpx_barcode_series(quantity,series_start,series_end,createdby,createddate) 
            VALUES (:quantity,:start,:end,:by,:date)
            ");
            $query->execute(array(
                "quantity"=>$quantity,
                "start"=>$series_start,
                "end"=>$series_end,
                "by"=>$by,
                "date"=>$createddate
                )
            );
            
        } catch (Exception $e) {
            $this->error_logs("Barcodeseries - save", $e->getMessage());
        }
    }

    public function getMax()
    {
        $query = $this->connection->prepare("
        SELECT COUNT(id)
        FROM gpx_barcode_series
        ");
        $query->execute();
        $result = $query->fetchColumn();
        return $result;
    }

    public function getEnd($max)
    {
        $query = $this->connection->prepare("
        SELECT gs.series_end
        FROM gpx_barcode_series gs WHERE gs.id = :id
        ");
        $query->execute(array("id"=>$max));
        $result = $query->fetchColumn();
        return $result;
    }
}

?>