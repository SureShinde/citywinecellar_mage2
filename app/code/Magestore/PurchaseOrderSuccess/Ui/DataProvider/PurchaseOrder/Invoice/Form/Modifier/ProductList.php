<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Ui\DataProvider\PurchaseOrder\Invoice\Form\Modifier;

use Magento\Ui\Component\Container;
use Magento\Ui\Component\Modal;
use Magento\Ui\Component\Form;
use Magento\Ui\Component\DynamicRows;

/**
 * Class ProductList
 *
 * Used for product list
 */
class ProductList extends AbstractModifier
{
    /**
     * @var string
     */
    protected $groupContainer = 'product_list';

    /**
     * @var string
     */
    protected $groupLabel = 'Product List';

    /**
     * @var int
     */
    protected $sortOrder = 20;

    protected $children = [
        'button_set' => 'button_set',
        'invoice_select_modal' => 'invoice_select_modal',
        'invoice_modal_select_listing' => 'os_purchase_order_invoice_select_listing',
        'dynamic_grid' => 'dynamic_grid',
        'invoice_item_list_container' => 'invoice_item_list_container',
        'invoice_item_list_listing' => 'os_purchase_order_invoice_item_listing',
        'invoice_sumary_total' => 'invoice_sumary_total'
    ];

    protected $mapFields = [
        'id' => 'product_id',
        'product_sku' => 'product_sku',
        'product_name' => 'product_name',
        'product_supplier_sku' => 'product_supplier_sku',
        'available_qty' => 'available_qty',
        'cost' => 'cost',
        'qty_billed' => 'available_qty',
        'unit_price' => 'cost_default', // show price without currency
        'tax' => 'tax',
        'discount' => 'discount',
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
                                'visible' => $this->getVisible(),
                                'opened' => true,
                                'componentType' => \Magento\Ui\Component\Form\Fieldset::NAME,
                                'sortOrder' => $this->getSortOrder()
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
        if ($this->getCurrentInvoice()) {
            $children = [
                $this->children['invoice_item_list_container'] => $this->getInvoiceItemListing(),
                $this->children['invoice_sumary_total'] => $this->getInvoiceSumaryTotal()
            ];
        } else {
            $children = [
                $this->children['button_set'] => $this->getInvoiceButtons(),
                $this->children['invoice_select_modal'] => $this->getInvoiceSelectModal(),
                $this->children['dynamic_grid'] => $this->getDynamicGrid()
            ];
        }
        return $children;
    }

