<?php
require_once __DIR__ . "/GenericModel.php";

class ApiModel extends GenericModel
{
    public function __construct($connection)
    {
        parent::__construct($connection);
    }

    public function testsave($data){
        $query = $this->connection->prepare("INSERT INTO aatest(data) VALUES (:data)");
        $result = $query->execute(array(
            "data" => $data
        ));
        return $result;
    }

    public function getallpendingreservation($userid)
    {
        $arr_reservation = array();
       

        $query = $this->connection->prepare("SELECT gr.id as reservation_id , gr.reservation_no , 
        gr.status as statusid , grs.name as statusname , 
        gc.account_no as account_no , concat(gc.firstname,' ', gc.lastname) as customername ,
        gr.createdby , gr.assigned_to , gr.createddate
        FROM gpx_reservation gr 
        JOIN gpx_customer gc ON gr.account_no = gc.account_no
        JOIN gpx_reservation_status grs ON gr.status = grs.id
        WHERE gr.assigned_to = :userid AND gr.status = 1");
        $query->execute(array(
            "userid" => $userid
        ));
        $reservation = $query->fetchAll();
        
        foreach ($reservation as $row) {
            
            $arr_reservation_boxtype = array();

            $query = $this->connection->prepare("SELECT id ,boxtype_id, boxtype , quantity , deposit ,reservation_no 
            FROM gpx_reservation_boxtype WHERE reservation_no = :reservation_no");
            $query->execute(array(
                "reservation_no" => $row['reservation_no']
            ));

            $result = $query->fetchAll();
            foreach ($result as $row2) {
                array_push(
                    $arr_reservation_boxtype,
                    array(
                        "reservation_boxtype_id" => $row2['id'],
                        "boxtype" => $row2['boxtype'],
                        "boxtype_id" => $row2['boxtype_id'],
                        "quantity" => $row2['quantity'],
                        "deposit" => $row2['deposit'],
                        "reservation_no" => $row2['reservation_no']
                    )
                );
            }

            array_push(
                $arr_reservation,
                array(
                    "reservation_id" => $row['reservation_id'],
                    "reservation_no" => $row['reservation_no'],
                    "statusid" => $row['statusid'],
                    "statusname" => $row['statusname'],
                    "account_no" => $row['account_no'],
                    "customername" => $row['customername'],
                    "createdby" => $row['createdby'],
                    "createddate" => $row['createddate'],
                    "assigned_to" => $row['assigned_to'],
                    "reservation_boxtype_details" => $arr_reservation_boxtype
                )
            );

        }

        $this->connection = null;
        return $arr_reservation;
    }

    public function checkreservationifexist($reservationno){
        return 1;
    }   

    public function savereservation($data){
                
        //reservation_boxtype_box_number[0]->reservation_id;
        $countdata = count($data);

        for($x = 0; $x < $countdata; $x++){
            
            //UPDATE RESERVATION
            $query = $this->connection->prepare("UPDATE gpx_reservation SET status = :status WHERE reservation_no = :reservationno");
            $result = $query->execute(array(
                "status" => $data->data[$x]->statusid,
                "reservationno" => $data->data[$x]->reservationno
            ));

            //INSERT BOX NUMBER FOR RESERVATION
            $countboxnumber = count($data->data[$x]->reservation_boxtype_box_number);

            for($y=0 ; $y < $countboxnumber; $y++){

                $query = $this->connection->prepare("INSERT INTO gpx_reservation_boxtype_box_number(boxtype,box_number,reservation_boxtype_id,createddate) 
                VALUES(:boxtype,:box_number,:reservation_boxtype_id,:createddate)");
                $result = $query->execute(array(
                    "boxtype" => $data->data[$x]->reservation_boxtype_box_number[$y]->boxtype,
                    "box_number" => $data->data[$x]->reservation_boxtype_box_number[$y]->box_number,
                    "reservation_boxtype_id" => $data->data[$x]->reservation_boxtype_box_number[$y]->reservation_boxtype_id,
                    "createddate" => $data->data[$x]->reservation_boxtype_box_number[$y]->createddate
                ));
                
            }
            
            //INSERT PAYMENT
            $query = $this->connection->prepare("INSERT INTO gpx_payment
            (or_no,booking_id,reservation_id,paymentterm,deposit,total_amount,createdby,createddate) 
            VALUES (:or_no,:booking_id,:reservation_id,:paymentterm,:deposit,:total_amount,:createdby,:createddate)");
            $result = $query->execute(array(
                "or_no" => $data->data[$x]->payment[0]->or_no,
                "booking_id" => $data->data[$x]->payment[0]->booking_id,
                "reservation_id" => $data->data[$x]->payment[0]->reservation_id,
                "paymentterm" => $data->data[$x]->payment[0]->paymentterm,
                "deposit" => $data->data[$x]->payment[0]->deposit,
                "total_amount" => $data->data[$x]->payment[0]->total_amount,
                "createdby" => $data->data[$x]->payment[0]->createdby,
                "createddate" => $data->data[$x]->payment[0]->createddate
            ));            
        }
        
        return $result;
    }

}
?>