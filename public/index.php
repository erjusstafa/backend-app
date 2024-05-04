
<?php

use App\Controller\Database;
use App\Controller\GraphQL;

require_once  '../app/services/database.php';
require_once  '../app/models/category.php';
require_once  '../app/models/product.php';
require_once  '../app/models/attribute.php';
require_once '../vendor/autoload.php';
$conf = require_once '../config/index.php';

$categories = new Category($conf['host'], $conf['database'], $conf['username'], $conf['pass']);
$categories->createTable(); //create table for categories
$products = new Product($conf['host'], $conf['database'], $conf['username'], $conf['pass']);
$products->createTable(); //create table for products
$json_data = file_get_contents('data.json');
$data = json_decode($json_data, true);


//populate with data categoriesa  && products table
foreach ($data['data']['categories'] as $categoryData) {
    $categories->insertCategory($categoryData['name']);
}

foreach ($data['data']['products'] as $productsData) {
    $products->insertProduct($productsData);
}


$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    $r->post('/graphql', [App\Controller\GraphQL::class, 'handle']);
});

$routeInfo = $dispatcher->dispatch(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI']
);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        $result = GraphQL::handle($conf);
        echo $result;
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        echo "graphql connected";
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        echo $handler($vars);
        $result = GraphQL::handle($conf);
        echo $result;

        break;
}
