
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
      echo "Data inserted successfully";
    }
  }
  public function getAllCategories()
  {
    $query = "SELECT name FROM categories";
    $statement = $this->executeData($query);
    $categories = $statement->fetchAll(PDO::FETCH_ASSOC);
    return $categories;
  }
}
