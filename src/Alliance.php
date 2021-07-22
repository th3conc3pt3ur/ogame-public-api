<?php
namespace OgamePublicApi;

class Alliance {
    private $id;
    private $name;
    private $tag;
    private $founder;
    private $foundDate;
    private $homepage;
    private $logo;
    private $open;

    /**
     * Player[]
     */
    private $players;

    /**
     * @param $data xmlDom ?
     * @return Alliance
     */
    public function __construct($data)
    {
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->tag = $data['tag'];
        $this->founder = $data['founder'];
        $this->foundDate = $data['foundDate'];
        $this->homePage = $data['homePage'];
        $this->logo = $data['logo'];
        $this->open = $data['open'];
        $this->players = $data['players'];
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
    public function getTag()
    {
        return $this->tag;
    }   
    public function setTag($tag)
    {
        $this->tag = $tag;
        return $this;
    }
    public function getFounder()
    {
        return $this->founder;
    }
    public function setFounder($founder)
    {
        $this->founder=$founder;
        return $this;
    }
    public function getFoundDate()
    {
        return $this->foundDate;
    }
    public function setFoundDate($foundDate)
    {
        $this->foundDate = $foundDate;
        return $this;
    }
    public function getHomepage()
    {
        return $this->homepage;
    }
    public function setHomepage($homepage)
    {
        $this->homepage = $homepage;
        return $this;
    }
    public function getLogo()
    {
        return $this->logo;
    }
    public function setLogo($logo)
    {
        $this->logo = $logo;
        return $this;
    }
    public function getOpen()
    {
        return $this->open;
    }
    public function setOpen($open)
    {
        $this->open = $open;
        return $this;
    }
}