    /**
     * Get invoice item listing
     *
     * @return array
     */
    public function getInvoiceItemListing()
    {
        $dataScope = 'invoice_item_list_listing';
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'component' => 'Magestore_PurchaseOrderSuccess/js/form/components/insert-listing',
                        'autoRender' => true,
                        'componentType' => 'insertListing',
                        'dataScope' => $this->children[$dataScope],
                        'externalProvider' => $this->children[$dataScope] . '.' . $this->children[$dataScope]
                            . '_data_source',
                        'ns' => $this->children[$dataScope],
                        'render_url' => $this->urlBuilder->getUrl('mui/index/render'),
                        'realTimeLink' => true,
                        'dataLinks' => [
                            'imports' => false,
                            'exports' => true
                        ],
                        'behaviourType' => 'simple',
                        'externalFilterMode' => true,
                        'imports' => [
                            'invoice_id' => '${ $.provider }:data.purchase_order_invoice_id',
                            'purchase_id' => '${ $.provider }:data.purchase_order_id',
                            '__disableTmpl' => [
                                'invoice_id' => false,
                                'purchase_id' => false
                            ]
                        ],
                        'exports' => [
                            'invoice_id' => '${ $.externalProvider }:params.invoice_id',
                            'purchase_id' => '${ $.externalProvider }:params.purchase_id',
                            '__disableTmpl' => [
                                'invoice_id' => false,
                                'purchase_id' => false
                            ]
                        ],
                        'selectionsProvider' =>
                            $this->children[$dataScope]
                            . '.' . $this->children[$dataScope]
                            . '.purchase_order_invoice_item_template_columns.ids'
                    ]
                ]
            ]
        ];
    }

    /**
     * Get invoice sumary total block
     *
     * @return array
     */
    public function getInvoiceSumaryTotal()
    {
        return $this->addHtmlContentContainer(
            'invoice_sumary_total_container',
            \Magestore\PurchaseOrderSuccess\Block\Adminhtml\PurchaseOrder\Invoice\Edit\Fieldset\Total::class
        );
    }

    /**
     * Get invoice buttons
     *
     * @return array
     */
    public function getInvoiceButtons()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => Container::NAME,
                        'componentType' => Container::NAME,
                        'label' => false,
                        'template' => 'Magestore_PurchaseOrderSuccess/form/components/button-list',
                    ],
                ],
            ],
            'children' => [
                'select_product_button' => $this->addButton(
                    'Select Products',
                    [
                        [
                            'targetName' => $this->scopeName . '.' . $this->groupContainer
                                . '.' . $this->children['invoice_select_modal'],
                            'actionName' => 'openModal'
                        ]
                    ]
                ),
            ]
        ];
    }

    /**
     * Get invoice select modal
     *
     * @return array
     */
    public function getInvoiceSelectModal()
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
                                    'text' => __('Select'),
                                    'class' => 'action-primary',
                                    'actions' => [
                                        [
                                            'targetName' => 'index = '
                                                . $this->children['invoice_modal_select_listing'],
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
                $this->children['invoice_modal_select_listing'] => $this->getInvoiceModalSelectListing()
            ]
        ];
    }

    /**
     * Get invoice modal select listing
     *
     * @return array
     */
    public function getInvoiceModalSelectListing()
    {
        $dataScope = 'invoice_modal_select_listing';
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'component' => 'Magestore_PurchaseOrderSuccess/js/form/components/insert-listing-select',
                        'autoRender' => true,
                        'componentType' => 'insertListing',
                        'dataScope' => $this->children[$dataScope],
                        'externalProvider' => $this->children[$dataScope] . '.' . $this->children[$dataScope]
                            . '_data_source',
                        'ns' => $this->children[$dataScope],
                        'render_url' => $this->urlBuilder->getUrl('mui/index/render'),
                        'realTimeLink' => true,
                        'dataLinks' => [
                            'imports' => false,
                            'exports' => true
                        ],
                        'behaviourType' => 'simple',
                        'externalFilterMode' => true,
                        'imports' => [
                            'supplier_id' => '${ $.provider }:data.supplier_id',
                            'purchase_id' => '${ $.provider }:data.purchase_order_id',
                            '__disableTmpl' => [
                                'supplier_id' => false,
                                'purchase_id' => false
                            ]
                        ],
                        'exports' => [
                            'supplier_id' => '${ $.externalProvider }:params.supplier_id',
                            'purchase_id' => '${ $.externalProvider }:params.purchase_id',
                            '__disableTmpl' => [
                                'supplier_id' => false,
                                'purchase_id' => false
                            ]
                        ],
                        'selectionsProvider' =>
                            $this->children[$dataScope]
                            . '.' . $this->children[$dataScope]
                            . '.purchase_order_item_template_columns.ids'
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
                        'dataScope' => 'data',
                        'deleteButtonLabel' => __('Remove'),
                        'dataProvider' => $this->children['invoice_modal_select_listing'],
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
                    ],
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
     */
    public function getRows()
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
            'id' => $this->getTextColumn('id', false, 'Product ID', 10),
            'product_sku' => $this->getTextColumn('product_sku', false, 'Product SKU', 20),
            'product_name' => $this->getTextColumn('product_name', false, 'Product Name', 30),
            'product_supplier_sku' => $this->getTextColumn('product_supplier_sku', false, 'Supplier SKU', 40),
            'available_qty' => $this->getTextColumn('available_qty', false, 'Available Qty', 50),
            'cost' => $this->getTextColumn('cost', false, 'Purchase Cost', 55),
            'qty_billed' => $this->getInputNumberColumn('qty_billed', true, 'Bill Qty', 60, [
                'validate-number' => true,
                'validate-greater-than-zero' => true,
                'required-entry' => true,
            ]),
            'unit_price' => $this->getInputNumberColumn('unit_price', true, 'Unit Price', 70, [
                'validate-number' => true,
                'validate-greater-than-zero' => true,
                'required-entry' => true,
            ]),
            'tax' => $this->getInputNumberColumn('tax', true, 'Tax (%)', 80, [
                'validate-number' => true,
                'validate-zero-or-greater' => true,
                'required-entry' => false,
            ]),
            'discount' => $this->getInputNumberColumn('discount', true, 'Discount (%)', 90, [
                'validate-number' => true,
                'validate-zero-or-greater' => true,
                'required-entry' => false,
            ]),
            'actionDelete' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'additionalClasses' => 'data-grid-actions-cell',
                            'componentType' => 'actionDelete',
                            'dataType' => Form\Element\DataType\Text::NAME,
                            'label' => __('Actions'),
                            'sortOrder' => 100,
                            'fit' => true,
                        ],
                    ],
                ],
            ]
        ];
    }
}
