<?php

class UserController extends GenericController
{
    public function __construct()
    {
        parent::__construct();
        require_once __DIR__ . "/../model/UserModel.php";
    }

    //LIST
    public function list()
    {
        $model = new UserModel($this->connection);
        $list = $model->getlist();
        $columns = array("employee_name", "username", "role");
        echo $this->twig->render('_generic_component/list.html', array(
            "logindetails" => $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,
            "list" => $list,
            "columns" => $columns,
            "url" => $_SERVER['REQUEST_URI'],
        ));
    }

    public function edit()
    {
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        $error = isset($_GET['error']) ? $_GET['error'] : null;

        $result = null;
        if (isset($id)) {
            $model = new UserModel($this->connection);
            $result = $model->getuserbyid($id);
        }

        echo $this->twig->render('user-management/user/edit.html', array(
            "logindetails" => $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,
            "allemployee" => $this->allemployee,
            "allrole" => $this->allrole,
            "result" => $result,
            "error" => $error
        ));
    }

    public function save()
    {
        $id = isset($_POST['id']) ? $_POST['id'] : "";
        $result = 0;
        $data = array(
            "username" => (isset($_POST['username']) ? $_POST['username'] : ""),
            "password" => (isset($_POST['pass']) ? $_POST['pass'] : ""),
            "employee_id" => (isset($_POST['employee_id']) ? $_POST['employee_id'] : ""),
            "role_id" => (isset($_POST['role_id']) ? $_POST['role_id'] : ""),
        );
       
        if ($id <> "") {
            $model = new UserModel($this->connection);
            $result = $model->update($data, $id);
        } else {
            $model = new UserModel($this->connection);
            if ($model->checkusername($data['username']) == 0) {
                $model = new GenericModel($this->connection);
                $result = $model->insert($data, USERS);
            }
        }

        if ($result == 1)
            header("Location: index.php?controller=user&action=list");
        else
            header("Location: index.php?controller=user&action=edit&error=true");
    }

}

