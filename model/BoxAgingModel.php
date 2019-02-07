<?php
require_once  __DIR__ . "/GenericModel.php";

class BoxAgingModel extends GenericModel
{

    public function __construct($connection)
    {
		parent::__construct($connection);
    }

    public function getlist()
    {        
        $query = $this->connection->prepare("SELECT gu.*,gu.unload_date as unloaded_date,
        gubox.box_number,
        CONCAT(gc.firstname,' ', gc.lastname) as receiver,
        gsd.name as destination,
        (SELECT gtnt.status FROM gpx_trackntrace gtnt
        WHERE gtnt.transaction_no = gubox.box_number ORDER BY gtnt.id DESC LIMIT 1) as last_status
        FROM gpx_unloading gu
        JOIN gpx_unloading_box_number gubox ON gubox.unloading_id = gu.id
        JOIN gpx_booking_consignee_box gbc ON gbc.box_number = gubox.box_number
        JOIN gpx_source_destination gsd ON gsd.id = gbc.destination_id
        JOIN gpx_customer gc ON gc.account_no = gbc.consignee
        ORDER BY id");
        $query->execute();
        $result = $query->fetchAll();
        $this->connection = null; 
        return $result;
    }

}
?>