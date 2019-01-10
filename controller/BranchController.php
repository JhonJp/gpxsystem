<?php

class BranchController extends GenericController
{
    public function __construct()
    {
        parent::__construct();
        require_once __DIR__ . "/../model/BranchModel.php";
    }

    //LIST
    function list() {

        $model = new BranchModel($this->connection);
        $list = $model->getlist();        
        $columns = array("name","address","type");
        echo $this->twig->render('_generic_component/list.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,
            "list" => $list,            
            "columns" => $columns,   
            "url" => $_SERVER['REQUEST_URI'],                     
        ));  

    }

    public function edit()
    {
        $alltype = array(
            array(
                "id" => "GP - Branch",
                "name" => "GP - Branch",
            ),
            array(
                "id" => "Partner - Hub",
                "name" => "Partner - Hub",
            ),
            array(
                "id" => "Partner - Area",
                "name" => "Partner - Area",
            ),
        );

        $result = null;
        if(isset($_GET['id'])){
            $model = new BranchModel($this->connection);
            $result = $model->getbranchbyid($_GET['id']);
        }

        echo $this->twig->render('settings/branch/edit.html', array(
            "logindetails" => $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,
            "alltype" => $alltype,
            "result" => $result,
        ));
    }

    public function save(){

        $id = isset($_POST['id']) ? $_POST['id'] : "" ;

        $data = array(
            "type" => (isset($_POST['type']) ? $_POST['type'] : ""),
            "name" => (isset($_POST['name']) ? $_POST['name'] : ""),
            "address" => (isset($_POST['address']) ? $_POST['address'] : 0)
        );
        
        if($id <> ""){
            $model = new BranchModel($this->connection);
            $result = $model->update($id,$data);
        }
        else{
            $model = new GenericModel($this->connection);
            $result = $model->insert($data,"gpx_branch");
        }
        if($result == 1)
            header("Location: index.php?controller=branch&action=list");
    }


}
