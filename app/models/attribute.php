<?php

namespace App\Controller;

use App\Controller\Database;
use PDOException;
 
class Atribute  
{

   /*  public function createTable()
    {
        $query = "CREATE TABLE IF NOT EXISTS attributes (
          id VARCHAR(25) PRIMARY KEY,
          items JSON,
          name VARCHAR(50),
          type  VARCHAR(25),
      )";
        $this->executeData($query);
    }


    function addAttribute($attrItem)
    {
        $query = "INSERT INTO attributes (id,items, name, type) VALUES (?,  ?, ?, ?)";
        $this->executeData($query, [
            ':id' => $attrItem['id'],
            ':items' => json_encode($attrItem['items']),
            ':name' => $attrItem['name'],
            ':type' => $attrItem['type'],
        ]);
    } */

   /* public function executeData($query)
    {
        try {
            $statement = $this->conn->prepare($query);
            return $statement;
        } catch (PDOException $e) {
            die("Query failed: " . $e->getMessage());
        }
    }   */
}
