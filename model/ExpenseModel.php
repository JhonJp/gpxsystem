<?php
require_once __DIR__ . "/GenericModel.php";

class ExpenseModel extends GenericModel
{

    public function __construct($connection)
    {
        parent::__construct($connection);
    }

    //LIST    
    public function getlist()
    {      
        $query = $this->connection->prepare("SELECT gex.* , gc.account_chart,
        CONCAT(ge.firstname,' ',ge.lastname) as employee_name,
        CONCAT(ge1.firstname,' ',ge1.lastname) as approved_by
        FROM gpx_expense gex
        JOIN gpx_employee ge ON gex.employee_id = ge.id 
        LEFT JOIN gpx_employee ge1 ON gex.approved_by = ge1.id 
        LEFT JOIN gpx_chartaccounts gc ON gex.chart_accounts = gc.id");
        $query->execute();    
        $result = $query->fetchAll();
        $this->connection = null; 
        return $result;  
    }

    public function getexpensebyid($id)
    {   
        $query = $this->connection->prepare("SELECT * FROM gpx_expense WHERE id = :id");
        $query->execute(array("id"=>$id));    
        $result = $query->fetchAll();
        $this->connection = null; 
        return $result;     
    }

    public function update($id,$data){
        $query = $this->connection->prepare("UPDATE gpx_expense 
        SET employee_id = :employee_id,
        amount = :amount ,
        chart_accounts = :chart_accounts,
        description = :description,
        status = :status    ,
        due_date = :due_date,
        approved_by = :approved_by,
        approved_date = :approved_date,
        documents = :documents
        WHERE id = :id");
        $result = $query->execute(array(
            "id" => $id,
            "employee_id"=> $data['employee_id'],
            "amount"=> $data['amount'],
            "chart_accounts"=> $data['chart_accounts'],
            "description"=> $data['description'],
            "status"=> $data['status'],
            "due_date"=> $data['due_date'],
            "approved_by"=> $data['approved_by'],
            "approved_date"=> $data['approved_date'],
            "documents"=> $data['documents']
        ));    
        $this->connection = null; 
        return $result; 
    }

}
?>