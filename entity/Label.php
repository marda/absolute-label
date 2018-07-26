<?php

namespace Absolute\Module\Label\Entity;

class Label
{

    private $id;
    private $name;
    private $color;
    private $created;
    private $userId;

    public function __construct($id, $userId, $name, $color, $created)
    {
        $this->id = $id;
        $this->userId = $userId;
        $this->name = $name;
        $this->color = $color;
        $this->created = $created;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getColor()
    {
        return $this->color;
    }

    public function getCreated()
    {
        return $this->created;
    }

    // SETTERS
    // ADDERS
    // OTHER METHODS  

    public function toJson()
    {
        return array(
            "id" => $this->id,
            "name" => $this->name,
            "color" => $this->color,
        );
    }

}
