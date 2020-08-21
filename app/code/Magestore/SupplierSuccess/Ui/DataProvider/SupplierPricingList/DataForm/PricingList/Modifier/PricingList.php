<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Ui\DataProvider\SupplierPricingList\DataForm\PricingList\Modifier;

use Magento\Ui\Component\Container;
use Magento\Ui\Component\Modal;
use Magento\Ui\Component\Form;
use Magento\Ui\Component\DynamicRows;
use Magestore\SupplierSuccess\Ui\DataProvider\Supplier\DataForm\Modifier\AbstractModifier;

/**
 * Class PricingList
 *
 * Used for pricing list
 */
class PricingList extends AbstractModifier
{
    /**
     * @var string
     */
    protected $groupContainer = 'pricing_list';

    /**
     * @var string
     */
    protected $groupLabel = 'Pricelist';

    /**
     * @var int
     */
    protected $sortOrder = 20;

    protected $children = [
        'button_set' => 'button_set',
        'pricing_list_product_select_modal' => 'pricing_list_product_select_modal',
        'pricing_list_product_modal_select_listing' => 'os_supplier_pricing_list_product_select_listing',
        'dynamic_grid' => 'dynamic_grid',
    ];

    protected $mapFields = [
        'id' => 'entity_id',
        'product_sku' => 'sku',
        'product_name' => 'name'
    ];

    /**
     * Modify data
     *
     * @param array $data
     * @return array
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * Modify purchase order form meta
     *
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        $meta = array_replace_recursive(
            $meta,
            [
                $this->groupContainer => [
                    'children' => $this->getProductListChildren(),
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __($this->groupLabel),
                                'collapsible' => true,
                                'visible' => true,
                                'opened' => true,
                                'componentType' => \Magento\Ui\Component\Form\Fieldset::NAME
                            ],
                        ],
                    ],
                ],
            ]
        );
        return $meta;
    }

    /**
     * Add general form fields
     *
     * @return array
     */
    public function getProductListChildren()
    {
        $children = [
            $this->children['button_set'] => $this->getReceivedProductButtons(),
            $this->children['pricing_list_product_select_modal'] => $this->getReceivedProductSelectModal(),
            $this->children['dynamic_grid'] => $this->getDynamicGrid()
        ];
        return $children;
    }

