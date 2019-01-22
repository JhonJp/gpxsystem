<?php

class IndexController extends GenericController
{
    public function __construct()
    {
        parent::__construct();
        require_once __DIR__ . "/../model/IndexModel.php";
    }

    public function dashboard()
    {   
        $index = new IndexModel($this->connection);
        $status = "1";
        echo $this->twig->render('index.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "pendingreservation" => $index->countPendingReservation($status),
            "booking" => $index->countBooking(),
            "customer" => $index->countCustomers('customer'),
            "receivers" => $index->countCustomers('receiver'),
            "paid" => $index->countPendingReservation('2'),
            "booked" => $index->countBooking(),
            "acceptance" => $index->countForAcceptance('1'),
            "bookingData" => $index->getBookinglist(),
        ));
    }

    public function login()
    {
        $username = isset($_POST['username']) ? $_POST['username'] : "";
        $password = isset($_POST['password']) ? $_POST['password'] : null; 
        $index = new IndexModel($this->connection);

        if (isset($username) && isset($password)) {
            $result = $index->login($username, $password);
        }
        
        if (isset($result)){ 

            if(isset($result[0]['id'])){                
                $_SESSION['login'] = true;     
                $_SESSION['logindetails'] = $result;
                if($result[0]['position'] == 'Partner'){
                    header("Location:index.php?controller=partnerportal&action=partnerdashboard");
                }else if($result[0]['position'] == 'Admin'){ 
                    header("Location:index.php?controller=index&action=dashboard");
                }else{
                    header("Location:index.php?controller=index&action=dashboard");
                }
            }
            else{
                echo $this->twig->render('login.html', array(
                    "error" => true,
                    "username" => $username
                ));               
            }
        }
        else{                    
            echo $this->twig->render('login.html', array(
                "error" => false,
                "username" => $username
            ));
        }
        
    }

    public function delete(){      
        if(isset($_POST['modal_id'])){
            $controller = $_POST['modal_table'];
            $url = $_POST['modal_url'];
            $table = "";
            switch($controller){
                CASE "customer" : $table = "gpx_customer"; break;
                CASE "sub_receiver" : $table = "gpx_customer_sub"; break;
                CASE "receiver" : $table = "gpx_customer"; break;
                CASE "ticket" : $table = "gpx_tickets"; break;
                CASE "employee" : $table = "gpx_employee"; break;
                CASE "chartaccounts" : $table = "gpx_chartaccounts"; break;
                CASE "user" : $table = "gpx_users"; break;
                CASE "allowancedisbursement" : $table = "gpx_allowance_disbursement"; break;
                CASE "financialliquidation" : $table = "gpx_financial_liquidation"; break;
                CASE "salarycompensation" : $table = "gpx_salary_compensation"; break;
                CASE "remittance" : $table = "gpx_remittance"; break;
                CASE "expense" : $table = "gpx_expense"; break;
                CASE "boxtype" : $table = "gpx_boxtype"; break;
                CASE "boxrate" : $table = "gpx_boxrate"; break;
                CASE "reservation" : $table = "gpx_reservation"; break;
                CASE "warehouse_inventory" : $table = "gpx_warehouse_inventory"; break;
                CASE "Account Type" : $table = "gpx_chartaccounts_type"; break;
                default: $table = ""; break;
            }
            $model = new IndexModel($this->connection);
            $result = $model->delete($_POST['modal_id'],$table);                                    
            header("Location:{$url}");
            
        }
    }

    public function logout(){        
        if(session_destroy())
            header("Location:index.php?controller=index&action=login");
    }

}

