<?php

// Define a category type
namespace App\Controller;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;


class Types
{

    // Define the CategoriesType 
    static public function CategoriesType()
    {

        $categoryType = new ObjectType([
            'name' => 'Category',
            'fields' => [
                'name' => ['type' => Type::string()],
            ],
        ]);
        return $categoryType;
    }


    // Define the AttributeType
    static public function AttributeType()
    {
        $attributeSetType = new ObjectType([
            'name' => 'AttributeSet',
            'fields' => [
                'id' => ['type' => Type::string()],
                'name' => ['type' => Type::string()],
                'type' => ['type' => Type::string()],
                'items' => ['type' => Type::listOf(new ObjectType([
                    'name' => 'Attribute',
                    'fields' => [
                        'id' => ['type' => Type::string()],
                        'displayValue' => ['type' => Type::string()],
                        'value' => ['type' => Type::string()]
                    ]
                ]))]
            ]
        ]);
        return $attributeSetType;
    }

    static public function ProductsType()
    {
        $attributeSetType = self::AttributeType();  // Retrieve the AttributeType
        // Define the Product type
        $productType = new ObjectType([
            'name' => 'Product',
            'fields' => [
                'id' => ['type' => Type::string()],
                'name' => ['type' => Type::string()],
                'inStock' => ['type' => Type::boolean()],
                'gallery' => ['type' => Type::listOf(Type::string())],
                'description' => ['type' => Type::string()],
                'category' => ['type' => Type::string()],
                /*   'attributes' => [
                      Attributes are of type AttributeSet
                    'type' => $attributeSetType(),
                      Resolver function to fetch attributes for the product
                    'resolve' => function ($product) {
                        Fetch and return attributes data for the product
                        return $product['attributes'];   Placeholder for attributes data
                    }
                ], */
                'prices' => Type::listOf(new ObjectType([
                    'name' => 'Price',
                    'fields' => [
                        'amount' => Type::float(),
                        'currency' => Type::listOf(new ObjectType([
                            'name' => 'currency',
                            'fields' => [
                                'amount' => Type::string(),
                                'currency' => Type::string(),
                            ],
                        ])),
                    ],
                ])),
                'brand' => ['type' => Type::string()]
            ]
        ]);
        return  $productType;
    }
}
