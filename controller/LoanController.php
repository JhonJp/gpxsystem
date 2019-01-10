<?php

class LoanController extends GenericController
{
    public function __construct()
    {
        parent::__construct();
        require_once __DIR__ . "/../model/LoanModel.php";
    }
    //LIST
    public function list()
    {
        $model = new LoanModel($this->connection);
        $list = $model->getlist();
        $columns = array("loan_no", "employee", "amount", "term", "interest", "loan_date", "approved_date", "status");
        echo $this->twig->render('_generic_component/list.html', array(
            "logindetails" => $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,
            "list" => $list,
            "columns" => $columns,
        ));
    }

    public function edit()
    {
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        $result = null;
        $allterm = array(
            array(
                "id" => "3",
                "name" => "3 Months"
            ),
            array(
                "id" => "6",
                "name" => "6 Months"
            ),
            array(
                "id" => "12",
                "name" => "12 Months"
            )
        );
        $allstatus = array(
            array(
                "id" => "Pending",
                "name" => "Pending"
            ),
            array(
                "id" => "Approved",
                "name" => "Approved"
            ),
            array(
                "id" => "Paid",
                "name" => "Paid"
            ),
            array(
                "id" => "Unpaid",
                "name" => "Unpaid"
            )
        );

        if (isset($id)) {
            $model = new LoanModel($this->connection);
            $result = $model->getloanbyid($id);
        }

        echo $this->twig->render('finance-management/loan/edit.html', array(
            "logindetails" => $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,
            "allemployee" => $this->allemployee,
            "allterm" => $allterm,
            "allstatus" => $allstatus,
            "result" => $result
        ));

    }

    public function history()
    {
        $result = null;
        $loan_no = isset($_GET['id']) ? $_GET['id'] : null;
        if (isset($loan_no)) {
            $model = new LoanModel($this->connection);
            $result = $model->gethistory($loan_no);
        }
        echo $this->twig->render('finance-management/loan/history.html', array(
            "logindetails" => $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,
            "result" => $result
        ));
    }

    public function updatepayment()
    {
        $result = null;
        $id = isset($_POST['id']) ? $_POST['id'] : null;
        $loanno = isset($_POST['loanno']) ? $_POST['loanno'] : null;
        $model = new LoanModel($this->connection);
        $result = $model->updatePayment($id,$loanno);
        header("Location: index.php?controller=loan&action=history&id=" . $loanno);
    }

    public function save()
    {
        $id = (isset($_POST['loanid']) ? $_POST['loanid'] : "");
        $loanno = (isset($_POST['loanno']) ? $_POST['loanno'] : "");

        $result = 0;
        $data = array(
            "loan_no" => ($loanno <> "") ? $loanno : "L" . $this->current_userid . date('YmdHis'),
            "employee_id" => (isset($_POST['employee_id']) ? $_POST['employee_id'] : ""),
            "amount" => (isset($_POST['amount']) ? $_POST['amount'] : ""),
            "term" => (isset($_POST['term']) ? $_POST['term'] : ""),
            "interest" => (isset($_POST['interest']) ? $_POST['interest'] : ""),
            "loan_date" => (isset($_POST['loan_date']) ? $_POST['loan_date'] : getdate('Y-m-d')),
            "approved_date" => (isset($_POST['approved_date']) ? $_POST['approved_date'] : ""),
            "status" => (isset($_POST['status']) ? $_POST['status'] : ""),
            "description" => (isset($_POST['description']) ? $_POST['description'] : ""),
            "created_by" => $this->current_userid
        );

        if ($id <> "") {
            $model = new LoanModel($this->connection);
            $result = $model->update($data, $id,$loanno);
        } else {
            $model = new LoanModel($this->connection);
            $result = $model->save($data);
        }

        if ($result == 1)
            header("Location: index.php?controller=loan&action=list");

    }

}

