<?php
require_once __DIR__ . "/GenericModel.php";

class ChartaccountsModel extends GenericModel
{

    public function __construct($connection)
    {
        parent::__construct($connection);
    }

    //LIST    
    public function getlist()
    {
        $query = $this->connection->prepare("SELECT gc.* , gc.account_chart as name , 
        gct.name as account_type FROM gpx_chartaccounts gc 
        JOIN gpx_chartaccounts_type gct ON gct.id = gc.account_type
        ORDER BY createddate DESC");
        $query->execute();
        $result = $query->fetchAll();
        $this->connection = null;
        return $result;
    }

    public function getchartaccountbyid($id)
    {
        $query = $this->connection->prepare("SELECT * FROM gpx_chartaccounts WHERE id = :id");
        $query->execute(array("id" => $id));
        $result = $query->fetchAll();
        $this->connection = null;
        return $result;
    }

    public function update($id, $data)
    {
        $query = $this->connection->prepare("UPDATE gpx_chartaccounts
        SET account_code = :account_code,
        account_chart = :account_chart ,
        account_type = :account_type
        WHERE id = :id");
        $result = $query->execute(array(
            "id" => $id,
            "account_code" => $data['account_code'],
            "account_chart" => $data['account_chart'],
            "account_type" => $data['account_type'],
        ));
        $this->connection = null;
        return $result;
    }


}
?>