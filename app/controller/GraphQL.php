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
use RuntimeException;

class GraphQL
{

    static public function handle($conf)
    {
        try {

            $queryType = new ObjectType([
                'name' => 'Query',
                'fields' => [
                    'categories' => [
                        'type' => Type::listOf(Types::CategoriesType()),
                        'resolve' => function ($root, $args, $context) use ($conf) {
                            $categories = new Category($conf['host'], $conf['database'], $conf['username'], $conf['pass']);
                            return $categories->getAllCategories();
                        },
                    ],
                ],
            ]);
            $mutationType = new ObjectType([
                'name' => 'Mutation',
                'fields' => [
                    'getAllCategories' => [
                        'type' => Type::listOf(Type::string()),
                        'resolve' => function ($root, $args, $context) use ($conf) {
                            $categories = new Category($conf['host'], $conf['database'], $conf['username'], $conf['pass']);
                            return $categories->getAllCategories();
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
