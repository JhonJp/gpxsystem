<?php
require_once __DIR__ . "/GenericModel.php";

class EmployeeModel extends GenericModel
{

    public function __construct($connection)
    {
        parent::__construct($connection);
    }

    public function getlist()
    {
        $query = $this->connection->prepare("SELECT ge.* , 
        CONCAT(ge.firstname, ' ', ge.lastname) as name  , gb.name as branch 
        FROM gpx_employee ge 
        LEFT JOIN gpx_branch gb ON ge.branch = gb.id
        ORDER BY ge.firstname");
        $query->execute();
        $result = $query->fetchAll();
        $this->connection = null;
        return $result;
    }

    public function getemployeebyid($id)
    {
        $query = $this->connection->prepare("SELECT * FROM gpx_employee WHERE id = :id");
        $query->execute(array("id" => $id));
        $result = $query->fetchAll();
        $this->connection = null;
        return $result;
    }

    public function updateImageOnly($img, $id)
    {
        $query = $this->connection->prepare("UPDATE gpx_employee SET image = :img WHERE id = :id");
        $query->execute(
            array(
                "id" => $id,
                "img" => $img
            ));
        return $result;
    }


    public function update($data,$id){
        print_r($data);
        //INSERT
        $query = $this->connection->prepare("UPDATE gpx_employee SET  
        firstname = :firstname,
        lastname = :lastname,
        middlename = :middlename,
        mobile = :mobile,
        phone = :phone,
        email = :email,
        gender = :gender,
        birthdate = :birthdate,
        house_number_street = :house_number_street,
        barangay = :barangay,
        city = :city,
        fathersname = :fathersname,
        mothersname = :mothersname,
        civilstatus = :civilstatus,
        religion = :religion,
        branch = :branch,
        position = :position,

        elementary = :elementary,
        elementaryyeargraduated = :elementaryyeargraduated,
        highschool = :highschool,
        highschooleargraduated = :highschooleargraduated,
        college = :college,
        collegeyeargraduated = :collegeyeargraduated,        

        company1 = :company1,
        position1 = :position1,
        date_from1 = :date_from1,
        date_to1 = :date_to1,
        company2 = :company2,
        position2 = :position2,
        date_from2 = :date_from2,
        date_to2 = :date_to2,
        WHERE id = :id");
        
        $result = $query->execute(array(
            "firstname" => $data['firstname'],
            "lastname" => $data['lastname'],
            "middlename" => $data['middlename'],
            "mobile" => $data['mobile'],
            "phone" => $data['phone'],
            "email" => $data['email'],
            "gender" => $data['gender'],
            "birthdate" => $data['birthdate'],
            "house_number_street" => $data['house_number_street'],
            "barangay" => $data['barangay'],
            "city" => $data['city'],
            "fathersname" => $data['fathersname'],
            "mothersname" => $data['mothersname'],
            "civilstatus" => $data['civilstatus'],
            "religion" => $data['religion'],
            "branch" => $data['branch'],
            "position" => $data['position'],

            "elementary" => $data['elementary'],
            "elementaryyeargraduated" => $data['elementaryyeargraduated'],
            "highschool" => $data['highschool'],
            "highschooleargraduated" => $data['highschooleargraduated'],
            "college" => $data['college'],
            "collegeyeargraduated" => $data['collegeyeargraduated'],

            "company1" => $data['company1'],
            "position1" => $data['position1'],
            "date_from1" => $data['date_from1'],
            "date_to1" => $data['date_to1'],

            "company2" => $data['company2'],
            "position2" => $data['position2'],
            "date_from2" => $data['date_from2'],
            "date_to2" => $data['date_to2'],

            "id" => $id

        ));
        return $result;
    }

}
?>