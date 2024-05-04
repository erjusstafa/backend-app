
<?php

use App\Controller\Database;

class Category extends Database
{

  public function createTable()
  {
    $query = "CREATE TABLE IF NOT EXISTS categories (
                 name VARCHAR(50) PRIMARY KEY
              )";
    $this->executeData($query);
  }


  public function categoryExists($name)
  {
    $query = "SELECT COUNT(*) FROM categories WHERE name = :name";
    $statement = $this->executeData($query, [':name' => $name]);
    $count = $statement->fetchColumn();
    return $count > 0;
  }

  public function insertCategory($name)
  {
    if ($this->categoryExists($name)) {
      return;
    } else {
      $query = "INSERT INTO categories (name) VALUES (:name)";
      $this->executeData($query, [':name' => $name]);
    }
  }
  public function getAllCategories()
  {
    $query = "SELECT name FROM categories";
    $stmt = $this->executeData($query);
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return array_map(function ($item) {
      return [
        'name' => $item['name'] ?? '',
      ];
    }, $categories);
  }


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
}
