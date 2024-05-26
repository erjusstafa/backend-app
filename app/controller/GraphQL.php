<?php

namespace App\Controller;

require_once 'types.php';
require_once 'inputType.php';
require_once '../config/index.php';

use App\Controller\Types;

use Category;
use GraphQL\GraphQL as GraphQLBase;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use PDO;
use Throwable;
use GraphQL\Type\SchemaConfig;
use Product;
use RuntimeException;

class GraphQL
{

    static public function handle()
    {
        try {

            $queryType = new ObjectType([
                'name' => 'Query',
                'fields' => [
                    'categories' => [
                        'type' => Type::listOf(Types::CategoriesType()),
                        'resolve' => function ($root, $args, $context) {
                            $categories = new Category(DB_HOST, DB_NAME, DB_USER, DB_PASS);
                            return $categories->getAllCategories();
                        },
                    ],

                    'productsByCategory' => [
                        'type' =>   Type::listOf(Types::ProductsType()), // Return single of products
                        'args' => [
                            'category' => Type::string()
                        ],
                        'resolve' => function ($root, $args) {
                            $category = $args['category'];
                            $product = new Product(DB_HOST, DB_NAME, DB_USER, DB_PASS);

                            return $product->productByCategory($category);
                        },
                    ],
                    'productsById' => [
                        'type' =>   Type::listOf(Types::ProductsType()),
                        'args' => [
                            'id' => Type::string()
                        ],
                        'resolve' => function ($root, $args) {
                            $id = $args['id'];
                            $product = new Product(DB_HOST, DB_NAME, DB_USER, DB_PASS);
                            return $product->productById($id);
                        },
                    ],
                ],
            ]);

            $mutationType = new ObjectType([
                'name' => 'Mutation',
                'fields' => [
                    'insertNewProduct' => [
                        'type' =>   Type::listOf(Types::ProductsType()),
                        'args' => [
                            'productInput' => InputTypes::ProductsInputType(),
                        ],
                        'resolve' => function ($root, $args) {
                            $product = $args['productInput'];

                            $addProduct = new Product(DB_HOST, DB_NAME, DB_USER, DB_PASS);
                            $insertedProduct = $addProduct->insertNewProduct($product);

                            return $insertedProduct;
                        }
                    ],
                ]
            ]);

            $schema = new Schema(
                (new SchemaConfig())
                    ->setQuery($queryType)
                    ->setMutation($mutationType)
            );

            $rawInput = file_get_contents('php://input');
            if ($rawInput === false) {
                throw new RuntimeException('Failed to get php://input');
            }
            $input = json_decode($rawInput, true);
            $query = $input['query'];
            $variableValues = $input['variables'] ?? null;

            $rootValue = ['prefix' => 'You said: '];

            $result = GraphQLBase::executeQuery($schema, $query,  $rootValue, null, $variableValues);
            $output = $result->toArray();
        } catch (Throwable $e) {
            $output = [
                'error' => [
                    'message' => $e->getMessage(),
                ],
            ];
        }

        header('Content-Type: application/json; charset=UTF-8');
        return json_encode($output);
    }
}
