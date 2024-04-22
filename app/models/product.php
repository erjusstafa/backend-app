<?php
require_once 'models.php';
require_once 'attribute.php';
class Product extends Models
{
    public function display()
    {
        $output = $this->data['id'] . "\n";
        $output .= $this->data['name'] . "\n";
        $output .= $this->data['inStock'] . "\n";
        foreach ($this->data['gallery'] as $img) {
            $output .=  $img;
        }
        $output .= "Description: " . $this->data['description'] . "\n";
        $output .= "Category: " . $this->data['category'] . "\n";
        $output .= "Price: " . $this->data['prices'][0]['currency']['symbol'] . $this->data['prices'][0]['amount'] . "\n";
        $output .= "Brand: " . $this->data['brand'] . "\n";
        foreach ($this->data['attributes'] as $item) {
            $attr = new Atribute($item);
            $output .= $attr->display();
        }
        return $output;
    }
}
