<?php

namespace App\Controller;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;

class InputTypes
{
    private static $priceInputType;
    private static $currencyInputType;
    private static $attributeInputType;
    private static $itemInputType;
    private static $productInputType;

    public static function PriceInputType()
    {
        if (!self::$priceInputType) {
            self::$priceInputType = new InputObjectType([
                'name' => 'PriceInput',
                'fields' => [
                    'amount' => ['type' => Type::float()],
                    'currency' => self::CurrencyInputType(),
                ],
            ]);
        }

        return self::$priceInputType;
    }

    public static function CurrencyInputType()
    {
        if (!self::$currencyInputType) {
            self::$currencyInputType = new InputObjectType([
                'name' => 'CurrencyInput',
                'fields' => [
                    'label' => ['type' => Type::string()],
                    'symbol' => ['type' => Type::string()],
                ],
            ]);
        }

        return self::$currencyInputType;
    }

    public static function AttributeInputType()
    {
        if (!self::$attributeInputType) {
            self::$attributeInputType = new InputObjectType([
                'name' => 'AttributeInput',
                'fields' => [
                    'id' => ['type' => Type::string()],
                    'name' => ['type' => Type::string()],
                    'type' => ['type' => Type::string()],
                    'items' => ['type' => Type::listOf(self::ItemInputType())],
                ],
            ]);
        }

        return self::$attributeInputType;
    }

    public static function ItemInputType()
    {
        if (!self::$itemInputType) {
            self::$itemInputType = new InputObjectType([
                'name' => 'ItemInput',
                'fields' => [
                    'id' => ['type' => Type::string()],
                    'displayValue' => ['type' => Type::string()],
                    'value' => ['type' => Type::string()],
                ],
            ]);
        }

        return self::$itemInputType;
    }

    public static function ProductsInputType()
    {
        if (!self::$productInputType) {
            self::$productInputType = new InputObjectType([
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
                    'quantity' => ['type' => Type::int()],
                ],
            ]);
        }

        return self::$productInputType;
    }
}
