<?php
namespace OgamePublicApi;

class Planet implements \JsonSerializable {
    private $id;
    private $name;
    private $coords;
    private $moon;
    
    public function __construct($data)
    {
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->coords = $data['coords'];
        $this->moon = $data['moon'];
    }
    public function jsonSerialize() {
        return ["id" => $this->id,"name" => $this->name,"coords" => $this->coords,"moon" => $this->moon];
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
    public function getCoords()
    {
        return $this->coords;
    }
    public function setCoords($coords)
    {
        $this->coords = $coords;
        return $this;
    }
    public function getMoon(){
        return $this->moon;
    }
    public function setMoon($moon)
    {
        $this->moon = $moon;
        return $this;
    }

}