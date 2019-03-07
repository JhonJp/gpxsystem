<?php
require_once __DIR__ . "/GenericModel.php";

class RoleModel extends GenericModel
{

    public function __construct($connection)
    {
        parent::__construct($connection);
    }

    //LIST    
    public function getlist()
    {        
        $query = $this->connection->prepare("SELECT * FROM gpx_role");
        $query->execute();    
        $result = $query->fetchAll();
        $this->connection = null; 
        return $result;
    }

    //get specific role
    public function getrole($id)
    {
        $query = $this->connection->prepare("SELECT gr.*, gra.* 
        FROM gpx_role gr
        LEFT JOIN gpx_role_access gra ON gra.role_id = gr.id WHERE gr.id = :id");
        $query->execute(array("id" => $id));    
        $result = $query->fetchAll();
        return $result;
    }

    //update role
    public function update($data,$id)
    {
        $query = $this->connection->prepare("
        UPDATE gpx_role
        SET name = :name,
        description = :description
        WHERE id = :id");
        $result = $query->execute(array(
            "id"=>$id,
            "name" => $data['name'],
            "description" => $data['description'],
        ));    
        return $result;
    }

    //update previlege
    public function updatePrev($data,$id)
    {
        $query = $this->connection->prepare("
        UPDATE gpx_role_access 
        SET 
        barcode_series=:barcode_series,
        reservation=:reservation,
        booking=:booking,
        warehouse_acceptance=:warehouse_acceptance,
        loading=:loading,
        in_transit=:in_transit,
        unloading=:unloading,
        delivery=:delivery,
        warehouse_inventory=:warehouse_inventory,
        box_releasing=:box_releasing,
        barcode_releasing=:barcode_releasing,
        trackntrace=:trackntrace,
        report_incident=:report_incident,
        customer=:customer,
        receiver=:receiver,
        consignments=:consignments,
        employee=:employee,
        user=:user,
        ticket=:tickets,
        chart_accounts=:chart_accounts,
        salary_compensation=:salary_compensation,
        allowance_disbursement=:allowance_disbursement,
        financial_liquidation=:financial_liquidation,
        loan=:loan,
        remittance=:remittance,
        expenses=:expenses,
        report_cargo=:report_cargo,
        report_sales=:report_sales,
        report_box_purchase=:report_box_purchase,
        report_box_disposed=:report_box_disposed,
        report_branch_incentives=:report_branch_incentives,
        report_exceptions=:report_exceptions,
        report_box_aging=:report_box_aging,
        createddate=:createddate,
        createdby=:createdby
        WHERE role_id = :role_id
        ");
        $result = $query->execute(array(
            "role_id"=>$id,
            "barcode_series"=>$data['barcode_series'],
            "reservation"=>$data['reservation'],
            "booking"=>$data['booking'],
            "warehouse_acceptance"=>$data['warehouse_acceptance'],
            "loading"=>$data['loading'],
            "in_transit"=>$data['in_transit'],
            "unloading"=>$data['unloading'],
            "delivery"=>$data['delivery'],
            "warehouse_inventory"=>$data['warehouse_inventory'],
            "box_releasing"=>$data['box_releasing'],
            "barcode_releasing"=>$data['barcode_releasing'],
            "trackntrace"=>$data['trackntrace'],
            "report_incident"=>$data['report_incident'],
            "customer"=>$data['customer'],
            "receiver"=>$data['receiver'],
            "consignments"=>$data['consignments'],
            "employee"=>$data['employee'],
            "user"=>$data['user'],
            "tickets"=>$data['tickets'],
            "chart_accounts"=>$data['chart_accounts'],
            "salary_compensation"=>$data['salary_compensation'],
            "allowance_disbursement"=>$data['allowance_disbursement'],
            "financial_liquidation"=>$data['financial_liquidation'],
            "loan"=>$data['loan'],
            "remittance"=>$data['remittance'],
            "expenses"=>$data['expenses'],
            "report_cargo"=>$data['report_cargo'],
            "report_sales"=>$data['report_sales'],
            "report_box_purchase"=>$data['report_box_purchase'],
            "report_box_disposed"=>$data['report_box_disposed'],
            "report_branch_incentives"=>$data['report_branch_incentives'],
            "report_exceptions"=>$data['report_exceptions'],
            "report_box_aging"=>$data['report_box_aging'],
            "createddate"=>$data['createddate'],
            "createdby"=>$data['createdby']
        ));    
        return $result;
    }

    public function insertdata($data)
    {
        $query = $this->connection->prepare("
        INSERT INTO gpx_role(name,description) VALUES (:name,:description)");
        $result = $query->execute(array(
            "name" => $data['name'],
            "description" => $data['description'],
        ));    
        return $result;
    }

    public function insertPrev($data,$id)
    {
        $query = $this->connection->prepare("
        INSERT INTO gpx_role_access(role_id, barcode_series, reservation, booking,
        warehouse_acceptance, loading, in_transit, unloading,
        delivery, warehouse_inventory, box_releasing, 
        barcode_releasing, trackntrace, report_incident, 
        customer, receiver, consignments, employee, user,
        ticket, chart_accounts, salary_compensation,
        allowance_disbursement, financial_liquidation, loan, 
        remittance, expenses, report_cargo, report_sales,
        report_box_purchase, report_box_disposed, report_branch_incentives, 
        report_exceptions, report_box_aging, createddate, createdby) 
        VALUES (:role_id,:barcode_series,:reservation,:booking,
        :warehouse_acceptance,:loading,:in_transit,:unloading,
        :delivery,:warehouse_inventory,:box_releasing,:barcode_releasing,
        :trackntrace,:report_incident,:customer,:receiver,:consignments,
        :employee,:user,:tickets,:chart_accounts,:salary_compensation,
        :allowance_disbursement,:financial_liquidation,:loan,
        :remittance,:expenses,:report_cargo,:report_sales,:report_box_purchase,
        :report_box_disposed,:report_branch_incentives,:report_exceptions,
        :report_box_aging,:createddate,:createdby)
        ");
        $result = $query->execute(array(
            "role_id"=>$id,
            "barcode_series"=>$data['barcode_series'],
            "reservation"=>$data['reservation'],
            "booking"=>$data['booking'],
            "warehouse_acceptance"=>$data['warehouse_acceptance'],
            "loading"=>$data['loading'],
            "in_transit"=>$data['in_transit'],
            "unloading"=>$data['unloading'],
            "delivery"=>$data['delivery'],
            "warehouse_inventory"=>$data['warehouse_inventory'],
            "box_releasing"=>$data['box_releasing'],
            "barcode_releasing"=>$data['barcode_releasing'],
            "trackntrace"=>$data['trackntrace'],
            "report_incident"=>$data['report_incident'],
            "customer"=>$data['customer'],
            "receiver"=>$data['receiver'],
            "consignments"=>$data['consignments'],
            "employee"=>$data['employee'],
            "user"=>$data['user'],
            "tickets"=>$data['tickets'],
            "chart_accounts"=>$data['chart_accounts'],
            "salary_compensation"=>$data['salary_compensation'],
            "allowance_disbursement"=>$data['allowance_disbursement'],
            "financial_liquidation"=>$data['financial_liquidation'],
            "loan"=>$data['loan'],
            "remittance"=>$data['remittance'],
            "expenses"=>$data['expenses'],
            "report_cargo"=>$data['report_cargo'],
            "report_sales"=>$data['report_sales'],
            "report_box_purchase"=>$data['report_box_purchase'],
            "report_box_disposed"=>$data['report_box_disposed'],
            "report_branch_incentives"=>$data['report_branch_incentives'],
            "report_exceptions"=>$data['report_exceptions'],
            "report_box_aging"=>$data['report_box_aging'],
            "createddate"=>$data['createddate'],
            "createdby"=>$data['createdby']
        ));    
        return $result;
    }

    public function getLastId(){
        $query = $this->connection->prepare("SELECT MAX(id) FROM gpx_role");
        $query->execute();
        $result = $query->fetchColumn(); 
        return $result;
    }

    //GET USER ID 
    public function getuserlogin(){
        $result = $_SESSION["logindetails"];
        return $result[0]['id'];
    }

}
?>