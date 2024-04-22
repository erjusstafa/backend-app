<?php
// Require the necessary files
require_once  '../app/services/database.php';
require_once  '../app/models/category.php';
require_once  '../app/models/product.php';
require_once  '../app/models/attribute.php';

$database = new Database();
// Now you can get the database connection and use it for executing queries
$conn = $database->connect();


$json_data = file_get_contents('data.json');
$data = json_decode($json_data, true);
 
// Instantiate models
$categoryModels = [];
foreach ($data['data']['categories'] as $categoryData) {
    $categoryModels[] = new Category($categoryData);
}

$productModels = [];
foreach ($data['data']['products'] as $productData) {
    $productModels[] = new Product($productData);
}



// Display categories
/* echo "Categories:\n";
foreach ($categoryModels as $categoryModel) {
    echo  $categoryModel->display() . "\n";
} */

// Display products
foreach ($productModels as $productModel) {
    echo $productModel->display();
}
