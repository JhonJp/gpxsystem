<?php
require_once __DIR__ . "/GenericModel.php";

class AllowancedisbursementModel extends GenericModel
{

    public function __construct($connection)
    {
        parent::__construct($connection);
    }

    //LIST    
    public function getlist()
    {        
        $query = $this->connection->prepare("SELECT gsc.*, CONCAT(ge.firstname,' ',ge.lastname) 
        as employee , gc.account_chart  , CONCAT('PHP ',gsc.amount) as amount
        FROM 
        gpx_allowance_disbursement gsc
        JOIN gpx_employee ge ON ge.id = gsc.employee_id
        JOIN gpx_chartaccounts gc ON gc.id = gsc.chart_accounts
        ORDER BY createddate desc");
        $query->execute();    
        $result = $query->fetchAll();
        $this->connection = null; 
        return $result;

    }

    public function getallowancebyid($id){
        $query = $this->connection->prepare("SELECT * FROM 
        gpx_allowance_disbursement WHERE id = :id");
        $query->execute(array("id"=>$id));    
        $result = $query->fetchAll();
        $this->connection = null; 
        return $result;
    }
    
    public function update($id,$data){
        $query = $this->connection->prepare("UPDATE gpx_allowance_disbursement 
        SET 
        employee_id = :employee_id,
        chart_accounts = :chart_accounts,
        disbursement_date = :disbursement_date,
        amount = :amount,
        description = :description
        
        WHERE id = :id");
        $result = $query->execute(array(
            "id" => $id,
            "employee_id" => $data['employee_id'],
            "chart_accounts" => $data['chart_accounts'],
            "disbursement_date" => $data['disbursement_date'],
            "amount" => $data['amount'],
            "description" => $data['description']
        ));    
        $this->connection = null; 
        return $result;
    }
}
?>