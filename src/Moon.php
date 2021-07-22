<?php
namespace OgamePublicApi;

class Moon implements \JsonSerializable
{
    private $id;
    private $name;
    private $size;

    public function __construct($data){
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->size = $data['size'];
    }

    public function jsonSerialize() {
        return ["id" => $this->id,"name" => $this->name,"size" => $this->size];
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
    public function getSize()
    {
        return $this->size;
    }
    public function setSize($size)
    {
        $this->size = $size;
        return $this;
    }
    
}