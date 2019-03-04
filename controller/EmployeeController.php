<?php

class EmployeeController extends GenericController
{
    public function __construct()
    {
        parent::__construct();
        require_once __DIR__ . "/../model/EmployeeModel.php";
    }

    //LIST
    public function list()
    {
        $model = new EmployeeModel($this->connection);
        $list = $model->getlist();   
        $columns = array("name","position","email","mobile", "branch");
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
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        $result = null;     
        if (isset($id)) {
            $model = new EmployeeModel($this->connection);
            $result = $model->getemployeebyid($id);
        }
        echo $this->twig->render('user-management/employee/edit.html', array(
            "logindetails" =>  $_SESSION['logindetails'],
            "breadcrumb" => $this->breadcrumb,
            "allbranch" => $this->allbranch,
            "result" => $result
        ));
    }

    public function save()
    {
        $id = isset($_POST['id']) ? $_POST['id'] : null;

        $data = array(
            "firstname" => (isset($_POST['firstname']) ? $_POST['firstname'] : ""),
            "lastname" => (isset($_POST['lastname']) ? $_POST['lastname'] : ""),
            "middlename" => (isset($_POST['middlename']) ? $_POST['middlename'] : ""),
            "birthdate" => (isset($_POST['birthdate']) ? $_POST['birthdate'] : null),
            "mobile" => (isset($_POST['mobile']) ? $_POST['mobile'] : ""),
            "position" => (isset($_POST['position']) ? $_POST['position'] : ""),
            "phone" => (isset($_POST['phone']) ? $_POST['phone'] : ""),
            "gender" => (isset($_POST['gender']) ? $_POST['gender'] : ""),            
            "email" => (isset($_POST['email']) ? $_POST['email'] : ""),         
            "fathersname" => (isset($_POST['fathersname']) ? $_POST['fathersname'] : ""),
            "mothersname" => (isset($_POST['mothersname']) ? $_POST['mothersname'] : ""),
            "civilstatus" => (isset($_POST['civilstatus']) ? $_POST['civilstatus'] : ""),
            "religion" => (isset($_POST['religion']) ? $_POST['religion'] : ""),
            "branch" => (isset($_POST['branch']) ? $_POST['branch'] : ""),            
            "house_number_street" => (isset($_POST['house_number_street']) ? $_POST['house_number_street'] : ""),            
            "barangay" => (isset($_POST['barangay']) ? $_POST['barangay'] : ""),
            "city" => (isset($_POST['city']) ? $_POST['city'] : ""),

            "elementary" => (isset($_POST['elementary']) ? $_POST['elementary'] : ""),
            "elementaryyeargraduated" => (isset($_POST['elementaryyeargraduated']) ? $_POST['elementaryyeargraduated'] : ""),
            "highschool" => (isset($_POST['highschool']) ? $_POST['highschool'] : ""),
            "highschooleargraduated" => (isset($_POST['highschooleargraduated']) ? $_POST['highschooleargraduated'] : ""),
            "college" => (isset($_POST['college']) ? $_POST['college'] : ""),
            "collegeyeargraduated" => (isset($_POST['collegeyeargraduated']) ? $_POST['collegeyeargraduated'] : ""),

            "company1" => (isset($_POST['company1']) ? $_POST['company1'] : ""),
            "position1" => (isset($_POST['position1']) ? $_POST['position1'] : ""),
            "date_from1" => (isset($_POST['date_from1']) ? $_POST['date_from1'] : ""),
            "date_to1" => (isset($_POST['date_to1']) ? $_POST['date_to1'] : ""),

            "company2" => (isset($_POST['company2']) ? $_POST['company2'] : ""),
            "position2" => (isset($_POST['position2']) ? $_POST['position2'] : ""),
            "date_from2" => (isset($_POST['date_from2']) ? $_POST['date_from2'] : ""),
            "date_to2" => (isset($_POST['date_to2']) ? $_POST['date_to2'] : ""),
            "createdby" => $this->current_userid
        );

        if ($id <> "") {
            //UPDATE
            $model = new EmployeeModel($this->connection);
            $result = $model->update($data,$id);
        } else {
            //INSERT
            $model = new EmployeeModel($this->connection);
            $result = $model->insert($data,"gpx_employee");
        }
        
        print_r($result);
    }


    public function saveimg()
    {
            $target_dir = "libraries/images/uploads/";
            $target_file = $target_dir . basename(date("Ymd")."-".$_SESSION['logindetails'][0]['id']."-".$_FILES["fileToUpload"]["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
            // Check if image file is a actual image or fake image
            if(isset($_POST["submit"])) {
                $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
                if($check !== false) {
                    echo "File is an image - " . $check["mime"] . ".";
                    $uploadOk = 1;
                } else {
                    echo "File is not an image.";
                    $uploadOk = 0;
                }
            }
            // Check if file already exists
            if (file_exists($target_file)) {
                echo "Sorry, file already exists.";
                $uploadOk = 0;
            }
            
            // Allow certain file formats
            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif" ) {
                echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                $uploadOk = 0;
            }
            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 0) {
            // if everything is ok, try to upload file
            } else {
                if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                    $img = date("Ymd")."-".$_SESSION['logindetails'][0]['id']."-".$_FILES["fileToUpload"]["name"];
                    $model = new EmployeeModel($this->connection);
                    $result = $model->updateImageOnly($img,$_SESSION['logindetails'][0]['id']);
                    header("Location:index.php?controller=index&action=logout");
                }
            }
    }
}

