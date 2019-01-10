<?php

class ReservationController extends GenericController
{
    public function __construct()
    {
        parent::__construct();        
    }

    //LIST
    public function list()
    {
        $model = new ReservationModel($this->connection);
        $list = $model->getlist();        
        $columns = array("reservation_no","customer","qty","deposit","assigned_to","reservation_date","status");
        echo $this->twig->render('_generic_component/list.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,
            "list" => $list,            
            "columns" => $columns,     
            "url" => $_SERVER['REQUEST_URI'],                 
            "moduledescription" => "List of all reservation - empty boxes and with box numbers",                 
        ));        
    }

    //EDIT
    public function edit()
    {
        $reservationid = isset($_GET['id']) ? $_GET['id'] : null;        
        $result = null;
        if (isset($reservationid)) {
            $reservation = new ReservationModel($this->connection);
            $result = $reservation->getreservationbyid($reservationid);
        }

        echo $this->twig->render('cargo-management/reservation/edit.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,
            "allshipper" => $this->allshipper,
            "allsalesdriver" => $this->allsalesdriver,
            "allboxtype" => $this->allboxtype,
            "allreservationstatus" => $this->allreservationstatus,            
            "result" => $result,
            "moduledescription" => "Add and updating reservation - empty boxes",                 
        ));
    }

    public function view()
    {
        $reservation_no = isset($_GET['reservation_no']) ? $_GET['reservation_no'] : null;        
        $result = null;
        if (isset($reservation_no)) {
            $reservation = new ReservationModel($this->connection);
            $result = $reservation->getreservationdetails($reservation_no);
            $box_number = $reservation->getboxnumbers($reservation_no);
        }

        echo $this->twig->render('cargo-management/reservation/view.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,         
            "result" => $result,
            "box_number" => $box_number,
        ));
    }

    public function save()
    {
        $list = json_decode(utf8_encode($_POST['list']));        
        $data = json_decode(utf8_encode($_POST['data']));     

        $model = new ReservationModel($this->connection);
        $result = $model->save($data,$list);
        print_r($result);
    }

    public function update(){

        $list = json_decode(utf8_encode($_POST['list']));             
        $data = json_decode(utf8_encode($_POST['data'])); 
        print_r($list);
        $model = new ReservationModel($this->connection);
        $result = $model->update($data,$list);
        print_r($result);
    }
}

