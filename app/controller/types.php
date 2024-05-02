<?php

namespace App\Controller;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;


class Types
{

    // Define the CategoriesType 
    public  static function CategoriesType()
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
    public  static function AttributeType()
    {
        $attributeSetType = new ObjectType([
            'name' => 'AttributeSet',
            'fields' => [
                'id' => ['type' => Type::string()],
                'name' => ['type' => Type::string()],
                'type' => ['type' => Type::string()],
                'items' => ['type' => Type::listOf(new ObjectType([
                    'name' => 'items',
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


    // Define the Product type

    public  static function ProductsType()
    {
        $productType = new ObjectType([
            'name' => 'Product',
            'fields' => [
                'id' => ['type' => Type::string()],
                'name' => ['type' => Type::string()],
                'inStock' => ['type' => Type::boolean()],
                'gallery' => ['type' => Type::listOf(Type::string())],
                'description' => ['type' => Type::string()],
                'category' => ['type' => Type::string()],
                'attributes' => [
                    'type' => Type::listOf(self::AttributeType()),
                    // Resolver function to fetch attributes for the product
                    'resolve' => function ($product) {
                        //Fetch and return attributes data for the product
                        return $product['attributes'] ?? [];   //Placeholder for attributes data
                    }
                ],
                'prices' => Type::listOf(new ObjectType([
                    'name' => 'Price',
                    'fields' => [
                        'amount' => Type::float(),
                        'currency' => new ObjectType([
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
        return  $productType;
    }
}
