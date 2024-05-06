<?php

namespace App\Controller;

require_once 'types.php';
require_once 'inputType.php';

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
                        'resolve' => function ($root, $args, $context)  {
                            $categories = new Category('localhost', 'test5', 'root', '');
                            return $categories->getAllCategories();
                        },
                    ],

                    'products' => [
                        'type' => Type::listOf(Types::ProductsType()),
                        'resolve' => function ($root, $args, $context)   {
                            $products = new Product('localhost', 'test5', 'root', '');
                            return $products->getAllProducts();
                        },
                    ],
                ],
            ]);

            $mutationType = new ObjectType([
                'name' => 'Mutation',
                'fields' => [
                  /*   'updateProduct' => [
                        'type' => Type::boolean(), // Return true if the mutation is successful--is necesary
                        'args' => [
                            'id' => Type::string(),
                            'name' =>  Type::string(),
                        ],
                        'resolve' => function ($root, $args) use ($conf) {
                            $id = $args['id'];
                            $name = $args['name'];
                            $updProduct = new Product($conf['host'], $conf['database'], $conf['username'], $conf['pass']);
                            return $updProduct->updateProduct($id, $name);
                        },
                    ],

                    'insertNewProduct' => [
                        'type' => Type::boolean(),
                        'args' => [
                            'productInput' => InputTypes::ProductsInputType(),
                        ],
                        'resolve' => function ($root, $args) use ($conf) {
                            $product = $args['productInput'];

                            $addProduct = new Product($conf['host'], $conf['database'], $conf['username'], $conf['pass']);
                            return $addProduct->insertNewProduct($product);
                        }

                    ], */
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
            $result = GraphQLBase::executeQuery($schema, $query, $rootValue, null, $variableValues);
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
