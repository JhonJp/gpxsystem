<?php
require_once __DIR__ . "/GenericModel.php";

class RemittanceModel extends GenericModel
{

    public function __construct($connection)
    {
        parent::__construct($connection);
    }

    //LIST
    public function getlist()
    {
        $query = $this->connection->prepare("SELECT gr.* , gb.name as branch , gr.remitted_amount_oic as amount,
        gc.account_chart , CONCAT(ge.firstname,' ',ge.lastname) as remitted_by ,
        CONCAT(ge1.firstname,' ',ge1.lastname) as verified_by
        FROM gpx_remittance gr
        LEFT JOIN gpx_branch gb ON gr.branch_source = gb.id
        LEFT JOIN gpx_chartaccounts gc ON gc.id = gr.chart_accounts
        LEFT JOIN gpx_employee ge ON ge.id = gr.remitted_by
        LEFT JOIN gpx_employee ge1 ON ge1.id = gr.verified_by
        ORDER BY gr.createddate DESC
        ");
        $query->execute();
        $result = $query->fetchAll();
        $this->connection = null;
        return $result;
    }

    public function getremittancebyid($id)
    {
        $query = $this->connection->prepare("SELECT * FROM gpx_remittance WHERE id = :id");
        $query->execute(array("id" => $id));
        $result = $query->fetchAll();
        $this->connection = null;
        return $result;
    }

    public function update($data, $id)
    {
        $query = $this->connection->prepare("UPDATE gpx_remittance
        SET
        branch_source = :branch_source,
        chart_accounts = :chart_accounts,
        bank = :bank,
        remitted_by = :remitted_by,
        remitted_amount_sales_driver = :remitted_amount_sales_driver,
        remitted_amount_oic = :remitted_amount_oic,
        verified_by = :verified_by,
        description = :description     
        WHERE id = :id");
        $result = $query->execute(array(
            "id" => $id,
            "branch_source" => $data['branch_source'],
            "chart_accounts" => $data['chart_accounts'],
            "bank" => $data['bank'],
            "remitted_by" => $data['remitted_by'],
            "remitted_amount_sales_driver" => $data['remitted_amount_sales_driver'],
            "remitted_amount_oic" => $data['remitted_amount_oic'],
            "verified_by" => $data['verified_by'],
            "description" => $data['description']
        ));
        $this->connection = null;
        return $result;
    }

    public function apisave($data)
    {
        try {
            $countdata = count($data['data']);

            for ($x = 0; $x < $countdata; $x++) {
                    $sql = "INSERT INTO gpx_remittance(id,branch_source,chart_accounts,bank,remitted_by,remitted_amount_sales_driver,remitted_amount_oic,verified_by,description,documents,status,createddate,createdby,recordstatus)
                     VALUES (:id,:branch_source,:chart_accounts,:bank,:remitted_by,:remitted_amount_sales_driver,:remitted_amount_oic,:verified_by,:description,:documents,:status,:createddate,:createdby,:recordstatus)";
                    $query = $this->connection->prepare($sql);
                    $result = $query->execute(array(
                        "id" => $data['data'][$x]['id'],
                        "branch_source" => $data['data'][$x]['branch_source'],
                        "chart_accounts" => $data['data'][$x]['chart_accounts'],
                        "bank" => $data['data'][$x]['bank'],
                        "remitted_by" => $data['data'][$x]['remitted_by'],
                        "remitted_amount_sales_driver" => $data['data'][$x]['remitted_amount_sales_driver'],
                        "remitted_amount_oic" => $data['data'][$x]['remitted_amount_oic'],
                        "verified_by" => $data['data'][$x]['verified_by'],
                        "description" => $data['data'][$x]['description'],
                        "documents" => $data['data'][$x]['documents'],
                        "status" => $data['data'][$x]['status'],
                        "createddate" => $data['data'][$x]['createddate'],
                        "createdby" => $data['data'][$x]['createdby'],
                        "recordstatus" => $data['data'][$x]['recordstat'],
                    ));
                    
                //}
            }

        } catch (Exception $e) {
            $this->error_logs("Remittance - apisave", $e->getMessage());
        }
    }

    public function saveExpense($data)
    {
        try {
            $countExp = count($data['data']);

                for ($y = 0; $y < $countExp; $y++) {

                    $query2 = $this->connection->prepare("INSERT INTO gpx_expense(id,employee_id,amount,chart_accounts,description,status,due_date,approved_by,approved_date,documents)
                    VALUES(:id,:employee_id,:amount,:chart_accounts,:description,:status,:due_date,:approved_by,:approved_date,:documents)");
                    $result = $query2->execute(array(
                        "id" => $data['data'][$y]['id'],
                        "employee_id" => $data['data'][$y]['employee_id'],                            
                        "amount" => $data['data'][$y]['amount'],                            
                        "chart_accounts" => $data['data'][$y]['chart_accounts'],                            
                        "description" => $data['data'][$y]['description'],                            
                        "status" => $data['data'][$y]['status'],                            
                        "due_date" => $data['data'][$y]['due_date'],                            
                        "approved_by" => $data['data'][$y]['approved_by'],                            
                        "approved_date" => $data['data'][$y]['approved_date'],                            
                        "documents" => $data['data'][$y]['documents'],                            
                    ));
                }

        } catch (Exception $e) {
            $this->error_logs("Remittance - apisave", $e->getMessage());
        }
    }

    public function salesdriverremittance($data)
    {
        try {
            $countdata = count($data['data']);            
            for ($x = 0; $x < $countdata; $x++) {

                $query = $this->connection->prepare("INSERT INTO gpx_remittance_sales_driver(
                    id,salers_driver_id,oic,amount)
                    VALUES (:id,:salers_driver_id,:oic,:amount)");
                $result = $query->execute(array(
                    "id" => $data['data'][$x]['id'],
                    "salers_driver_id" => $data['data'][$x]['salers_driver_id'],
                    "oic" => $data['data'][$x]['oic'],
                    "amount" => $data['data'][$x]['amount'],
                ));
            }
        } catch (Exception $e) {
            $this->error_logs("Salesdriver Remittance - apisave", $e->getmessage());
        }
    }


    public function getRemittanceByOic($id)
    {
        $query = $this->connection->prepare("SELECT * FROM gpx_remittance_sales_driver WHERE oic = :id");
        $query->execute(array("id"=>$id));
        $result = $query->fetchAll();
        return $result;
    }

}
