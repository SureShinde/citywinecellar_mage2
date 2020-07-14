<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\MigrateData\Rewrite\SupplierSuccess\Ui\DataProvider\Product\Form\Modifier;

use Magento\Ui\Component\Form;

/**
 * Class Related
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Supplier extends \Magestore\SupplierSuccess\Ui\DataProvider\Product\Form\Modifier\Supplier
{
    /**
     * Fill meta columns
     *
     * @return array
     */
    public function fillModifierMeta()
    {
        return [
            'id' => $this->getTextColumn('id', false, __('Supplier Id'), 10),
            'supplier_name' => $this->getTextColumn('supplier_name', false, __('Supplier Name'), 20),
            'product_supplier_sku' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'dataType' => Form\Element\DataType\Number::NAME,
                            'formElement' => Form\Element\Input::NAME,
                            'componentType' => Form\Field::NAME,
                            'dataScope' => 'product_supplier_sku',
                            'label' => __('Supplier SKU'),
                            'fit' => true,
                            'additionalClasses' => 'admin__field-small',
                            'sortOrder' => 30,
                            'validation' => [
//                                'required-entry' => true,
                            ],
                        ],
                    ],
                ],
            ],
            'cost' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'dataType' => Form\Element\DataType\Number::NAME,
                            'formElement' => Form\Element\Input::NAME,
                            'componentType' => Form\Field::NAME,
                            'dataScope' => 'cost',
                            'label' => __('Cost'),
                            'fit' => true,
                            'additionalClasses' => 'admin__field-small',
                            'sortOrder' => 40,
                            'validation' => [
                                'validate-number' => true,
                                'required-entry' => true,
                            ],
                        ],
                    ],
                ],
            ],
            'tax' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'dataType' => Form\Element\DataType\Number::NAME,
                            'formElement' => Form\Element\Input::NAME,
                            'componentType' => Form\Field::NAME,
                            'dataScope' => 'tax',
                            'label' => __('Tax'),
                            'fit' => true,
                            'additionalClasses' => 'admin__field-small',
                            'sortOrder' => 50,
                            'validation' => [
                                'validate-number' => true,
                                'required-entry' => true,
                            ],
                        ],
                    ],
                ],
            ],
            'actionDelete' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'additionalClasses' => 'data-grid-actions-cell',
                            'componentType' => 'actionDelete',
                            'dataType' => Form\Element\DataType\Text::NAME,
                            'label' => __('Actions'),
                            'sortOrder' => 90,
                            'fit' => true,
                        ],
                    ],
                ],
            ],
            'position' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'dataType' => Form\Element\DataType\Number::NAME,
                            'formElement' => Form\Element\Input::NAME,
                            'componentType' => Form\Field::NAME,
                            'dataScope' => 'position',
                            'sortOrder' => 100,
                            'visible' => false,
                        ],
                    ],
                ],
            ],
        ];
    }
    
    /**
     * Fields Map
     *
     * @var array
     */
    protected $_mapFields = [
        'id' => 'supplier_id',
        'supplier_name' => 'supplier_name',
        'product_supplier_sku' => 'product_supplier_sku',
        'cost' => 'cost',
        'tax' => 'tax'
    ];
    
    /**
     * Fill data column
     *
     * @param \Magestore\SupplierSuccess\Model\Supplier $item
     * @return array
     */
    public function fillDynamicData($item)
    {
        return [
            'id' => $item->getId(),
            'supplier_name' => $item->getSupplierName(),
            'product_supplier_sku' => $item->getProductSupplierSku(),
            'cost' => $item->getCost(),
            'tax' => $item->getTax(),
        ];
    }
}

