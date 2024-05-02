<?php

namespace App\Controller;

require_once 'types.php';
header('Content-Type: application/json; charset=UTF-8');

use GraphQL\GraphQL as GraphQLBase;
use GraphQL\Type\Definition\InputObjectType;
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

            $attributeInputType = new InputObjectType([
                'name' => 'AttributeInput',
                'fields' => [
                    'id' => ['type' => Type::string()],
                    'name' => ['type' => Type::string()],
                    'type' => ['type' => Type::string()],
                    'items' => ['type' => Type::listOf(new InputObjectType([
                        'name' => 'ItemInput',
                        'fields' => [
                            'id' => ['type' => Type::string()],
                            'displayValue' => ['type' => Type::string()],
                            'value' => ['type' => Type::string()]
                        ]
                    ]))]
                ]
            ]);
            $productType = new InputObjectType([
                'name' => 'Product',
                'fields' => [
                    'id' => ['type' => Type::string()],
                    'name' => ['type' => Type::string()],
                    'inStock' => ['type' => Type::boolean()],
                    'gallery' => ['type' => Type::listOf(Type::string())],
                    'description' => ['type' => Type::string()],
                    'category' => ['type' => Type::string()],
                    'attributes' => [
                        'type' => Type::listOf($attributeInputType),
                        // Resolver function to fetch attributes for the product
                        'resolve' => function ($product) {
                            //Fetch and return attributes data for the product
                            return $product['attributes'] ?? [];   //Placeholder for attributes data
                        }
                    ],
                    'prices' => Type::listOf(new InputObjectType([
                        'name' => 'Price',
                        'fields' => [
                            'amount' => Type::float(),
                            'currency' => new InputObjectType([
                                'name' => 'currency',
                                'fields' => [
                                    'label' => Type::string(),
                                    'symbol' => Type::string(),
                                ],
                            ]),
                        ],
                    ])),
                    'brand' => ['type' => Type::string()]
                ]
            ]);


            $queryType = new ObjectType([
                'name' => 'Query',
                'fields' => [
                    'categories' => [
                        'type' => Type::listOf(Types::CategoriesType()),
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
                        'type' => Type::listOf(Types::ProductsType()),
                        'resolve' => function ($root, $args) use ($conn) {
                            // Execute a query to fetch products names
                            $query = "SELECT * FROM products";
                            $stmt = $conn->prepare($query);
                            $stmt->execute();
                            $products =  $stmt->fetchAll(PDO::FETCH_ASSOC);

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
            $mutationType = new ObjectType([
                'name' => 'Mutation',
                'fields' => [
                    'updateProduct' => [
                        'type' => Type::boolean(), // Return true if the mutation is successful--is necesary
                        'args' => [
                            'id' => Type::string(),
                            'name' =>  Type::string(),
                        ],
                        'resolve' => function ($root, $args) use ($conn) {
                            // Prepare and execute SQL update query to change category names
                            $id = $args['id'];
                            $newName = $args['name'];
                            $query = "UPDATE products SET name = :name WHERE id = :id ";
                            $stmt = $conn->prepare($query);
                            $stmt->bindParam(':name', $newName);
                            $stmt->bindParam(':id', $id);
                            $result = $stmt->execute();
                            return $result;  // Return true if the update was successful, false otherwise

                        },
                    ],
                    
                    'insertProduct' => [
                        'type' => Type::boolean(), // Return true if the mutation is successful
                        'args' => [
                            'id' => Type::string(),
                            'attributes' => $attributeInputType, // Use the input type for attributes
                        ],
                        'resolve' => function ($root, $args) use ($conn) {
                            $id = $args['id'];
                            $attributes = json_encode($args['attributes']); // Serialize attributes to JSON
            
                            // Prepare and execute the SQL query
                            $productQuery = "INSERT INTO products (id, attributes) VALUES (:id, :attributes)";
                            $productStmt = $conn->prepare($productQuery);
                            $productStmt->execute([
                                ':id' => $id,
                                ':attributes' => $attributes,
                            ]);
            
                            return true; // Indicate success
                        },
                 
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

            $result = GraphQLBase::executeQuery($schema, $query);
            $output = $result->toArray();
        } catch (Throwable $e) {
            $output = [
                'error' => [
                    'message' => $e->getMessage(),
                ],
            ];
        }

        return json_encode($output);
    }
}
