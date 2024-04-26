<?php

// Define a category type
namespace App\Controller;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;


class Types
{

    static public function Cat()
    {

        $categoryType = new ObjectType([
            'name' => 'Category',
            'fields' => [
                'name' => ['type' => Type::string()],
            ],
        ]);
        return $categoryType;
    }
}
