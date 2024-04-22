<?php
require_once 'models.php';


class Category extends Models
{
    public function display()
    {
       return $this->data['name'];
    }
}
