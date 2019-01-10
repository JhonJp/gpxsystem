<?php
require_once __DIR__ . "/GenericModel.php";

class FinancialLiquidationModel extends GenericModel
{

    public function __construct($connection)
    {
        parent::__construct($connection);
    }

    //LIST    
    public function getlist()
    {        
        $query = $this->connection->prepare("SELECT gsc.*, CONCAT(ge.firstname,' ',ge.lastname) as employee FROM 
        gpx_financial_liquidation gsc
        JOIN gpx_employee ge ON ge.id = gsc.employee_id");
        $query->execute();    
        $result = $query->fetchAll();
        $this->connection = null; 
        return $result;

    }

    public function getfinancialbyid($id)
    {        
        $query = $this->connection->prepare("SELECT * FROM 
        gpx_financial_liquidation WHERE id = :id");
        $query->execute(array("id"=>$id));    
        $result = $query->fetchAll();
        $this->connection = null; 
        return $result;

    }

    public function update($id,$data)
    {        
        $query = $this->connection->prepare("UPDATE
        gpx_financial_liquidation 
        SET 
        employee_id = :employee_id,
        liquidation_date = :liquidation_date,
        amount = :amount,
        description = :description
        WHERE id = :id");
        $result = $query->execute(array(
            "id"=>$id,
            "employee_id"=>$data['employee_id'],
            "liquidation_date"=>$data['liquidation_date'],
            "amount"=>$data['amount'],
            "description"=>$data['description'],
        ));            
        $this->connection = null; 
        return $result;

    }

    

}
?>