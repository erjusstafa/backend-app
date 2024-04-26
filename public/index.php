<?php
// Require the necessary files

use App\Controller\GraphQL;

require_once  '../app/services/database.php';
require_once  '../app/models/category.php';
require_once  '../app/models/product.php';
require_once  '../app/models/attribute.php';
require_once '../vendor/autoload.php';


// Now you can get the database connection and use it for executing queries
$database = new Database();
$conn = $database->connect();

//get data from json file 
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

// Display categories && products
/* echo "Categories:\n";
foreach ($categoryModels as $categoryModel) {
    echo  $categoryModel->display() . "\n";
}  
echo "Products:\n";
  foreach ($productModels as $productModel) {
    echo $productModel->display();
} 
 */



/* $dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    $r->post('/graphql', [app\controller\GraphQLTest::class, 'handle']);
}); */

$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    $r->post('/graphql', [app\controller\GraphQL::class, 'handle']);
});


$routeInfo = $dispatcher->dispatch(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI']
);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        $result = GraphQL::handle($conn);
        echo $result;
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        echo "test2";

        // ... 405 Method Not Allowed
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        echo $handler($vars);
        echo "test3";

        break;
}
