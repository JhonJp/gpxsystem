<?php
require_once __DIR__ . "/GenericModel.php";

class BoxRateModel extends GenericModel
{

    public function __construct($connection)
    {
        parent::__construct($connection);
    }

    //LIST    
    public function getlist()
    {       
        $query = $this->connection->prepare("SELECT gbr.id , gb.name as boxtype,
        cbm ,
        gsd.name as source, gsd2.name as destination , CONCAT(gr.currency_code, ' ', gbr.amount ) as amount
        FROM gpx_boxrate gbr
        JOIN gpx_boxtype gb ON gbr.boxtype_id = gb.id
        JOIN gpx_source_destination gsd ON gbr.source_id = gsd.id
        JOIN gpx_source_destination gsd2 ON gbr.destination_id = gsd2.id
        JOIN gpx_currency gr ON gbr.currency_id = gr.id
        ORDER BY createddate DESC
        ");
        $query->execute();
        $result = $query->fetchAll();
        $this->connection = null; 
        return $result; 
    }

    public function getboxratebyid($id)
    {       
        $query = $this->connection->prepare("SELECT * FROM gpx_boxrate WHERE id = :id");
        $query->execute(array("id"=>$id));
        $result = $query->fetchAll();
        $this->connection = null; 
        return $result; 
    }

    public function update($id,$data)
    {       
        $query = $this->connection->prepare("UPDATE gpx_boxrate 
        SET
        boxtype_id = :boxtype_id,
        cbm = :cbm,
        source_id = :source_id,
        destination_id = :destination_id,
        currency_id = :currency_id,
        amount = :amount
        WHERE id = :id");
        $result = $query->execute(array(
            "id"=>$id,
            "boxtype_id"=>$data['boxtype_id'],
            "cbm"=>$data['cbm'],
            "source_id"=>$data['source_id'],
            "destination_id"=>$data['destination_id'],
            "currency_id"=>$data['currency_id'],
            "amount"=>$data['amount']
        ));
        $this->connection = null; 
        return $result; 
    }

}
?>