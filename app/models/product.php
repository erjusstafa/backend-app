<?php

use App\Controller\Atribute;
use App\Controller\Database;

require_once '../config/index.php';

class Product extends Database
{
    private $attributeHandler;

    public function __construct()
    {
        parent::__construct(DB_HOST, DB_NAME, DB_USER, DB_PASS);
        $this->attributeHandler = new Atribute();
    }

    //create table for Product with necessary fields
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
        quantity INT NOT NULL DEFAULT 0,

        FOREIGN KEY (category) REFERENCES categories(name)
    )";
        $this->executeData($query);
    }


    //fill product table with data
    public function insertProduct($productsData)
    {
        // Check if product with the same id already exists
        if ($this->productExists($productsData['id'])) {
            return; // Skip insertion
        }
        // Convert gallery data to JSON format
        $galleryJson = json_encode($productsData['gallery'] ?? []);
        $attributeJson = $this->attributeHandler->encodeAttributes($productsData['attributes'] ?? []);
        $priceJson = json_encode($productsData['prices'] ?? []);
        // Insert product into products table
        $query = "INSERT INTO products (id, name, inStock, gallery, description, category, attributes,prices, brand,quantity) VALUES (?, ?, ?, ?, ?, ?,?, ?,?,?)";

        $this->executeData($query, [
            $productsData['id'],
            $productsData['name'],
            $productsData['inStock'],
            $galleryJson,
            $productsData['description'],
            $productsData['category'],
            $attributeJson,
            $priceJson,
            $productsData['brand'],
            $productsData['quantity']

        ]);
    }


    //check if product exist
    private function productExists($productId)
    {
        $query = "SELECT COUNT(*) FROM products WHERE id = ?";
        $stmt = $this->executeData($query, [$productId]);
        $count = $stmt->fetchColumn();
        return $count > 0;
    }

    //get product by category
    public function productByCategory($category)
    {
        if ($category === 'all') {
            // Return all products without filtering by category
            $query = "SELECT * FROM products";
        } else {
            // Filter products by the specified category
            $query = "SELECT * FROM products WHERE category = :category";
        }
        $stmt = $this->conn->prepare($query);

        if ($category !== 'all') {
            $stmt->bindParam(':category', $category);
        }

        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->formatProducts($products);
    }

    //get product by ID
    public function productById($id)
    {
        // Filter products by the specified id
        $query = "SELECT * FROM products WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->formatProducts($products);
    }


    //create order in db
    public function insertNewProduct($product)
    {

        if (isset($product['brand'])) {
            $brand = $product['brand'];
        } else {
            // Handle the case where "brand" key is undefined
            $brand = ""; // Or provide a default value
        }
        $id = $product['id'];
        $name = $product['name'];
        $inStock = $product['inStock'];
        $gallery = json_encode($product['gallery']);
        $description = $product['description'];
        $category = $product['category'];
        $attributes = $this->attributeHandler->encodeAttributes($product['attributes'] ?? []);
        $prices = json_encode($product['prices']);
        $quantity = $product['quantity'];

        // Check if a product with the same attributes already exists
        if ($existingProduct = $this->getProductByAttributes($product)) {
            // Product already exists, update its quantity
            $existingQuantity = $existingProduct['quantity'];
            $quantity += $existingQuantity;
            $query = "UPDATE products SET quantity = :quantity WHERE id = :id";
            $this->executeData($query, [
                ':id' => $existingProduct['id'],
                ':quantity' => $quantity
            ]);
            return $this->productById($existingProduct['id']); // Return the updated product

        } else {
            // Product doesn't exist, insert a new row
            $query = "INSERT INTO products (id, name, inStock, gallery, description, category, attributes, prices, brand, quantity) VALUES (:id, :name, :inStock, :gallery, :description, :category, :attributes, :prices, :brand, :quantity)";
            $this->executeData($query, [
                ':id' => $id,
                ':name' => $name,
                ':inStock' => $inStock,
                ':gallery' => $gallery,
                ':description' => $description,
                ':category' => $category,
                ':attributes' => $attributes,
                ':prices' => $prices,
                ':brand' => $brand,
                ':quantity' => $quantity
            ]);
            return $this->productById($id); // Return the newly inserted product
        }
    }


    private function getProductByAttributes($product)
    {
        $id = $product['id']; // Assuming 'id' is the product identifier

        // Construct a query to retrieve a product with matching attributes
        $query = "SELECT * FROM products WHERE id = :id ";
        $stmt = $this->executeData($query, [
            ':id' => $id,

        ]);

        // Fetch the matching product
        $matchingProduct = $stmt->fetch(PDO::FETCH_ASSOC);

        return $matchingProduct;
    }

    private function formatProducts($products)
    {
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
                'brand' => $item['brand'] ?? '',
                'quantity' => $item['quantity'] ?? 0
            ];
        }, $products);
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
