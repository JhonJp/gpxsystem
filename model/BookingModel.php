<?php
require_once __DIR__ . "/GenericModel.php";

class BookingModel extends GenericModel
{

    public function __construct($connection)
    {
        parent::__construct($connection);
    }

    //LIST
    public function getlist()
    {
        $query = $this->connection->prepare("
        SELECT gb.transaction_no , CONCAT(gc.firstname,' ',gc.lastname) as customer ,
        gb.book_date , gbs.name as status , COUNT(gbcb.box_number) as qty,gbcb.box_number,
        gbr.name as branch
        FROM
        gpx_booking gb
        LEFT JOIN gpx_customer gc ON gb.customer = gc.account_no
        LEFT JOIN gpx_employee gemp ON gemp.id = gb.createdby
        LEFT JOIN gpx_branch gbr ON gbr.id = gemp.branch
        JOIN gpx_booking_status gbs ON gb.booking_status = gbs.id
        LEFT JOIN gpx_booking_consignee_box gbcb ON gb.transaction_no = gbcb.transaction_no
        GROUP BY gb.transaction_no
        ");
        $query->execute();
        $result = $query->fetchAll();
        $this->connection = null;
        return $result;
    }

    public function getlistContent()
    {
        $query = $this->connection->prepare("
        SELECT * FROM gpx_boxcontents
        ");
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }
    

    public function getlistapi()
    {

        $result = array();
        $query = $this->connection->prepare("SELECT * FROM gpx_booking");
        $query->execute();
        $result1 = $query->fetchAll();

        foreach ($result1 as $row) {

            $gpx_booking_consignee_box = array();
            $query = $this->connection->prepare("SELECT * FROM gpx_booking_consignee_box WHERE transaction_no = :transaction_no");
            $query->execute(array(
                "transaction_no" => $row['transaction_no'],
            ));
            $result2 = $query->fetchAll();

            foreach ($result2 as $row2) {
                array_push(
                    $gpx_booking_consignee_box,
                    array(
                        "id" => $row2['id'],
                        "consignee" => $row2['consignee'],
                        "source_id" => $row2['source_id'],
                        "destination_id" => $row2['destination_id'],
                        "boxtype" => $row2['boxtype'],
                        "transaction_no" => $row2['transaction_no'],
                        "box_number" => $row2['box_number'],
                        "hardport" => $row2['hardport'],
                        "box_content" => $row2['box_content'],
                        "status" => $row2['status'],
                    )
                );
            }

            array_push(
                $result,
                array(
                    "id" => $row['id'],
                    "transaction_no" => $row['transaction_no'],
                    "reservation_no" => $row['reservation_no'],
                    "customer" => $row['customer'],
                    "book_date" => $row['book_date'],
                    "schedule_date" => $row['schedule_date'],
                    "booking_status" => $row['booking_status'],
                    "booking_type" => $row['booking_type'],
                    "createdby" => $row['createdby'],
                    "gpx_booking_consignee_box" => $gpx_booking_consignee_box,
                )
            );
        }

        return $result;
    }

    public function getbookingdetails($transaction_no)
    {
        $query = $this->connection->prepare("SELECT
        gb.transaction_no , gb.reservation_no,
        CONCAT(gc1.firstname, ' ', gc1.lastname) as customer,
        gb.book_date,
        gbs.name as status
        FROM gpx_booking gb
        LEFT JOIN gpx_customer gc1 ON gc1.account_no = gb.customer
        LEFT JOIN gpx_booking_status gbs ON gbs.id = gb.booking_status
        WHERE
        transaction_no = :transaction_no
        ");
        $query->execute(
            array(
                "transaction_no" => $transaction_no,
            )
        );
        $result = $query->fetchAll();
        return $result;
    }

    public function getbookingboxnumber($transaction_no)
    {
        $query = $this->connection->prepare("SELECT
        gbcb.* ,
        CONCAT(gc1.firstname, ' ', gc1.lastname) as receiver
        FROM gpx_booking_consignee_box gbcb
        LEFT JOIN gpx_customer gc1 ON gbcb.consignee = gc1.account_no
        LEFT JOIN gpx_reservation_status grs  ON grs.id = gc1.account_no
        WHERE
        transaction_no = :transaction_no
        ");
        $query->execute(
            array(
                "transaction_no" => $transaction_no,
            )
        );
        $result = $query->fetchAll();
        return $result;
    }

    public function getcustomer($reservation_no)
    {
        $query = $this->connection->prepare("SELECT account_no FROM gpx_reservation WHERE reservation_no = :reservation_no");
        $query->execute(array("reservation_no" => $reservation_no));
        $result = $query->fetchAll();
        $this->connection = null;
        return $result;
    }

    public function apisave($data)
    {
        try {

            $countdata = count($data['data']);
            for ($x = 0; $x < $countdata; $x++) {

                $check = $this->checkbooking($data['data'][$x]['transaction_no']);

                if (count($check) == 0) {
                    //BOOKING
                    $query = $this->connection->prepare("INSERT INTO gpx_booking(transaction_no,reservation_no,customer,
                    book_date,booking_status,booking_type,createdby)
                VALUES (:transaction_no,:reservation_no,:customer,:book_date,:booking_status,:booking_type,:createdby)");
                    $result = $query->execute(array(
                        "transaction_no" => $data['data'][$x]['transaction_no'],
                        "reservation_no" => $data['data'][$x]['reservation_no'],
                        "customer" => $data['data'][$x]['customer'],
                        "book_date" => $data['data'][$x]['booking_date'],
                        "booking_status" => $data['data'][$x]['booking_stat'],
                        "booking_type" => $data['data'][$x]['booking_type'],
                        "createdby" => $data['data'][$x]['created_by'],
                    ));

                    //CONSIGNEE BOOKING
                    $countboxnumber = count($data['data'][$x]['booking_box']);
                    for ($y = 0; $y < $countboxnumber; $y++) {
                        $query = $this->connection->prepare("INSERT INTO gpx_booking_consignee_box(consignee,boxtype,
                        transaction_no,box_number,source_id,destination_id,box_content,hardport)
                    VALUES (:consignee,:boxtype,:transaction_no,:box_number,:source_id,:destination_id,:box_content,:hardport)");
                        $result = $query->execute(array(
                            "consignee" => $data['data'][$x]['booking_box'][$y]['consignee'],
                            "boxtype" => $data['data'][$x]['booking_box'][$y]['boxtype'],
                            "transaction_no" => $data['data'][$x]['booking_box'][$y]['transaction_no'],
                            "box_number" => $data['data'][$x]['booking_box'][$y]['box_number'],
                            "source_id" => $data['data'][$x]['booking_box'][$y]['source_id'],
                            "destination_id" => $data['data'][$x]['booking_box'][$y]['destination_id'],
                            "box_content" => $data['data'][$x]['booking_box'][$y]['contents'],
                            "hardport" => $data['data'][$x]['booking_box'][$y]['hardport'],
                        ));

                        //////UPDATE RESERVATION STATUS///////
                        $box_no = $data['data'][$x]['booking_box'][$y]['box_number'];
                        $transaction_number = $data['data'][$x]['transaction_no'];
                        $this->updateReservationStatus($data['data'][$x]['reservation_no']);
                        $this->updateReservationStatusBoxNumber($box_no, $transaction_number);
                        $this->updateBarcodeStatus($box_no);

                        ///////////TRACK N TRACE LOGS/////////
                        $logs = array(
                            "transaction_no" => $box_no,
                            "status" => "Picked-Up",
                            "dateandtime" => $data['data'][$x]['booking_date'],
                            "activity" => "Picked-Up",
                            "location" => $this->getlocationemployeebyid($data['data'][$x]['created_by']),
                            "qty" => "1",
                            "details" => "Box has been picked-up on ".date_format($data['data'][$x]['booking_date'],"d/m/Y"),
                        );
                        $this->savetrackntrace($logs);
                    }

                    //DISCOUNTS
                    $countdiscount = count($data['data'][$x]['discounts']);
                    for ($xy = 0; $xy < $countdiscount; $xy++) {
                        $queries = $this->connection->prepare("INSERT INTO gpx_booking_discount(transaction_no, discount, remarks) 
                        VALUES (:transaction_no,:discount,:remarks)");
                        $result = $queries->execute(array(
                            "transaction_no" => $data['data'][$x]['discounts'][$xy]['transaction_no'],
                            "discount" => $data['data'][$x]['discounts'][$xy]['discount'],
                            "remarks" => $data['data'][$x]['discounts'][$xy]['remarks'],
                        ));
                    }

                    //INSERT IMAGE ATTACHMENT
                    $countimage = count($data['data'][$x]['booking_image']);
                    for ($image = 0; $image < $countimage; $image++) {

                        $query2 = $this->connection->prepare("INSERT INTO gpx_all_image(module,transaction_no,image) VALUES (:module,:trans,:image)");
                        $result = $query2->execute(array(
                            "module" => $data['data'][$x]['booking_image'][$image]['module'],
                            "trans" => $data['data'][$x]['transaction_no'],                            
                            "image" => $data['data'][$x]['booking_image'][$image]['image'],                        
                        ));
                    }
                
                    //$this->updateReservationStatus($data['data'][$x]['reservation_no']);

                    $query = $this->connection->prepare("SELECT * FROM gpx_payment
                    WHERE transaction_no = :transaction_no OR reservation_no = :reservation_no");
                    $query->execute(array(
                        "transaction_no" => $data['data'][$x]['payment'][0]['booking_id'],
                        "reservation_no" => $data['data'][$x]['payment'][0]['reservation_no'],
                    ));
                    $gpx_payment = $query->fetchAll();

                    if (count($gpx_payment) != 0) {
                        //////UPDATE PAYMENT////
                        $query = $this->connection->prepare("UPDATE gpx_payment SET paymentterm = :paymentterm,
                        transaction_no = :transaction_no, deposit = :deposit , total_amount = :total_amount
                        WHERE transaction_no = :transaction_no OR reservation_no = :reservation_no");
                        $query->execute(array(
                            "paymentterm" => $data['data'][$x]['payment'][0]['paymentterm'],
                            "deposit" => $data['data'][$x]['payment'][0]['deposit'],
                            "total_amount" => $data['data'][$x]['payment'][0]['total_amount'],
                            "transaction_no" => $data['data'][$x]['payment'][0]['booking_id'],
                            "reservation_no" => $data['data'][$x]['payment'][0]['reservation_no'],
                        ));

                    } else {
                        //////INSERT PAYMENT////
                        $query = $this->connection->prepare("INSERT INTO gpx_payment(or_no,transaction_no,reservation_no,paymentterm,
                deposit,total_amount,createdby,createddate)
                VALUES (:or_no,:transaction_no,:reservation_no,:paymentterm,
                :deposit,:total_amount,:createdby,:createddate)");
                        $result = $query->execute(array(
                            "or_no" => "",
                            "transaction_no" => $data['data'][$x]['payment'][0]['booking_id'],
                            "reservation_no" => $data['data'][$x]['payment'][0]['reservation_no'],
                            "paymentterm" => $data['data'][$x]['payment'][0]['paymentterm'],
                            "deposit" => $data['data'][$x]['payment'][0]['deposit'],
                            "total_amount" => $data['data'][$x]['payment'][0]['total_amount'],
                            "createdby" => $data['data'][$x]['payment'][0]['createdby'],
                            "createddate" => $data['data'][$x]['payment'][0]['createddate'],
                        ));
                    }
                }
            }

        } catch (Exception $e) {
            $this->error_logs("Booking - apisave", $e->getMessage());
        }
    }

    public function checkbooking($transaction_no)
    {
        $query = $this->connection->prepare("SELECT * FROM gpx_booking WHERE transaction_no = :transaction_no");
        $query->execute(array("transaction_no" => $transaction_no));
        $result = $query->fetchAll();
        return $result;
    }

    public function updateReservationStatusBoxNumber($box_number, $transaction_number)
    {
        $query = $this->connection->prepare("UPDATE gpx_reservation_boxtype_box_number
        SET status = 4 , transaction_no = :transaction_no
        WHERE box_number = :box_number");
        $query->execute(array(
            "box_number" => $box_number,
            "transaction_no" => $transaction_number,
        ));

    }

    public function updateBarcodeStatus($box_number)
    {
        $query = $this->connection->prepare("UPDATE gpx_barcode_distribution_number
        SET status = 1 
        WHERE boxnumber = :box_number");
        $query->execute(array(
            "box_number" => $box_number,
        ));

    }

    public function updateReservationStatus($reservation_no)
    {
        $query = $this->connection->prepare("UPDATE gpx_reservation SET status = 4
        WHERE reservation_no = :reservation_no");
        $query->execute(array(
            "reservation_no" => $reservation_no,
        ));

    }

    //GET BOOKING IMAGES
    public function getimages($trans)
    {
        $query = $this->connection->prepare("SELECT image 
        FROM gpx_all_image WHERE transaction_no = :id AND module = 'booking'");
        $query->execute(array("id" => $trans));
        $result = $query->fetchAll();
        return $result;
    }

}
