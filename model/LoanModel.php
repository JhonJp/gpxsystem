<?php
require_once __DIR__ . "/GenericModel.php";

class LoanModel extends GenericModel
{

    public function __construct($connection)
    {
        parent::__construct($connection);
    }

    //LIST    
    public function getlist()
    { 
        $query = $this->connection->prepare("SELECT gl.* , 
        CONCAT(term,' Months') as term,
        CONCAT(interest,'%') as interest,
        CONCAT(ge.firstname,' ',ge.lastname) as employee
        FROM gpx_loan gl
        JOIN gpx_employee ge ON gl.employee_id = ge.id
        ORDER BY createddate DESC");
        $query->execute();    
        $result = $query->fetchAll();
        $this->connection = null; 
        return $result;       
    }

    public function getloanbyid($id)
    { 
        $query = $this->connection->prepare("SELECT * 
        FROM gpx_loan 
        WHERE id = :id");
        $query->execute(array(
            "id" => $id
        ));    
        $result = $query->fetchAll();
        $this->connection = null; 
        return $result;       
    }

    public function gethistory($loan_no){

        $query = $this->connection->prepare("SELECT * FROM gpx_loan_payment WHERE loan_no = :loan_no");
        $query->execute(
            array(
                "loan_no" => $loan_no
            )
        );    
        $result = $query->fetchAll();
        $this->connection = null; 
        return $result;
    }



    public function updatePayment($id,$loan_no)
    {
        $query = $this->connection->prepare("UPDATE gpx_loan 
        SET status = 'Unpaid' 
        WHERE loan_no = :loan_no");
        $result = $query->execute(
            array(
                "loan_no" => $loan_no
            )
        );    

        $query = $this->connection->prepare("UPDATE gpx_loan_payment
        SET paid_date = NOW() , status = 'Paid'
        WHERE id = :id");
        $result = $query->execute(
            array(
                "id" => $id
            )
        );       
        
        ///UPDATE TO PAID
        $query = $this->connection->prepare("SELECT COUNT(*) FROM gpx_loan_payment       
        WHERE loan_no = :loan_no AND status = 'Unpaid'");
        $query->execute(
            array(
                "loan_no" => $loan_no
            )
        );    
        if($query->fetchColumn() == "0"){
            $query = $this->connection->prepare("UPDATE gpx_loan
            SET status = 'Paid'
            WHERE loan_no = :loan_no");
            $result = $query->execute(
                array(
                    "loan_no" => $loan_no
                )
            );   
        }
        ///

        
        return $result;
    }

    public function save($data)
    {
   
        $query = $this->connection->prepare("INSERT INTO gpx_loan(loan_no,employee_id,amount,term,interest,loan_date,approved_date,status,created_by)  
        VALUES(:loan_no,:employee_id,:amount,:term,:interest,:loan_date,:approved_date,:status,:created_by)");
        $result = $query->execute(
            array(
                "loan_no" => $data['loan_no'],
                "employee_id" => $data['employee_id'],
                "amount" => $data['amount'],
                "term" => $data['term'],
                "interest" => $data['interest'],
                "loan_date" => $data['loan_date'],
                "approved_date" => $data['approved_date'],
                "status" => $data['status'],
                "created_by" => $data['created_by'],
            )
        ); 

        if($data['status'] == 'Approved'){
            $due_date = date('Y-m-d');
            $amount = (((($data['amount'] * $data['interest'])/100) + ($data['amount']))/$data['term']);

            for($x = 1; $x <= $data['term']; $x++){            
                $due_date = date('Y-m-d',strtotime($due_date. ' + 30 days'));
                $query = $this->connection->prepare("INSERT INTO gpx_loan_payment(loan_no,due_date,amount,status) 
                VALUES(:loan_no,:due_date,:amount,:status)");
                $result = $query->execute(
                    array(
                        "loan_no" => $data['loan_no'],
                        "due_date" => $due_date,
                        "amount" => $amount,
                        "status" => 'Unpaid'
                    )
                );    
            }
        }
        
        return $result;
    }

    public function update($data,$id,$loanno)
    {
        $query = $this->connection->prepare("UPDATE gpx_loan
        SET approved_date = NOW() , status = :status ,
        amount = :amount, term = :term , interest = :interest
        WHERE id = :id");
        $result = $query->execute(
            array(
                "status" => $data['status'],
                "amount" => $data['amount'],
                "term" => $data['term'],
                "interest" => $data['interest'],
                "id" => $id
            )
        );   

        if($data['status'] == 'Approved'){
        $due_date = date('Y-m-d');
        $amount = (((($data['amount'] * $data['interest'])/100) + ($data['amount']))/$data['term']);

        for($x = 1; $x <= $data['term']; $x++){            
            $due_date = date('Y-m-d',strtotime($due_date. ' + 30 days'));
            $query = $this->connection->prepare("INSERT INTO gpx_loan_payment(loan_no,due_date,amount,status) 
            VALUES(:loan_no,:due_date,:amount,:status)");
            $result = $query->execute(
                array(
                    "loan_no" => $loanno,
                    "due_date" => $due_date,
                    "amount" => $amount,
                    "status" => 'Unpaid'
                )
            );    
        }
        }
     
        return $result;
    }
}
?>