<?php
namespace OgamePublicApi;

class Player  implements \JsonSerializable{

    private $id;
    private $name;
    private $status;
    private $alliance;
    private $data;

    public function __construct($data)
    {
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->status = $data['status'];
        $this->alliance = $data['alliance'];
        $this->data = $data['data'];
        
    }
    public function jsonSerialize() {
        return ["id" => $this->id,"name" => $this->name,"status" => $this->status,"alliance" => $this->alliance,"data" => $this->data];
    }

    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
    public function getName()
    {
        return $this->name;
    }
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    public function getStatus()
    {
        return $this->status;
    }
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }
    public function getAlliance()
    {
        return $this->alliance;
    }
    public function setAlliance($alliance)
    {
        $this->alliance = $alliance;
        return $this;
    }
    public function getData()
    {
        return $this->data;
    }
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }
}

?>