    /**
     * GetReceivedProductButtons
     *
     * @return array
     */
    public function getReceivedProductButtons()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => Container::NAME,
                        'componentType' => Container::NAME,
                        'label' => false,
                        'template' => 'Magestore_SupplierSuccess/form/components/button-list',
                    ],
                ],
            ],
            'children' => [
                'select_product_button' => $this->addButton(
                    __('Select Products'),
                    [
                        [
                            'targetName' => 'os_supplier_pricinglist_modal_add_listing.os_supplier_pricinglist_modal_add_listing' //phpcs:disable
                                . '.'
                                . $this->groupContainer
                                . '.' . $this->children['pricing_list_product_select_modal'],
                            'actionName' => 'openModal',
                            'params' => [
                                true,
                                ['supplier_id' => 1],
                            ]
                        ]
                    ]
                ),
            ]
        ];
    }

    /**
     * GetReceivedProductSelectModal
     *
     * @return array
     */
    public function getReceivedProductSelectModal()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Modal::NAME,
                        'type' => 'container',
                        'options' => [
                            'onCancel' => 'actionCancel',
                            'title' => __('Select Products'),
                            'buttons' => [
                                [
                                    'text' => __('Cancel'),
                                    'actions' => ['closeModal']
                                ],
                                [
                                    'text' => __('Select Product'),
                                    'class' => 'action-primary',
                                    'actions' => [
                                        [
                                            'targetName' => 'index = ' . $this->children['pricing_list_product_modal_select_listing'],
                                            'actionName' => 'save',
                                        ],
                                        'closeModal'
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'children' => [
                $this->children['pricing_list_product_modal_select_listing'] => $this->getPricingListProductModalSelectListing()
            ]
        ];
    }

    /**
     * GetPricingListProductModalSelectListing
     *
     * @return array
     */
    public function getPricingListProductModalSelectListing()
    {
        $dataScope = 'pricing_list_product_modal_select_listing';
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'autoRender' => true,
                        'componentType' => 'insertListing',
                        'dataScope' => $this->children['pricing_list_product_modal_select_listing'],
                        'externalProvider' => $this->children[$dataScope] . '.' . $this->children[$dataScope]
                            . '_data_source',
                        'selectionsProvider' =>
                            $this->children[$dataScope]
                            . '.' . $this->children[$dataScope]
                            . '.pricing_list_product_template_columns.ids',
                        'ns' => $this->children['pricing_list_product_modal_select_listing'],
                        'provider' =>
                            'os_supplier_pricinglist_modal_add_listing'
                            . '.'
                            . 'os_supplier_pricinglist_modal_add_listing'
                            . '_data_source',
                        'render_url' => $this->urlBuilder->getUrl('mui/index/render'),
                        'realTimeLink' => true,
                        'dataLinks' => [
                            'imports' => false,
                            'exports' => true
                        ],
                        'behaviourType' => 'simple',
                        'externalFilterMode' => true,
                        'imports' => [
                            'entity_id' => '${ $.provider }:data.' . 'entity_id',
                            '__disableTmpl' => [
                                'entity_id' => false
                            ]
                        ],
                        'exports' => [
                            'entity_id' => '${ $.externalProvider }:params.' . 'entity_id',
                            '__disableTmpl' => [
                                'entity_id' => false
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }


    /**
     * Returns dynamic rows configuration
     *
     * @return array
     */
    public function getDynamicGrid()
    {
        $grid = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'additionalClasses' => 'admin__field-wide',
                        'componentType' => DynamicRows::NAME,
                        'label' => null,
                        'renderDefaultRecord' => false,
                        'template' => 'ui/dynamic-rows/templates/grid',
                        'component' => 'Magento_Ui/js/dynamic-rows/dynamic-rows-grid',
                        'addButton' => false,
                        'itemTemplate' => 'record',
                        'dataScope' => 'data.links',
                        'deleteButtonLabel' => __('Remove'),
                        'dataProvider' => $this->children['pricing_list_product_modal_select_listing'],
                        'map' => $this->mapFields,
                        'links' => [
                            'insertData' => '${ $.provider }:${ $.dataProvider }',
                            '__disableTmpl' => [
                                'insertData' => false
                            ]
                        ],
                        'sortOrder' => 20,
                        'columnsHeader' => false,
                        'columnsHeaderAfterRender' => true,
                    ]
                ],
            ],
            'children' => $this->getRows(),
        ];
        return $grid;
    }

    /**
     * Returns Dynamic rows records configuration
     *
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getRows()
    {
        return [
            'record' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => Container::NAME,
                            'isTemplate' => true,
                            'is_collection' => true,
                            'component' => 'Magento_Ui/js/dynamic-rows/record',
                            'dataScope' => '',
                        ],
                    ],
                ],
                'children' => [
                    'id' => $this->getTextColumn('id', true, __('ID'), 10),
                    'product_sku' => $this->getTextColumn('product_sku', false, __('SKU'), 20),
                    'product_name' => $this->getTextColumn('product_name', false, __('Name'), 30),
                    'minimal_qty' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'dataType' => Form\Element\DataType\Number::NAME,
                                    'formElement' => Form\Element\Input::NAME,
                                    'componentType' => Form\Field::NAME,
                                    'dataScope' => 'minimal_qty',
                                    'label' => __('Minimal Qty'),
                                    'fit' => true,
                                    'additionalClasses' => 'admin__field-small',
                                    'sortOrder' => 40,
                                    'validation' => [
                                        'validate-number' => true,
                                        'validate-greater-than-zero' => true,
                                        'required-entry' => true,
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
                                    'sortOrder' => 45,
                                    'validation' => [
                                        'validate-number' => true,
                                        'validate-greater-than-zero' => true,
                                        'required-entry' => true,
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'start_date' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'dataType' => 'date',
                                    'formElement' => 'date',
                                    'componentType' => Form\Field::NAME,
                                    'dataScope' => 'start_date',
                                    'label' => __('Start date'),
                                    'fit' => true,
                                    'additionalClasses' => 'admin__field-small',
                                    'sortOrder' => 50,
                                    'validation' => [
                                        'validate-date' => true,
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'end_date' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'dataType' => 'date',
                                    'formElement' => 'date',
                                    'componentType' => Form\Field::NAME,
                                    'dataScope' => 'end_date',
                                    'label' => __('End date'),
                                    'fit' => true,
                                    'additionalClasses' => 'admin__field-small',
                                    'sortOrder' => 60,
                                    'validation' => [
                                        'validate-date' => true,
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
                                    'sortOrder' => 70,
                                    'fit' => true,
                                ],
                            ],
                        ],
                    ]
                ],
            ],
        ];
    }

    /**
     * Returns Dynamic rows records configuration
     *
     * @return array
     */
    public function getRows1()
    {
        return [
            'record' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => 'container',
                            'isTemplate' => true,
                            'is_collection' => true,
                            'component' => 'Magento_Ui/js/dynamic-rows/record',
                            'dataScope' => '',
                        ],
                    ],
                ],
                'children' => $this->fillModifierMeta(),
            ],
        ];
    }

    /**
     * Fill meta columns
     *
     * @return array
     */
    public function fillModifierMeta()
    {
        return [
            'id' => $this->getTextColumn('id', false, __('Product ID'), 10),
            'product_sku' => $this->getTextColumn('product_sku', false, __('Product SKU'), 20),
            'product_name' => $this->getTextColumn('product_name', false, __('Product Name'), 30),
//            'product_supplier_sku' => $this->getTextColumn('product_supplier_sku', false, 'Supplier SKU', 40),
//            'available_qty' => $this->getTextColumn('available_qty', false, 'Available Qty', 50),
            'received_qty' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'dataType' => Form\Element\DataType\Number::NAME,
                            'formElement' => Form\Element\Input::NAME,
                            'componentType' => Form\Field::NAME,
                            'dataScope' => 'received_qty',
                            'label' => __('Receive Qty'),
                            'fit' => true,
                            'additionalClasses' => 'admin__field-small',
                            'sortOrder' => 60,
                            'validation' => [
                                'validate-number' => true,
                                'validate-greater-than-zero' => true,
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
                            'sortOrder' => 70,
                            'fit' => true,
                        ],
                    ],
                ],
            ]
        ];
    }
}
