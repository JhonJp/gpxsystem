<?php
require_once __DIR__ . "/GenericModel.php";

class WarehouseInventoryModel extends GenericModel
{

    public function __construct($connection)
    {
        parent::__construct($connection);
    }

    public function getlist()
    {
        $query = $this->connection->prepare("SELECT gwi.id, 
        gw.name as warehouse_name ,
        gwi.manufacturer_name ,
        GROUP_CONCAT(CONCAT(gb.name,' ',gwi.quantity)) as box_type_and_quantity

        FROM gpx_warehouse_inventory gwi
        JOIN gpx_warehouse gw ON gw.id = gwi.warehouse_id
        JOIN gpx_boxtype gb ON gb.id = gwi.boxtype_id
        GROUP BY 
        gw.name,
        gwi.manufacturer_name
        ");
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }

    public function getlist2()
    {
        $query = $this->connection->prepare("SELECT gwi.id, gw.name as warehouse_name , gwi.manufacturer_name ,
        GROUP_CONCAT(gb.name) as box_type,
        GROUP_CONCAT(gwi.quantity) as quantity,
        gwi.createdby, gwi.createddate
        FROM gpx_warehouse_inventory gwi
        JOIN gpx_warehouse gw ON gw.id = gwi.warehouse_id
        JOIN gpx_boxtype gb ON gb.id = gwi.boxtype_id
        GROUP BY gw.name,gwi.manufacturer_name
        ");
        $query->execute();
        $result = $query->fetchAll();
        $this->connection = null;
        return $result;
    }

    public function apisave($data)
    {
        try {
            $countdata = count($data['data']);

            for ($x = 0; $x < $countdata; $x++) {

                $id = $data['data'][$x]['id'];
                
                $check = $this->checkdata($id);

                if (count($check) != 0) {
                    $query = $this->connection->prepare("UPDATE gpx_warehouse_inventory
                    SET
                    quantity = :quantity
                    WHERE id = :id");

                    $result = $query->execute(array(
                        "id" => $data['data'][$x]['id'],
                        "quantity" => $data['data'][$x]['quantity'],
                    ));
                } else {
                    $query = $this->connection->prepare("INSERT INTO gpx_warehouse_inventory(
                    id,warehouse_id,manufacturer_name,boxtype_id,quantity,createddate,createdby,price)
                    VALUES (:id,:warehouse_id,:manufacturer_name,:boxtype_id,:quantity,:createddate,:createdby,:price)");
                    $result = $query->execute(array(
                        "id" => $data['data'][$x]['id'],
                        "warehouse_id" => $data['data'][$x]['warehouse_id'],
                        "manufacturer_name" => $data['data'][$x]['manufacturer_name'],
                        "boxtype_id" => $data['data'][$x]['boxtype_id'],
                        "quantity" => $data['data'][$x]['quantity'],
                        "createddate" => $data['data'][$x]['createddate'],
                        "createdby" => $data['data'][$x]['createdby'],
                        "price" => $data['data'][$x]['price'],
                    ));

                    $countimage = count($data['data'][$x]['purchase_order']);
                    for ($image = 0; $image < $countimage; $image++) {

                        $query2 = $this->connection->prepare("INSERT INTO gpx_all_image(module,transaction_no,image) VALUES (:module,:trans,:image)");
                        $result = $query2->execute(array(
                            "module" => $data['data'][$x]['purchase_order'][$image]['module'],
                            "trans" => $data['data'][$x]['id'],                            
                            "image" => $data['data'][$x]['purchase_order'][$image]['image'],                        
                        ));
                    }

                    $query = $this->connection->prepare("INSERT INTO gpx_warehouse_inventory_report(
                        id,warehouse_id,manufacturer_name,boxtype_id,quantity,createddate,createdby)
                        VALUES (:id,:warehouse_id,:manufacturer_name,:boxtype_id,:quantity,:createddate,:createdby)");
                        $result = $query->execute(array(
                            "id" => $data['data'][$x]['id'],
                            "warehouse_id" => $data['data'][$x]['warehouse_id'],
                            "manufacturer_name" => $data['data'][$x]['manufacturer_name'],
                            "boxtype_id" => $data['data'][$x]['boxtype_id'],
                            "quantity" => $data['data'][$x]['quantity'],
                            "createddate" => $data['data'][$x]['createddate'],
                            "createdby" => $data['data'][$x]['createdby'],
                        ));
                }
            }
        } catch (Exception $e) {
            $this->error_logs("WarehouseInventory - apisave", $e->getmessage());
        }
    }

    public function checkdata($id)
    {
        $query = $this->connection->prepare("SELECT * FROM gpx_warehouse_inventory WHERE id = :id");
        $query->execute(array("id" => $id));
        $result = $query->fetchAll();
        return $result;
    }

}
