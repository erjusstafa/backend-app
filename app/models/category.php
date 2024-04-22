<?php
require_once 'models.php';


class Category extends Models
{

    /* public function __construct($name)
    {
        parent::__construct($this->name);
        $this->name = $name;
    } */

    public function display()
    {
       return $this->data['name'];
    }
}
