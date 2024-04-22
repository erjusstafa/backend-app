<?php
require_once 'models.php';

class Atribute extends Models
{
    public function display()
    {
        return $this->data['id'];
    /*     foreach ($this->data['items'] as $item) {
            echo "😍" . $item['value'];
        } */
    }
}
