<?php

class RoleController extends GenericController
{
    public function __construct()
    {
        parent::__construct();
        require_once __DIR__ . "/../model/RoleModel.php";
    }

    //LIST
    public function list()
    {
        $model = new RoleModel($this->connection);
        $list = $model->getlist();        
        $columns = array("name","description");
        echo $this->twig->render('_generic_component/list.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,
            "list" => $list,            
            "columns" => $columns,                     
        ));        
    }

    public function edit()
    {
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        $model = new RoleModel($this->connection);
        $result = null;
        $rolemax = null; 
        if (isset($id)) {
            $result = $model->getrole($id);
        }
        
        $rolemax = $model->getLastId();
        echo $this->twig->render('user-management/roles/edit.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,
            "result" => $result,
            "id" => $id,
            "rolemax" => $rolemax
        ));
    }

    public function save()
    {
        $data = json_decode(utf8_encode($_POST['data'])); 
            $id = $data->data[0]->id;
            $name = $data->data[0]->name;
            $desc = $data->data[0]->description;
            $maxrole = $data->data[0]->maxrole;
            //cargo
            $barcode_series = $data->data[0]->barcode_series;
            $reservation = $data->data[0]->reservation;
            $booking = $data->data[0]->booking;
            $warehouse_acceptance = $data->data[0]->warehouse_acceptance;
            $loading = $data->data[0]->loading;
            $in_transit = $data->data[0]->in_transit;
            $unloading = $data->data[0]->unloading;
            $delivery = $data->data[0]->delivery;
            $warehouse_inventory = $data->data[0]->warehouse_inventory;
            $box_releasing = $data->data[0]->box_releasing;
            $barcode_releasing = $data->data[0]->barcode_releasing;
            $trackntrace = $data->data[0]->trackntrace;
            //customer
            $customer = $data->data[0]->customer;
            $receiver = $data->data[0]->receiver;
            $consignment = $data->data[0]->consignment;
            //finance
            $chart_accounts = $data->data[0]->chart_accounts;
            $salary_compensation = $data->data[0]->salary_compensation;
            $allowance_disbursement = $data->data[0]->allowance_disbursement;
            $financial_liquidation = $data->data[0]->financial_liquidation;
            $loan = $data->data[0]->loan;
            $remittance = $data->data[0]->remittance;
            $expenses = $data->data[0]->expenses;
            //employee
            $employee = $data->data[0]->employee;
            $user = $data->data[0]->user;
            $tickets = $data->data[0]->tickets;
            //report
            $report_incident = $data->data[0]->report_incident;
            $report_cargo = $data->data[0]->report_cargo;
            $report_sales = $data->data[0]->report_sales;
            $report_box_purchase = $data->data[0]->report_box_purchase;
            $report_box_disposed = $data->data[0]->report_box_disposed;
            $report_branch_incentives = $data->data[0]->report_branch_incentives;
            $report_exceptions = $data->data[0]->report_exceptions;
            $report_box_aging = $data->data[0]->report_box_aging;

            //created by and date
            $createdby = $data->data[0]->createdby;
            $createddate = date('Y/m/d H:i:s');


            $result = 0;
            $mainrole = array(
                "name" => (isset($name) ? $name : ""),
                "description" => (isset($desc) ? $desc : ""),
            );
            $prev = array(
                "barcode_series" => (isset($barcode_series) ? $barcode_series : ""),
                "reservation" => (isset($reservation ) ? $reservation : ""),
                "booking" => (isset($booking) ? $booking : ""),
                "warehouse_acceptance" => (isset($warehouse_acceptance) ? $warehouse_acceptance : ""),
                "loading" => (isset($loading) ? $loading : ""),
                "in_transit" => (isset($in_transit) ? $in_transit : ""),
                "unloading" => (isset($unloading) ? $unloading : ""),
                "delivery" => (isset($delivery) ? $delivery : ""),
                "warehouse_inventory" => (isset($warehouse_inventory) ? $warehouse_inventory : ""),
                "box_releasing" => (isset($box_releasing) ? $box_releasing : ""),
                "barcode_releasing" => (isset($barcode_releasing) ? $barcode_releasing : ""),
                "trackntrace" => (isset($trackntrace) ? $trackntrace : ""),
                "report_incident" => (isset($report_incident) ? $report_incident : ""),
                "customer" => (isset($customer) ? $customer : ""),
                "receiver" => (isset($receiver) ? $receiver : ""),
                "consignments" => (isset($consignment) ? $consignment : ""),
                "employee" => (isset($employee) ? $employee : ""),
                "user" => (isset($user) ? $user : ""),
                "tickets" => (isset($tickets) ? $tickets : ""),
                "chart_accounts" => (isset($chart_accounts) ? $chart_accounts : ""),
                "salary_compensation" => (isset($salary_compensation) ? $salary_compensation : ""),
                "allowance_disbursement" => (isset($allowance_disbursement) ? $allowance_disbursement : ""),
                "financial_liquidation" => (isset($financial_liquidation) ? $financial_liquidation : ""),
                "loan" => (isset($loan) ? $loan : ""),
                "remittance" => (isset($remittance) ? $remittance : ""),
                "expenses" => (isset($expenses) ? $expenses : ""),
                "report_cargo" => (isset($report_cargo) ? $report_cargo : ""),
                "report_sales" => (isset($report_sales) ? $report_sales : ""),
                "report_box_purchase" => (isset($report_box_purchase) ? $report_box_purchase : ""),
                "report_box_disposed" => (isset($report_box_disposed) ? $report_box_disposed : ""),
                "report_branch_incentives" => (isset($report_branch_incentives) ? $report_branch_incentives : ""),
                "report_exceptions" => (isset($report_exceptions) ? $report_exceptions : ""),
                "report_box_aging" => (isset($report_box_aging) ? $report_box_aging : ""),
                "createddate" => $createddate,
                "createdby" => $createdby
            );

            if ($id <> "") {
                $model = new RoleModel($this->connection);
                $result = $model->update($mainrole, $id);
                $result = $model->updatePrev($prev, $id);
            } else {
                $model = new RoleModel($this->connection);
                $result = $model->insertdata($mainrole);
                $result = $model->insertPrev($prev, $maxrole + 1);

            }

        //print_r($data);
    }

}

