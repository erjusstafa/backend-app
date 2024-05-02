<?php

namespace App\Controller;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;

class InputTypes
{
    public  static function PriceInputType()
    {

        $priceInputType = new InputObjectType([
            'name' => 'PriceInput',
            'fields' => [
                'amount' => ['type' => Type::float()],
                'currency' => new InputObjectType([
                    'name' => 'CurrencyInput',
                    'fields' => [
                        'label' => ['type' => Type::string()],
                        'symbol' => ['type' => Type::string()],
                    ],
                ]),
            ],
        ]);
        return $priceInputType;
    }


    public  static function AttributeInputType()
    {
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
        return $attributeInputType;
    }


    public  static function ProductsInputType()
    {
        $productInputType = new InputObjectType([
            'name' => 'ProductInput',
            'fields' => [
                'id' => ['type' => Type::string()],
                'name' => ['type' => Type::string()],
                'inStock' => ['type' => Type::boolean()],
                'gallery' => ['type' => Type::listOf(Type::string())],
                'description' => ['type' => Type::string()],
                'category' => ['type' => Type::string()],
                'attributes' => ['type' => Type::listOf(self::AttributeInputType())],
                'prices' => ['type' => Type::listOf(self::PriceInputType())],
                'brand' => ['type' => Type::string()],
            ]
        ]);
        return $productInputType;
    }
}
