<?php
require_once __DIR__ . "/GenericModel.php";

class UserModel extends GenericModel
{

    public function __construct($connection)
    {
        parent::__construct($connection);
    }

    //LIST    
    public function getlist()
    {        
        $query = $this->connection->prepare("SELECT gu.* , gr.name as role ,
        CONCAT(ge.firstname,' ',ge.lastname) as 'employee_name' 
        FROM gpx_users gu 
        JOIN gpx_employee ge ON gu.employee_id = ge.id
        JOIN gpx_role gr ON gr.id = gu.role_id
        ORDER BY ge.firstname ASC
        ");
        $query->execute();    
        $result = $query->fetchAll();
        $this->connection = null; 
        return $result;
    }

    public function getuserbyid($id)
    {        
        $query = $this->connection->prepare("SELECT * FROM gpx_users WHERE id = :id");
        $query->execute(array("id"=>$id));    
        $result = $query->fetchAll();
        $this->connection = null; 
        return $result;
    }

    public function checkusername($username)
    {        
        $query = $this->connection->prepare("SELECT count(*) as cnt FROM gpx_users WHERE username = :username");
        $query->execute(array("username"=>$username));    
        $result = $query->fetchColumn();
        $this->connection = null; 
        return $result;
    }


    public function save($data)
    {        
        $query = $this->connection->prepare("INSERT INTO gpx_users VALUES(:username,:password,:employee_id,:role_id)");
        $result = $query->execute(array(
            "username" => $data['username'],
            "password" => $data['password'],
            "employee_id" => $data['employee_id'],
            "role_id" => $data['role_id'],
        ));    
        $this->connection = null; 
        return $result;
    }


    public function update($data,$id)
    {        
        $query = $this->connection->prepare("UPDATE gpx_users 
        SET 
        username = :username,
        password = :password,
        employee_id = :employee_id,
        role_id = :role_id
        WHERE id = :id");
        $result = $query->execute(array(
            "id"=>$id,
            "username" => $data['username'],
            "password" => $data['password'],
            "employee_id" => $data['employee_id'],
            "role_id" => $data['role_id'],
        ));    
        $this->connection = null; 
        return $result;
    }

}
?>