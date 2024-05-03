<?php

use App\Controller\Database;

require_once 'attribute.php';



class Product extends Database
{
    public function executeData($query, $params = [])
    {
        
        
        try {
            $statement = $this->conn->prepare($query);
            $statement->execute($params);
            return $statement;
        } catch (PDOException $e) {
            die("Query failed: " . $e->getMessage());
        }
    }
        /* 
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
        return $output; */

}
