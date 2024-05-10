<?php

use App\Controller\Atribute;
use App\Controller\Database;

class Product extends Database
{
    public function createTable()
    {
        $query = "CREATE TABLE IF NOT EXISTS products (
        id VARCHAR(25) PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        inStock BOOLEAN,
        description TEXT,
        category VARCHAR(50),
        brand VARCHAR(255),
        gallery JSON,        
        attributes JSON,    
        prices JSON ,       
        FOREIGN KEY (category) REFERENCES categories(name)
    )";
        $this->executeData($query);
    }


    public function insertProduct($productsData)
    {

        // Check if product with the same id already exists
        if ($this->productExists($productsData['id'])) {
            return; // Skip insertion
        }

        // Convert gallery data to JSON format
        $galleryJson = json_encode($productsData['gallery'] ?? []);
        $attributeJson = json_encode($productsData['attributes'] ?? []);
        $priceJson = json_encode($productsData['prices'] ?? []);


        $this->encodeAttributes($attributeJson);

        // Insert product into products table
        $query = "INSERT INTO products (id, name, inStock, gallery, description, category, attributes,prices, brand) VALUES (?, ?, ?, ?, ?, ?,?, ?,?)";

        $this->executeData($query, [
            $productsData['id'],
            $productsData['name'],
            $productsData['inStock'],
            $galleryJson,
            $productsData['description'],
            $productsData['category'],
            $attributeJson,
            $priceJson,
            $productsData['brand']
        ]);
    }

    private function productExists($productId)
    {
        $query = "SELECT COUNT(*) FROM products WHERE id = ?";
        $stmt = $this->executeData($query, [$productId]);
        $count = $stmt->fetchColumn();
        return $count > 0;
    }


    public  function encodeAttributes($attributesData)
    {
        $attributes = new Atribute('localhost', 'test5', 'root', '');
        return $attributes->encodeAttributes($attributesData);
    }
    /* 
    public function getAllProducts()
    {
        $query = "SELECT * FROM products";
        $stmt = $this->executeData($query);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(function ($item) {
            return [
                'id' => $item['id'] ?? '',
                'name' => $item['name'] ?? '',
                'inStock' => $item['inStock'] ?? false,
                'gallery' => json_decode($item['gallery'], true) ?? [],
                'description' => $item['description'] ?? '',
                'category' => $item['category'] ?? '',
                'attributes' => json_decode($item['attributes'], true) ?? [],
                'prices' => json_decode($item['prices'], true) ?? [],
                'brand' => $item['brand'] ??  ''

            ];
        }, $products);
    } */

    public function productByCategory($category)
    {
        if ($category === 'all') {
            // Return all products without filtering by category
            $query = "SELECT * FROM products";
            $stmt = $this->conn->prepare($query);
        } else {
            // Filter products by the specified category
            $query = "SELECT * FROM products WHERE category = :category";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':category', $category);
        }
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($item) {
            return [
                'id' => $item['id'] ?? '',
                'name' => $item['name'] ?? '',
                'inStock' => $item['inStock'] ?? false,
                'gallery' => json_decode($item['gallery'], true) ?? [],
                'description' => $item['description'] ?? '',
                'category' => $item['category'] ?? '',
                'attributes' => json_decode($item['attributes'], true) ?? [],
                'prices' => json_decode($item['prices'], true) ?? [],
                'brand' => $item['brand'] ?? ''
            ];
        }, $products);
    }



    public function productById($id)
    {

        // Filter products by the specified id
        $query = "SELECT * FROM products WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($item) {
            return [
                'id' => $item['id'] ?? '',
                'name' => $item['name'] ?? '',
                'inStock' => $item['inStock'] ?? false,
                'gallery' => json_decode($item['gallery'], true) ?? [],
                'description' => $item['description'] ?? '',
                'category' => $item['category'] ?? '',
                'attributes' => json_decode($item['attributes'], true) ?? [],
                'prices' => json_decode($item['prices'], true) ?? [],
                'brand' => $item['brand'] ?? ''
            ];
        }, $products);
    }

    public function updateProduct($id, $name)
    {

        $query = "UPDATE products SET name = :name WHERE id = :id ";
        $stmt =  $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':id', $id);
        $result = $stmt->execute();
        return $result;
    }

    public function insertNewProduct($product)
    {

        $id = $product['id'];
        $name = $product['name'];
        $inStock = $product['inStock'];
        $gallery = json_encode($product['gallery']);
        $description = $product['description'];
        $category = $product['category'];
        $attributes = json_encode($product['attributes']);
        $prices = json_encode($product['prices']);
        $brand = $product['brand'];

        $query = "INSERT INTO products (id, name, inStock, gallery, description, category, attributes, prices, brand) VALUES (:id, :name, :inStock, :gallery, :description, :category, :attributes, :prices, :brand)";
        $this->executeData($query, [
            ':id' => $id,
            ':name' => $name,
            ':inStock' =>  $inStock,
            ':gallery' => $gallery,
            ':description' => $description,
            ':category' => $category,
            ':attributes' => $attributes,
            ':prices' => $prices,
            ':brand' => $brand,
        ]);

        return true;
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
