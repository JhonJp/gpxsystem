<?php

class BoxtypeController extends GenericController
{
    public function __construct()
    {
        parent::__construct();
        require_once __DIR__ . "/../model/BoxTypeModel.php";
    }

    public function list()
    {
        $model = new BoxTypeModel($this->connection);
        $list = $model->getlist();        
        $columns = array("name","l_w_h","depositprice","description");
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
        $result = null;
        if(isset($_GET['id'])){
            $model = new BoxTypeModel($this->connection);
            $result = $model->getboxtypebydid($_GET['id']);
        }
        echo $this->twig->render('settings/boxtype/edit.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,
            "result" => $result
        ));
    }

    public function save(){

        $id = isset($_POST['id']) ? $_POST['id'] : "" ;

        $data = array(
            "name" => (isset($_POST['boxtype']) ? $_POST['boxtype'] : ""),
            "depositprice" => (isset($_POST['depositprice']) ? $_POST['depositprice'] : ""),
            "size_length" => (isset($_POST['size_length']) ? $_POST['size_length'] : 0),
            "size_width" => (isset($_POST['size_width']) ? $_POST['size_width'] : 0),
            "size_height" => (isset($_POST['size_height']) ? $_POST['size_height'] : 0),
            "description" => (isset($_POST['description']) ? $_POST['description'] :  $_POST['boxtype']),
            "nsb" => (isset($_POST['nsbtype']) ? $_POST['nsbtype'] :  $_POST['nsbtype']),
        );
        
        if($id <> ""){
            $model = new BoxTypeModel($this->connection);
            $result = $model->update($id,$data);
        }
        else{
            $model = new GenericModel($this->connection);
            $result = $model->insert($data,BOXTYPE);
        }
        if($result == 1)
            header("Location: index.php?controller=boxtype&action=list");
    }


}

