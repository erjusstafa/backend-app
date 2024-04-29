<?php

namespace App\Controller;

require_once 'types.php';

use GraphQL\GraphQL as GraphQLBase;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use PDO;
use Throwable;
use GraphQL\Type\SchemaConfig;
use RuntimeException;

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
                            $query = "SELECT * FROM categories";
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
                            $query = "SELECT * FROM products";
                            $stmt = $conn->prepare($query);
                            $stmt->execute();
                            $products =  $stmt->fetchAll(PDO::FETCH_ASSOC);
                            if (empty($products)) {
                                return []; // Return empty array if products array is empty
                            }

                            return array_map(function ($item) {

                                return [
                                    'id' => $item['id'] ?? '',
                                    'name' => $item['name'] ?? '',
                                    'inStock' => $item['inStock'] ?? false,
                                    'gallery' => json_decode($item['gallery'], true) ?? [],
                                    'description' => $item['description'] ?? '',
                                    'category' => $item['category'] ?? '',
                                    'attributes' =>  json_decode($item['attributes'], true) ?? [],
                                    'prices' => json_decode($item['prices'], true) ?? [], // Decode JSON string to array
                                    'brand' => $item['brand'] ??  ''

                                ];
                            }, $products);
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

            $schema = new Schema(
                (new SchemaConfig())
                    ->setQuery($queryType)
                /*  ->setMutation($mutationType) */
            );

            $rawInput = file_get_contents('php://input');
            if ($rawInput === false) {
                throw new RuntimeException('Failed to get php://input');
            }

            $input = json_decode($rawInput, true);
            /*  $query = $input['query']; */

            $query = '{products{id, attributes{items{displayValue}}}}';
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
