<?php

namespace App\Controller;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;


class Types
{
    private static $productsType;
    private static $attributesType;
    private static $attributeItemsType;
    private static $currencyType;
    private static $pricesType;

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

 
    public static function ProductsType()
    {
        if (!self::$productsType) {
            self::$productsType = new ObjectType([
                'name' => 'Product',
                'fields' => [
                    'id' => Type::string(),
                    'name' => Type::string(),
                    'inStock' =>  Type::boolean(),
                    'gallery' => Type::listOf(Type::string()),
                    'description' => Type::string(),
                    'category' => Type::string(),
                    'attributes' => Type::listOf(self::AttributesType()),
                    'prices' => Type::listOf(self::PricesType()),
                    'brand' => Type::string(),
                    'quantity' => Type::int(),
                ],
            ]);
        }

        return self::$productsType;
    }


    public static function AttributesType()
    {
        if (!self::$attributesType) {
            self::$attributesType = new ObjectType([
                'name' => 'Attribute',
                'fields' => [
                    'id' => Type::string(),
                    'name' => Type::string(),
                    'type' => Type::string(),
                    'items' => Type::listOf(self::AttributeItemsType()),
                ],
            ]);
        }

        return self::$attributesType;
    }


    public static function AttributeItemsType()
    {
        if (!self::$attributeItemsType) {
            self::$attributeItemsType = new ObjectType([
                'name' => 'AttributeItem',
                'fields' => [
                    'id' => Type::string(),
                    'displayValue' => Type::string(),
                    'value' => Type::string(),
                ],
            ]);
        }

        return self::$attributeItemsType;
    }

 
    public static function PricesType()
    {
        if (!self::$pricesType) {
            self::$pricesType = new ObjectType([
                'name' => 'Price',
                'fields' => [
                    'amount' => Type::float(),
                    'currency' => self::CurrencyType(),
                ],
            ]);
        }

        return self::$pricesType;
    }


    public static function CurrencyType()
    {
        if (!self::$currencyType) {
            self::$currencyType = new ObjectType([
                'name' => 'Currency',
                'fields' => [
                    'label' => Type::string(),
                    'symbol' => Type::string(),
                ],
            ]);
        }

        return self::$currencyType;
    }
 
}
