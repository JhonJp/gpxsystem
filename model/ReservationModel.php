<?php
require_once __DIR__ . "/GenericModel.php";

class ReservationModel extends GenericModel
{

    public function __construct($connection)
    {
        parent::__construct($connection);
    }

    //LIST
    public function getlist()
    {
        $query = $this->connection->prepare("SELECT
        gr.id ,
        gr.reservation_no,
        CONCAT(ge.firstname, ' ',ge.lastname) as driver_name,
        CONCAT(gc.firstname, ' ',gc.lastname) as customer,
        SUM(grb.quantity) as qty ,
        CONCAT(SUM(grb.deposit)) as deposit ,
        gr.createddate as date,
        gbr.name as branch,
        grs.name as status
        FROM gpx_reservation gr
        JOIN gpx_reservation_boxtype grb ON gr.reservation_no = grb.reservation_no
        LEFT JOIN gpx_customer gc ON gc.account_no = gr.account_no
        JOIN gpx_employee ge ON gr.assigned_to = ge.id
        LEFT JOIN gpx_branch gbr ON ge.branch = gbr.id
        LEFT JOIN gpx_reservation_status grs ON grs.id = gr.status
        GROUP BY gr.id
        ORDER BY gr.createddate DESC

        ");
        $query->execute();
        $result = $query->fetchAll();
        $this->connection = null;
        return $result;
    }

    //EDIT
    public function getreservationbyid($id)
    {
        $query = $this->connection->prepare("SELECT gr.*,
        grb.boxtype , grb.quantity , grb.deposit , grb.reservation_no,
        gc.account_no as account_no , gr.status ,gr.assigned_to
        FROM gpx_reservation_boxtype grb
        JOIN gpx_reservation gr ON grb.reservation_no = gr.reservation_no
        JOIN gpx_customer gc ON gr.account_no = gc.account_no
        WHERE gr.id = :id");
        $query->execute(array(
            "id" => $id,
        ));
        $result = $query->fetchAll();
        $this->connection = null;
        return $result;
    }

    public function getreservationdetails($reservation_no)
    {
        $query = $this->connection->prepare("SELECT gr.*,
        CONCAT(gc.firstname,' ',gc.lastname) as customer,
        CONCAT(ge.firstname,' ',ge.lastname) as assigned_to
        FROM  gpx_reservation gr 
        LEFT JOIN gpx_customer gc ON gr.account_no = gc.account_no
        LEFT JOIN gpx_employee ge ON ge.id = gr.assigned_to
        WHERE gr.reservation_no = :reservation_no");
        $query->execute(array(
            "reservation_no" => $reservation_no,
        ));
        $result = $query->fetchAll();
        return $result;
    }

    public function getboxnumbers($reservation_no)
    {
        $query = $this->connection->prepare("SELECT A.boxtype , A.box_number
        , B.name as status , A.transaction_no
        FROM  gpx_reservation_boxtype_box_number A
        LEFT JOIN gpx_reservation_status B ON A.status = B.id
        WHERE reservation_no = :reservation_no");
        $query->execute(array(
            "reservation_no" => $reservation_no,
        ));
        $result = $query->fetchAll();
        return $result;
    }


    public function save($data, $list)
    {
        $result = null;
        try {

            $account_no = $data->data[0]->account_no;
            $grandtotal = $data->data[0]->grandtotal;
            $assigned_to = $data->data[0]->assigned_to;
            $createdby = $this->getuserlogin();
            $reservation_no = ($createdby . date('YmdHis'));
            $createddate = date('Y/m/d H:i:s');

            //INSERT RESERVATION
            $result = $this->insertto_gpx_reservation($reservation_no, $account_no, $createdby, $createddate, $assigned_to, 1);
            if ($result != 0) {
                //INSERT RESERVATION BOXTYPE
                for ($x = 1; $x < count($list); $x++) {
                    $boxtype = trim($list[$x]->boxtype);
                    $qty = $list[$x]->qty;
                    $deposit = $list[$x]->deposit;
                    $boxtype_id = $list[$x]->boxtype_id;
                    $result = $this->insertto_gpx_reservation_boxtype($boxtype, $qty, $deposit, $reservation_no, $boxtype_id);
                }
            }
        } catch (Exception $e) {
            $this->error_logs("Reservation - save", $e->getMessage());
        }
        return $result;
    }

    public function update($data, $list)
    {

        $query = $this->connection->prepare("UPDATE gpx_reservation
        SET status = :status,
        assigned_to = :assigned_to
        WHERE id = :id");
        $result = $query->execute(array(
            "id" => $data->data[0]->reservationid,
            "status" => $data->data[0]->status,
            "assigned_to" => $data->data[0]->assigned_to,
        ));

        $query = $this->connection->prepare("SELECT reservation_no FROM gpx_reservation
        WHERE id = :id");
        $query->execute(array(
            "id" => $data->data[0]->reservationid,
        ));
        $reservation_no = $query->fetchColumn();

        for ($x = 0; $x < count($list); $x++) {
            $boxtype = trim($list[$x]->boxtype);
            $qty = $list[$x]->qty;
            $deposit = $list[$x]->deposit;
            $boxtype_id = $list[$x]->boxtype_id;  
            print_r($list);
            if ($list[$x]->id == "" && $list[$x]->boxtype_id != "") {
                //INSERT
                $result = $this->insertto_gpx_reservation_boxtype($boxtype, $qty, $deposit, $reservation_no, $boxtype_id);
            }
        }

    }

    public function insertto_gpx_reservation($reservation_no, $account_no, $createdby, $createddate, $assigned_to, $status)
    {
        $result = 0;
        if ($this->checkreservationifexist($reservation_no) == 0) {
            $query = $this->connection->prepare("INSERT INTO gpx_reservation(reservation_no,
            account_no, createdby,createddate ,assigned_to,status)
            VALUES (:reservation_no, :account_no, :createdby , :createddate ,:assigned_to, :status)");
            $result = $query->execute(array(
                "reservation_no" => $reservation_no,
                "account_no" => $account_no,
                "createdby" => $createdby,
                "createddate" => $createddate,
                "assigned_to" => $assigned_to,
                "status" => $status,
            ));
        }
        return $result;
    }

    public function insertto_gpx_reservation_boxtype($boxtype, $quantity, $deposit, $reservation_no, $boxtype_id)
    {
        $result = null;
        $cnt = 0;
        
        if ($boxtype_id != "") {
            $query = $this->connection->prepare("SELECT * FROM gpx_reservation_boxtype
            WHERE reservation_no = :reservation_no AND boxtype = :boxtype AND quantity = :quantity LIMIT 1");
            $query->execute(array(
                "reservation_no" => $reservation_no,
                "boxtype" => $boxtype,
                "quantity" => $quantity,
            ));
            $result = $query->fetchAll();
            $cnt = count($result);
        }
        
        if ($cnt == 0) {
            $query = $this->connection->prepare("INSERT INTO gpx_reservation_boxtype(boxtype,
            quantity , deposit ,reservation_no,boxtype_id)
            VALUES (:boxtype, :quantity, :deposit ,:reservation_no,:boxtype_id)");
            $query->execute(array(
                "boxtype" => $boxtype,
                "quantity" => $quantity,
                "deposit" => $deposit,
                "reservation_no" => $reservation_no,
                "boxtype_id" => $boxtype_id,
            ));
        }

        return $result;
    }

    public function insertto_gpxpayment($or_no, $transaction_no, $reservation_no, $paymentterm, $deposit, $total_amount, $createdby, $createddate)
    {

        $query = $this->connection->prepare("INSERT INTO gpx_payment(or_no,transaction_no,reservation_no,paymentterm,
        deposit,total_amount,createdby,createddate)
        VALUES (:or_no,:transaction_no,:reservation_no,:paymentterm,
        :deposit,:total_amount,:createdby,:createddate)");
        $result = $query->execute(array(
            "or_no" => $or_no,
            "transaction_no" => $transaction_no,
            "reservation_no" => $reservation_no,
            "paymentterm" => $paymentterm,
            "deposit" => $deposit,
            "total_amount" => $total_amount,
            "createdby" => $createdby,
            "createddate" => $createddate,
        ));
        return $result;
    }

    public function insertto_gpx_reservation_boxtype_box_number($boxtype, $box_number, $reservation_no, $createddate,$status)
    {
        $result = 0;
        if ($this->checkboxnumberifexist($box_number) == 0) {
            $query = $this->connection->prepare("INSERT INTO gpx_reservation_boxtype_box_number
        (boxtype,box_number,reservation_no,createddate,status)
        VALUES(:boxtype,:box_number,:reservation_no,:createddate,:status)");
            $result = $query->execute(array(
                "boxtype" => $boxtype,
                "box_number" => $box_number,
                "reservation_no" => $reservation_no,
                "createddate" => $createddate,
                "status" => $status,
            ));
        }
        return $result;
    }

    public function apisave($data)
    {
        try {
            $countdata = count($data['data']);
            
        
            for ($x = 0; $x < $countdata; $x++) {
                //GET
                $reservation_no = $data['data'][$x]['reservation_no']; //$data->data[$x]->reservation_no;
                $account_no = $data['data'][$x]['account_no'];
                $createdby = $data['data'][$x]['createdby'];
                $createddate = $data['data'][$x]['createddate'];
                $assigned_to = $data['data'][$x]['assigned_to'];
                $statusid = $data['data'][$x]['statusid'];

                $deposit = $data['data'][$x]['payment'][0]['deposit']; //$data->data[$x]->payment[0]->deposit;
                $payment_createdby = $data['data'][$x]['payment'][0]['createdby'];
                $payment_createddate = $data['data'][$x]['payment'][0]['createddate'];

                //CHECK IF EXIST
                if ($this->checkreservationifexist($reservation_no) != 0) {

                    //UPDATE RESERVATION
                    $query = $this->connection->prepare("UPDATE gpx_reservation SET
                    status = :status  WHERE reservation_no = :reservation_no");
                    $query->execute(array(
                        "reservation_no" => $reservation_no,
                        "status" => $statusid,
                    ));
                    //INSERT BOX NUMBERS
                    $countboxnumber = count($data['data'][$x]['reservation_boxtype_box_number']);
                    for ($y = 0; $y < $countboxnumber; $y++) {
                        $boxtype = $data['data'][$x]['reservation_boxtype_box_number'][$y]['boxtype']; //$data->data[$x]->reservation_boxtype_box_number[$y]->boxtype
                        $box_number = $data['data'][$x]['reservation_boxtype_box_number'][$y]['box_number'];
                        $reservation_no = $data['data'][$x]['reservation_boxtype_box_number'][$y]['reservation_no'];
                        $createddate = $data['data'][$x]['reservation_boxtype_box_number'][$y]['createddate'];
                        $status = $data['data'][$x]['reservation_boxtype_box_number'][$y]['status'];

                        $this->insertto_gpx_reservation_boxtype_box_number($boxtype, $box_number, $reservation_no, $createddate, $status);
                    }
                    
                    //INSERT PAYMENT
                    $this->insertto_gpxpayment("", "", $reservation_no, "Partial", $deposit, 0, $payment_createdby, $payment_createddate);

                } else {
                    //INSERT RESERVATION
                    $result = $this->insertto_gpx_reservation($reservation_no, $account_no, $createdby, $createddate, $assigned_to, $statusid);
                    if ($result != 0) {
                        //INSERT BOX TYPE
                        $countype = count($data['data'][$x]['reservation_boxtype']);
                        for ($t = 0; $t < $countype; $t++) {
                            $boxtype = $data['data'][$x]['reservation_boxtype'][$t]['boxtype'];
                            $quantity = $data['data'][$x]['reservation_boxtype'][$t]['quantity'];
                            $deposit = $data['data'][$x]['reservation_boxtype'][$t]['deposit'];
                            $reservation_no = $data['data'][$x]['reservation_boxtype'][$t]['reservation_no'];
                            $boxtype_id = $data['data'][$x]['reservation_boxtype'][$t]['boxtype_id'];        
                            $result = $this->insertto_gpx_reservation_boxtype($boxtype, $quantity, $deposit, $reservation_no, $boxtype_id);
                        }
                        //INSERT BOX NUMBERS
                        $countboxnumber = count($data['data'][$x]['reservation_boxtype_box_number']);
                        for ($y = 0; $y < $countboxnumber; $y++) {
                            $boxtype = $data['data'][$x]['reservation_boxtype_box_number'][$y]['boxtype']; //$data->data[$x]->reservation_boxtype_box_number[$y]->boxtype
                            $box_number = $data['data'][$x]['reservation_boxtype_box_number'][$y]['box_number'];
                            $reservation_no = $data['data'][$x]['reservation_boxtype_box_number'][$y]['reservation_no'];
                            $createddate = $data['data'][$x]['reservation_boxtype_box_number'][$y]['createddate'];
                            $status = $data['data'][$x]['reservation_boxtype_box_number'][$y]['status'];

                            $this->insertto_gpx_reservation_boxtype_box_number($boxtype, $box_number, $reservation_no, $createddate,$status);
                        }
                        //INSERT PAYMENT
                        $this->insertto_gpxpayment("", "", $reservation_no, "Partial", $deposit, 0, $payment_createdby, $payment_createddate);
                    }
                }

            }
            
        } catch (Exception $e) {
            $this->error_logs("Reservation - apisave", $e->getMessage());
        }
    }

    public function checkreservationifexist($reservation_no)
    {
        $result = 0;
        $query = $this->connection->prepare("SELECT * FROM gpx_reservation
        WHERE reservation_no = :reservation_no");
        $query->execute(array(
            "reservation_no" => $reservation_no,
        ));
        $result = $query->fetchAll();
        return count($result);
    }

    public function checkboxnumberifexist($box_number)
    {
        $result = 0;
        $query = $this->connection->prepare("SELECT * FROM gpx_reservation_boxtype_box_number
        WHERE box_number = :box_number");
        $query->execute(array(
            "box_number" => $box_number,
        ));
        $result = $query->fetchAll();
        return count($result);
    }

}
