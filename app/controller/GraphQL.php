<?php

namespace App\Controller;

require_once 'types.php';
require_once 'inputType.php';

use App\Controller\InputTypes;
use App\Controller\Types;
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
                            'productInput' => InputTypes::ProductsInputType(), // Use the main ProductInput type
                        ],
                        'resolve' => function ($root, $args) use ($conn) {
                            $product = $args['productInput'];

                            // Extract product fields from the input object
                            $id = $product['id'];
                            $name = $product['name'];
                            $inStock = $product['inStock'];
                            $gallery = json_encode($product['gallery']);
                            $description = $product['description'];
                            $category = $product['category'];
                            $attributes = json_encode($product['attributes']);
                            $prices = json_encode($product['prices']);
                            $brand = $product['brand'];

                            $productQuery = "INSERT INTO products (id, name, inStock, gallery, description, category, attributes, prices, brand) VALUES (:id, :name, :inStock, :gallery, :description, :category, :attributes, :prices, :brand)";
                            $productStmt = $conn->prepare($productQuery);
                            $productStmt->execute([
                                ':id' => $id,
                                ':name' => $name,
                                ':inStock' => $inStock,
                                ':gallery' => $gallery,
                                ':description' => $description,
                                ':category' => $category,
                                ':attributes' => $attributes,
                                ':prices' => $prices,
                                ':brand' => $brand,
                            ]);

                            return true; // Indicate success
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