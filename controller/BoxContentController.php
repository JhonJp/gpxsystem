<?php

class BoxContentController extends GenericController
{
    public function __construct()
    {
        parent::__construct();
        require_once __DIR__ . "/../model/BoxContentModel.php";
    }

    //LIST
    public function list()
    {
        $model = new BoxContentModel($this->connection);
        $list = $model->getdescriptions();         
        $columns = array("name","description");
        $moduledescription = "List of box contents";
        echo $this->twig->render('_generic_component/list.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,
            "list" => $list,            
            "columns" => $columns,           
            "url" => $_SERVER['REQUEST_URI'],
            "moduledescription" => $moduledescription                                            
        ));       
    }

    public function edit()
    {
        $result = null;
        if(isset($_GET['id'])){
            $model = new BoxContentModel($this->connection);
            $result = $model->getcontentbyid($_GET['id']);
        }
        echo $this->twig->render('settings/boxcontent/edit.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,
            "result" => $result
        ));
    }

    public function save()
    {
        $id = isset($_POST['id']) ? $_POST['id'] : "" ;

        $data = array(
            "name" => (isset($_POST['name']) ? $_POST['name'] : ""),
            "description" => (isset($_POST['description']) ? $_POST['description'] : ""),
        );
        
        if($id <> ""){
            $model = new BoxContentModel($this->connection);
            $result = $model->update($id,$data);
        }
        else{
            $model = new GenericModel($this->connection);
            $result = $model->insert($data,"gpx_boxcontents");
        }
        if($result == 1)
            header("Location: index.php?controller=boxcontent&action=list");
    }

}

