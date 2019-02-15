<?php

class BookingController extends GenericController{
    
    public function __construct() {
        parent::__construct(); 
        require_once  __DIR__ . "/../model/BookingModel.php";    
    }

    //LIST
    public function list()
    {
        $model = new BookingModel($this->connection);
        $list = $model->getlist();            
        $columns = array("book_date","transaction_no","branch","customer","status","qty");
        echo $this->twig->render('_generic_component/list.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,
            "list" => $list,            
            "columns" => $columns,   
            "moduledescription" => "List of all booking (pick-up) - from reservation and direct booking(nsb)",                                   
        ));        
    }

    public function view()
    {        
        $transaction_no = isset($_GET['transaction_no']) ? $_GET['transaction_no'] : null;
        $model = new BookingModel($this->connection);
        $result = $model->getbookingdetails($transaction_no); 
        $box_numbers = $model->getbookingboxnumber($transaction_no);
        $image = $model->getimages($transaction_no); 

        echo $this->twig->render('cargo-management/booking/view.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,     
            "result" => $result,
            "images" => $image,     
            "box_numbers" => $box_numbers,     
        ));
    }
    

}

