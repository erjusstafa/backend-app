<?php

namespace App\Controller;

require_once 'types.php';

use GraphQL\GraphQL as GraphQLBase;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use PDO;
use Throwable;


class GraphQL
{

    static public function handle($conn)
    {
        try {

            $types = new Types();
            $queryType = new ObjectType([
                'name' => 'Query',
                'fields' => [
                    'categories' => [
                        'type' => Type::listOf($types->CategoriesType()),
                        'resolve' => function ($root, $args, $context) use ($conn) {

                            // Execute a query to fetch categories names
                            $query = "SELECT name FROM categories";
                            $stmt = $conn->prepare($query);
                            $stmt->execute();
                            $categories =  $stmt->fetchAll(PDO::FETCH_ASSOC);
                            return $categories;
                        },
                    ],

                    'products' => [
                        'type' => Type::listOf($types->ProductsType()),
                        'resolve' => function ($root, $args, $context) use ($conn) {

                            // Execute a query to fetch products names
                            $query = "SELECT id FROM products";
                            $stmt = $conn->prepare($query);
                            $stmt->execute();
                            $products =  $stmt->fetchAll(PDO::FETCH_ASSOC);
                            return $products;
                        },
                    ],
                ],
            ]);

            /*  $mutationType = new ObjectType([
                'name' => 'Mutation',
                'fields' => [
                    'sum' => [
                        'type' => Type::int(),
                        'args' => [
                            'x' => ['type' => Type::int()],
                            'y' => ['type' => Type::int()],
                        ],
                        'resolve' => static fn ($calc, array $args): int => $args['x'] + $args['y'],
                    ],
                ],
            ]); */

            /* $schema = new Schema(
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
            $output = $result->toArray(); */


            // Create a schema
            $schema = new Schema(['query' => $queryType]);

            // Execute a query
            $query = '{ products {id} }';
            $result = GraphQLBase::executeQuery($schema, $query);
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
