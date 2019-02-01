<?php
require_once __DIR__ . "/GenericModel.php";

class CustomerModel extends GenericModel
{

    public function __construct($connection)
    {
        parent::__construct($connection);
    }

    public function getcustomerbyid($id)
    {
        $query = $this->connection->prepare("SELECT  * FROM gpx_customer WHERE id = :id");
        $query->execute(array(
            "id" => $id,
        ));
        $result = $query->fetchAll();
        $this->connection = null;
        return $result;
    }

    public function getcustomerbyAccnt($id)
    {
        $query = $this->connection->prepare("SELECT  * FROM gpx_customer WHERE account_no = :id");
        $query->execute(array(
            "id" => $id,
        ));
        $result = $query->fetchAll();
        $this->connection = null;
        return $result;
    }

    public function getreceiverbyAccnt($id)
    {
        $query = $this->connection->prepare("SELECT  gc.*,
        gprov.provDesc as province,
        gbry.brgyDesc as brgy,
        gct.citymunDesc as city
        FROM gpx_customer gc
        LEFT JOIN refbrgy gbry ON gbry.brgyCode = gc.barangay
        LEFT JOIN refcitymun gct ON gct.citymunCode = gc.city
        LEFT JOIN refprovince gprov ON gprov.provCode = gc.province
        WHERE account_no = :id");
        $query->execute(array(
            "id" => $id,
        ));
        $result = $query->fetchAll();
        $this->connection = null;
        return $result;
    }

    public function getaccountno()
    {
        $query = $this->connection->prepare("SELECT MAX(accountno) as lastno FROM gpx_customer");
        $query->execute();
        $result = $query->fetchColumn();
        $lastno = preg_replace("/[^0-9]/", '', $result);
        return (int) $lastno + 1;
    }

    public function getsubreciever($account_no)
    {

        $query = $this->connection->prepare("SELECT * FROM gpx_customer_sub WHERE account_no = :account_no");
        $query->execute(array("account_no" => $account_no));
        $result = $query->fetchAll();
        return $result;
    }

    public function checkcustomerifexist()
    {
        $query = $this->connection->prepare("SELECT MAX(accountno) as lastno FROM gpx_customer");
        $query->execute();
        $result = $query->fetchColumn();
        $lastno = preg_replace("/[^0-9]/", '', $result);
        return (int) $lastno + 1;
        return 0;
    }

    public function save($data)
    {
        //INSERT
        $userid = $this->getuserlogin();
        $accountno = 'GP-' . ($userid . date('YmdHis'));

        $query = $this->connection->prepare("INSERT INTO gpx_customer
        (account_no,firstname,lastname,middlename,mobile,phone,email,gender,birthdate,house_number_street,
        barangay,postal_code,city,type,createdby)
        VALUES (:accountno,:firstname,:lastname,:middlename,:mobile,:phone,:email,:gender,:birthdate,:house_number,
        :street,:barangay,:city,:type,:createdby)");
        $result = $query->execute(array(
            "accountno" => $accountno,
            "firstname" => $data['firstname'],
            "lastname" => $data['lastname'],
            "middlename" => $data['middlename'],
            "mobile" => $data['mobile'],
            "phone" => $data['phone'],
            "email" => $data['email'],
            "gender" => $data['gender'],
            "birthdate" => $data['birthdate'],
            "house_number_street" => $data['house_number_street'],
            "postal_code" => $data['postal_code'],
            "barangay" => $data['barangay'],
            "city" => $data['city'],
            "type" => $data['type'],
            "createdby" => $userid,
        ));
        return $result;
    }

    public function insert_subreceiver($list, $account_no)
    {

        $result = 0;
        for ($x = 1; $x < count($list); $x++) {
            $query = $this->connection->prepare("SELECT count(*) as cnt FROM gpx_customer_sub WHERE firstname = :firstname AND lastname = :lastname");
            $query->execute(array(
                "firstname" => $list[$x]->fname,
                "lastname" => $list[$x]->lname,
            ));
            $count = $query->fetchColumn();
            if ($count == 0) {
                $query = $this->connection->prepare("INSERT INTO gpx_customer_sub
                (firstname,lastname,relation,address,account_no)
                VALUES (:firstname,:lastname,:relation,:address,:account_no)");
                $result = $query->execute(array(
                    "firstname" => $list[$x]->fname,
                    "lastname" => $list[$x]->lname,
                    "relation" => $list[$x]->relation,
                    "address" => $list[$x]->address,
                    "account_no" => $account_no,
                ));
            }
        }
        return $result;
    }

    public function update($data, $customerid)
    {
        //UPDATE
        $query = $this->connection->prepare("UPDATE
        gpx_customer
        SET firstname = :firstname,lastname = :lastname,middlename = :middlename,mobile = :mobile,
        phone = :phone,email = :email,
        gender = :gender,birthdate = :birthdate,house_number_street = :house_number_street,
        postal_code = :postal_code,barangay = :barangay,city = :city ,province = :province
        WHERE id = :id
        ");
        $result = $query->execute(array(
            "id" => $customerid,
            "firstname" => $data['firstname'],
            "lastname" => $data['lastname'],
            "middlename" => $data['middlename'],
            "mobile" => $data['mobile'],
            "phone" => $data['phone'],
            "email" => $data['email'],
            "gender" => $data['gender'],
            "birthdate" => $data['birthdate'],
            "house_number_street" => $data['house_number_street'],
            "postal_code" => $data['postal_code'],
            "barangay" => $data['barangay'],
            "city" => $data['city'],
            "province" => $data['province'],
        ));

        return $result;
    }

    public function apisave($data)
    {
        try {
            $countdata = count($data['data']);

            for ($x = 0; $x < $countdata; $x++) {

                $query = $this->connection->prepare("INSERT INTO gpx_customer
                (account_no,firstname,lastname,middlename,mobile,phone,email,birthdate,gender,house_number_street,
                barangay,postal_code,city,type,createdby)
                VALUES (:account_no,:firstname,:lastname,:middlename,:mobile,:phone,:email,:birthdate,:gender,:house_number_street,
                :postal_code,:barangay,:city,:type,:createdby)");
                $result = $query->execute(array(
                    "account_no" => $data['data'][$x]['account_no'],
                    "firstname" => $data['data'][$x]['firstname'],
                    "lastname" => $data['data'][$x]['lastname'],
                    "middlename" => $data['data'][$x]['middlename'],
                    "mobile" => $data['data'][$x]['mobile'],
                    "phone" => $data['data'][$x]['phone'],
                    "email" => $data['data'][$x]['email'],
                    "birthdate" => $data['data'][$x]['birthdate'],
                    "gender" => $data['data'][$x]['gender'],
                    "house_number_street" => $data['data'][$x]['unit'],
                    "postal_code" => $data['data'][$x]['postal'],
                    "barangay" => $data['data'][$x]['barangay'],
                    "city" => $data['data'][$x]['city'],
                    "type" => $data['data'][$x]['type'],
                    "createdby" => $data['data'][$x]['createdby'],
                ));
            }
        } catch (Exception $e) {
            $this->error_logs("Customer - apisave", $e->getMessage());
        }

    }
}
