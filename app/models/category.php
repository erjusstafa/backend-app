<?php
require_once 'models.php';


class Category extends Models
{
    public function display()
    {
       
         $output = $this->data['name'] . "\n";
         $output .= $this->data['__typename'] . "\n";

       return $output;
    }
}
