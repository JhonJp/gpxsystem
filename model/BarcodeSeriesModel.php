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
        CONCAT(gemp.firstname,' ',gemp.lastname) as created_by,
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

    public function getBarcodesDist()
    {
        $query = $this->connection->prepare("
        SELECT 
        gbd.transaction_no,
        CONCAT(ge.firstname,' ', ge.lastname) as driver,
        CONCAT(gee.firstname,' ', gee.lastname) as created_by,
        gb.name as branch
        FROM gpx_barcode_distribution gbd
        LEFT JOIN gpx_employee ge ON ge.id = gbd.driver_id 
        LEFT JOIN gpx_employee gee ON gee.id = gbd.createdby 
        LEFT JOIN gpx_branch gb ON gb.id = ge.branch
        ");
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }

    public function getBarcodes($id)
    {
        $query = $this->connection->prepare("
        SELECT * FROM gpx_barcode_distribution_number gbdn
        WHERE gbdn.transaction_no = :trans
        ");
        $query->execute(array("trans"=>$id,));
        $result = $query->fetchAll();
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

    public function apisave($data)
    {

        try {
            $countdata = count($data['data']);
            for ($x = 0; $x < $countdata; $x++) {
                $check = $this->checkdata($data['data'][$x]['transaction_no']);
                if (count($check) == 0) {
                    //$dist_type = $data['data'][$x]['type'];
                    $query = $this->connection->prepare("INSERT INTO gpx_barcode_distribution(transaction_no,driver_id,status,createddate,createdby) 
                    VALUES (:trans,:driver_id,:status,:crdate,:by)");
                    $result = $query->execute(array(
                        "trans" => $data['data'][$x]['transaction_no'],
                        "driver_id" => $data['data'][$x]['driver_id'],
                        "status" => "1",
                        "crdate" => $data['data'][$x]['createddate'],
                        "by" => $data['data'][$x]['createdby'],
                    ));

                    $countboxnumber = count($data['data'][$x]['barcodes']);

                    for ($y = 0; $y < $countboxnumber; $y++) {

                        $query2 = $this->connection->prepare("INSERT INTO gpx_barcode_distribution_number
                        (transaction_no,boxnumber,status)
                        VALUES (:trans,:boxnumber,:status)");
                        $result = $query2->execute(array(
                            "trans" => $data['data'][$x]['barcodes'][$y]['transaction_no'],
                            "boxnumber" => $data['data'][$x]['barcodes'][$y]['box_number'],
                            "status" => $data['data'][$x]['barcodes'][$y]['status'],
                        ));
                        
                    }

                }
            }

        } catch (Exception $e) {
            $this->error_logs("Barcode Series - apisave", $e->getMessage());
        }

    }

    public function checkdata($id)
    {
        $query = $this->connection->prepare("SELECT * FROM gpx_barcode_distribution WHERE transaction_no = :id");
        $query->execute(array("id" => $id));
        $result = $query->fetchAll();
        return $result;
    }



}

?>