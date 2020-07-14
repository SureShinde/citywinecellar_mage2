<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;

class Qty extends AbstractModifier
{
    const CODE_QUANTITY = 'qty';
    const CODE_QTY_CONTAINER = 'quantity_and_stock_status_qty';
    const ADVANCED_INVENTORY_BUTTON = 'advanced_inventory_button';

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        if ($groupCode = $this->getGroupCodeByField($meta, self::CODE_QTY_CONTAINER)) {
            $parentChildren = &$meta[$groupCode]['children'];
            if (!empty($parentChildren[self::CODE_QTY_CONTAINER])) {
                
//                $tooltip = [
//                    'description' => __(
//                        'This is custom sale product, change quantity function is not available.'
//                    ),
//                ];


                $parentChildren[self::CODE_QTY_CONTAINER] = array_replace_recursive(
                    $parentChildren[self::CODE_QTY_CONTAINER],
                    [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'visible' => 0,
                                ],
                            ],
                        ],
//                        'children' => [
//                            static::CODE_QUANTITY => [
//                                'arguments' => [
//                                    'data' => [
//                                        'config' => [
//                                            'visible' => 0,
//                                            'tooltip' => $tooltip,
//                                            'dataScope' => static::CODE_QUANTITY,
//                                            'disabled'=> true,
//                                        ],
//                                    ],
//                                ],
//                            ],
//                            static::ADVANCED_INVENTORY_BUTTON => [
//                                'arguments' => [
//                                    'data' => [
//                                        'config' => [
//                                            'visible' => 0,
//                                            'dataScope' => static::ADVANCED_INVENTORY_BUTTON,
//                                        ],
//                                    ],
//                                ],
//                            ],
//                        ],
                    ]
                );
            } 
        }

        return $meta;
    }
}
