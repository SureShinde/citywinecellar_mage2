<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\BarcodeSuccess\Ui\DataProvider\Barcode\Form\Modifier;

use Magento\Ui\Component\Form;
use Magento\Ui\Component\DynamicRows;

/**
 * Class Related
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Product extends \Magestore\BarcodeSuccess\Ui\DataProvider\Barcode\Form\Modifier\AbstractModifier
{

    protected $_groupContainer = 'os_generate_barcode';

    protected $_dataLinks = 'selected_products';

    protected $_groupLabel = 'Product(s)';

    protected $_fieldsetContent = 'Please select the product to generate barcode';


    protected $_buttonTitle = 'Select Products';


    protected $_modalTitle = 'Select Products to generate';


    protected $_modalButtonTitle = 'Add Selected Products';

    protected $_collapsible = false;

    protected $_sortOrder = 10;

    protected $_modifierConfig = [
        'button_set' => 'barcode_button_set',
        'modal' => 'generate_products_modal',
        'listing' => 'os_barcode_product_listing',
        'form' => 'os_barcode_generate_form',
        'columns_ids' => 'product_columns.ids'
    ];

    protected $_mapFields = [
        'id' => 'entity_id',
        'name' => 'name',
        'sku' => 'sku',
        'price' => 'price',
        'status' => 'status_text',
        'attribute_set' => 'attribute_set_text',
        'thumbnail' => 'thumbnail_src',
    ];

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return parent::modifyData($data);
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        return parent::modifyMeta($meta);
    }
    /**
     * Fill meta columns
     *
     * @return array
     */
    public function fillModifierMeta()
    {
        $meta = [
            'id' => $this->getTextColumn('id', true, __('ID'), 10),
            'sku' => $this->getTextColumn('sku', false, __('SKU'), 20),
            'qty' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'dataType' => Form\Element\DataType\Number::NAME,
                            'formElement' => Form\Element\Input::NAME,
                            'componentType' => Form\Field::NAME,
                            'dataScope' => 'qty',
                            'label' => __('Item Quantity'),
                            'fit' => true,
                            'additionalClasses' => 'admin__field-small',
                            'sortOrder' => 30,
                            'validation' => [
                                'validate-number' => true,
                                'required-entry' => true,
                                'not-negative-amount' => true
                            ]
                        ],
                    ],
                ],
            ],
            'supplier' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'dataType' => Form\Element\DataType\Number::NAME,
                            'formElement' => Form\Element\Input::NAME,
                            'componentType' => Form\Field::NAME,
                            'dataScope' => 'supplier',
                            'label' => __('Supplier Code'),
                            'fit' => true,
                            'additionalClasses' => 'admin__field-small',
                            'sortOrder' => 40,
                            'validation' => [
                            ],
                        ],
                    ],
                ],
            ],
            'purchased_time' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'dataType' => 'date',
                            'formElement' => 'date',
                            'componentType' => Form\Field::NAME,
                            'dataScope' => 'purchased_time',
                            'label' => __('Purchased Time'),
                            'fit' => true,
                            'additionalClasses' => 'admin__field-small',
                            'sortOrder' => 50,
                            'validation' => [
                            ],
                            'options' => [
                                'showsTime' => true,
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
                            'sortOrder' => 60,
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
                            'sortOrder' => 70,
                            'visible' => false,
                        ],
                    ],
                ],
            ],
        ];
        $one_barcode_per_sku = $this->helper->getStoreConfig('barcodesuccess/general/one_barcode_per_sku');
        if($one_barcode_per_sku){
            unset($meta['qty']);
            unset($meta['purchased_time']);
        }
        return $meta;
    }
}
