<?php
require_once __DIR__ . "/GenericModel.php";

class SalarycompensationModel extends GenericModel
{

    public function __construct($connection)
    {
        parent::__construct($connection);
    }

    //LIST    
    public function getlist()
    {        
        $query = $this->connection->prepare("SELECT gsc.* , 
        CONCAT('PHP ',gsc.amount ) as amount , CONCAT(ge.firstname,' ',ge.lastname) as employee , gc.account_chart  
        FROM gpx_salary_compensation gsc
        JOIN gpx_employee ge ON ge.id = gsc.employee_id
        JOIN gpx_chartaccounts gc ON gc.id = gsc.chart_accounts");
        $query->execute();    
        $result = $query->fetchAll();
        $this->connection = null; 
        return $result;

    }

    public function getsalarybyid($id){
        $query = $this->connection->prepare("SELECT * FROM gpx_salary_compensation WHERE id = :id");
        $query->execute(array("id"=>$id));    
        $result = $query->fetchAll();
        $this->connection = null; 
        return $result;
    }

    

}
?>