<?php
require_once __DIR__ . "/GenericModel.php";

class TicketModel extends GenericModel
{

    public function __construct($connection)
    {
        parent::__construct($connection);
    }

    //LIST    
    public function getlist()
    {  
        $query = $this->connection->prepare("SELECT gt.* , CONCAT(ge.firstname,' ',ge.lastname) as assigned_to
        , gty.name as ticket_type , gts.name as status,
         CONCAT(gc.firstname,' ', gc.lastname) as customer_name
        FROM gpx_tickets gt 
        JOIN gpx_tickets_type gty ON gty.id = gt.ticket_type
        JOIN gpx_tickets_status gts ON gts.id = gt.status
        JOIN gpx_customer gc ON gt.account_no = gc.account_no
        JOIN gpx_employee ge ON ge.id = gt.assigned_to
        ");
        $query->execute();    
        $result = $query->fetchAll();
        $this->connection = null; 
        return $result;     
    }

    public function getticketbyid($id)
    {   
        $query = $this->connection->prepare("SELECT * FROM gpx_tickets WHERE id = :id");
        $query->execute(array("id"=>$id));    
        $result = $query->fetchAll();
        $this->connection = null; 
        return $result;     
    }

    public function update($id,$data){
        $query = $this->connection->prepare("UPDATE gpx_tickets 
        SET ticket_type = :ticket_type ,
        status = :status,
        assigned_to = :assigned_to,
        account_no = :account_no,
        description = :description
        WHERE id = :id");
        $result = $query->execute(array(
            "id" => $id,
            "ticket_type"=> $data['ticket_type'],
            "account_no"=> $data['account_no'],
            "status"=> $data['status'],
            "assigned_to"=> $data['assigned_to'],
            "description"=> $data['description'],
        ));    
        $this->connection = null; 
        return $result; 
    }


}
?>