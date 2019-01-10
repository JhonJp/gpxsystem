<?php

class CustomerController extends GenericController
{
    public function __construct()
    {
        parent::__construct();
        require_once __DIR__ . "/../model/CustomerModel.php";
    }

    //LIST
    public function list()
    {
        $model = new ReservationModel($this->connection);
        $list = $model->getlist();        
        $columns = array("account_no","name","city","email","mobile");
        echo $this->twig->render('_generic_component/list.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,
            "list" => $this->allshipper,            
            "columns" => $columns,                     
            "url" => $_SERVER['REQUEST_URI'],                     
        ));        
    }

    public function edit()
    {
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        $result = null;

        if (isset($id)) {
            $customer = new CustomerModel($this->connection);
            $result = $customer->getcustomerbyid($id);            
        }
        echo $this->twig->render('customer-management/customer/edit.html', array(
            "logindetails" => $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,
            "result" => $result
        ));
    }

    public function save()
    {        
        $details = isset($_POST['data']) ? json_decode(utf8_encode($_POST['data'])) : null;
        print_r($details);        
        $type = isset($details) ?  $details[1]->value : $_POST['type'];
    
        $account_no = 'GP-'. ($this->current_userid . date('YmdHis'));
        $id = "";

        if($type == "receiver"){
            //RECEIVER
            $id = $details[0]->value;
            $list = isset($_POST['list']) ? json_decode(utf8_encode($_POST['list'])) : null;          

            $data = array(
                "account_no" => $account_no,
                "firstname" =>  $details[2]->value,
                "lastname" => $details[3]->value,
                "middlename" => $details[4]->value,
                "birthdate" => $details[5]->value,
                "mobile" => $details[6]->value,
                "phone" => $details[7]->value,
                "gender" => $details[8]->value,
                "email" => $details[9]->value,     
                "province" => $details[10]->value,
                "city" => $details[11]->value,
                "barangay" => $details[12]->value,
                "postal_code" => $details[13]->value,
                "house_number_street" => $details[14]->value,                
                "type" => $details[1]->value,
                "createdby" => $this->current_userid,
            );
            
        }
        else{
            $id = $_POST['id'];
            $data = array(
                "account_no" => $account_no,
                "firstname" => (isset($_POST['firstname']) ? $_POST['firstname'] : ""),
                "lastname" => (isset($_POST['lastname']) ? $_POST['lastname'] : ""),
                "middlename" => (isset($_POST['middlename']) ? $_POST['middlename'] : ""),
                "birthdate" => (isset($_POST['birthdate']) ? $_POST['birthdate'] : null),
                "mobile" => (isset($_POST['mobile']) ? $_POST['mobile'] : ""),
                "phone" => (isset($_POST['phone']) ? $_POST['phone'] : ""),
                "gender" => (isset($_POST['gender']) ? $_POST['gender'] : ""),
                "email" => (isset($_POST['email']) ? $_POST['email'] : ""),
                "house_number_street" => (isset($_POST['house_number_street']) ? $_POST['house_number_street'] : ""),
                "postal_code" => (isset($_POST['postal_code']) ? $_POST['postal_code'] : ""),
                "barangay" => (isset($_POST['barangay']) ? $_POST['barangay'] : ""),
                "city" => (isset($_POST['city']) ? $_POST['city'] : ""),
                "province" => (isset($_POST['province']) ? $_POST['province'] : ""),
                "type" => (isset($_POST['type']) ? $_POST['type'] : ""),
                "createdby" => $this->current_userid,
            );
        }
        
        if ($id <> "") {
            //UPDATE
            $customer = new CustomerModel($this->connection);
            $result = $customer->update($data,$id);
            if($type=="receiver"){
                print_r($details);
                $list = isset($_POST['list']) ? json_decode(utf8_encode($_POST['list'])) : null;                          
                $customer = new CustomerModel($this->connection);                
                $result = $customer->insert_subreceiver($list,$details[15]->value);
            }            
        } else {
            
            $model = new GenericModel($this->connection);
            $result = $model->insert($data,"gpx_customer");

            if($type=="receiver"){
                $list = isset($_POST['list']) ? json_decode(utf8_encode($_POST['list'])) : null;                          
                $customer = new CustomerModel($this->connection);
                $result = $customer->insert_subreceiver($list,$account_no);
            }
        }
        print_r($result);
        
    }

}

