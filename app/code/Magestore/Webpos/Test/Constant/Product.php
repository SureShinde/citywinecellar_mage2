<?php

/**
 *
 */
namespace Magestore\Webpos\Test\Constant;

/**
 * Class Product
 * @package Magestore\Webpos\Test\Constant
 */
class Product
{
    /**
     * operator
     */
    const OPERATOR_SKU = "API-SKU";
    const OPERATOR_NAME = "API Simple Product";

    /**
     * list skus
     */
    const SKU_1   = 'API-SKU-1';
    const SKU_2   = 'API-SKU-2';
    const SKU_3   = 'API-SKU-3';
    const SKU_4   = 'API-SKU-4';
    const SKU_5   = 'API-SKU-5';
    const SKU_6   = 'API-SKU-6';
    const SKU_7   = 'API-SKU-7';
    const SKU_8   = 'API-SKU-8';
    const SKU_9   = 'API-SKU-9';
    const SKU_10  = 'API-SKU-10';
    const SKU_11  = 'API-SKU-11';
    const SKU_12  = 'API-SKU-12';
    const SKU_13  = 'API-SKU-13';
    const SKU_14  = 'API-SKU-14';
    const SKU_15  = 'API-SKU-15';

    /**
     * list names
     */
    const NAME_1   = 'API Simple Product API-SKU-1';
    const NAME_2   = 'API Simple Product API-SKU-2';
    const NAME_3   = 'API Simple Product API-SKU-3';
    const NAME_4   = 'API Simple Product API-SKU-4';
    const NAME_5   = 'API Simple Product API-SKU-5';
    const NAME_6   = 'API Simple Product API-SKU-6';
    const NAME_7   = 'Download Product API-SKU-7';
    const NAME_8   = 'Download Product API-SKU-8';
    const NAME_9   = 'API Simple Product API-SKU-9';
    const NAME_10  = 'API Simple Product API-SKU-10';
    const NAME_11  = 'API Simple Product API-SKU-11';
    const NAME_12  = 'API Simple Product API-SKU-12';
    const NAME_13  = 'API Simple Product API-SKU-13';
    const NAME_14  = 'API Simple Product API-SKU-14';
    const NAME_15  = 'API Simple Product API-SKU-15';

    /**
     * updated name
     */
    const UPDATED_NAME_SKU_14 = "Updated-product-API-SKU-14";
    const UPDATED_NAME_SKU_15 = "Updated-product-API-SKU-15";

    /**
     * @return array
     */
    public static function productsSku()
    {
        $productsName = [
            1 => self::SKU_1,
            2 => self::SKU_2,
            3 => self::SKU_3,
            4 => self::SKU_4,
            5 => self::SKU_5,
            6 => self::SKU_6,
            7 => self::SKU_7,
            8 => self::SKU_8,
            9 => self::SKU_9,
            10 => self::SKU_10,
            11 => self::SKU_11,
            12 => self::SKU_12,
            13 => self::SKU_13,
            14 => self::SKU_14,
            15 => self::SKU_15
        ];
        return $productsName;
    }

    /**
     * @return array
     */
    public static function productsName()
    {
        $productsName = [
            1 => self::NAME_1,
            2 => self::NAME_2,
            3 => self::NAME_3,
            4 => self::NAME_4,
            5 => self::NAME_5,
            6 => self::NAME_6,
            7 => self::NAME_7,
            8 => self::NAME_8,
            9 => self::NAME_9,
            10 => self::NAME_10,
            11 => self::NAME_11,
            12 => self::NAME_12,
            13 => self::NAME_13,
            14 => self::NAME_14,
            15 => self::NAME_15
        ];
        return $productsName;
    }

    /**
     * @return array
     */
    public static function stocksData()
    {
        $stockData = [
            self::SKU_1 => [
                'qty' => 10.5,
                'is_in_stock' => true,
                'manage_stock' => true,
                'is_qty_decimal' => true
            ],
            self::SKU_2 => [
                'qty' => 20,
                'is_in_stock' => true,
                'manage_stock' => true,
                'is_qty_decimal' => false
            ],
            self::SKU_3 => [
                'qty' => 30,
                'is_in_stock' => false,
                'manage_stock' => true
            ],
            self::SKU_4 => [
                'use_config_manage_stock' => false
            ],
            self::SKU_5 => [
                'qty' => 50,
                'is_in_stock' => false,
                'manage_stock' => true
            ],
            self::SKU_6 => [
                'qty' => 60,
                'is_in_stock' => false,
                'manage_stock' => true
            ],
            self::SKU_7 => [
                'qty' => 70,
                'is_in_stock' => true,
                'manage_stock' => true
            ],
            self::SKU_8 => [
                'qty' => 80,
                'is_in_stock' => true,
                'manage_stock' => true
            ],
            self::SKU_9 => [
                'qty' => 90,
                'is_in_stock' => true,
                'manage_stock' => true
            ],
            self::SKU_10 => [
                'qty' => 100,
                'is_in_stock' => true,
                'manage_stock' => true
            ],
            self::SKU_11 => [
                'qty' => 110,
                'is_in_stock' => true,
                'manage_stock' => true
            ],
            self::SKU_12 => [
                'qty' => 120,
                'is_in_stock' => true,
                'manage_stock' => true
            ],
            self::SKU_13 => [
                'qty' => 130,
                'is_in_stock' => true,
                'manage_stock' => true
            ],
            self::SKU_14 => [
                'qty' => 140,
                'is_in_stock' => true,
                'manage_stock' => true
            ],
            self::SKU_15 => [
                'qty' => 150,
                'is_in_stock' => true,
                'manage_stock' => true
            ],
        ];
        return $stockData;
    }
}
