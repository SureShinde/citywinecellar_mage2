<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Ui\DataProvider\PurchaseOrder\ReceivedProduct\Form\Modifier;

use Magento\Ui\Component\Container;
use Magento\Ui\Component\Modal;
use Magento\Ui\Component\Form;
use Magento\Ui\Component\DynamicRows;

/**
 * Class ProductList
 *
 * Used for product list
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
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
        'received_product_select_modal' => 'received_product_select_modal',
        'received_product_modal_select_listing' => 'os_purchase_order_received_product_select_listing',
        'dynamic_grid' => 'dynamic_grid',
    ];

    protected $mapFields = [
        'id' => 'product_id',
        'product_sku' => 'product_sku',
        'product_name' => 'product_name',
        'product_supplier_sku' => 'product_supplier_sku',
        'available_qty' => 'available_qty',
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
        $children = [
            $this->children['button_set'] => $this->getReceivedProductButtons(),
            $this->children['received_product_select_modal'] => $this->getReceivedProductSelectModal(),
            $this->children['dynamic_grid'] => $this->getDynamicGrid()
        ];
        $moduleManager = \Magento\Framework\App\ObjectManager::getInstance()
            ->create(\Magento\Framework\Module\Manager::class);
        if ($moduleManager->isEnabled('Magestore_BarcodeSuccess')) {
            $children['received_product_barcode_scan_input'] = $this->getReceivedProductScanBarcodeInput();
        }
        return $children;
    }

    /**
     * Get received product buttons
     *
     * @return array
     */
    public function getReceivedProductButtons()
    {

        $moduleManager = \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\Module\Manager::class);
        $showScanBarcodeButton = $moduleManager->isEnabled('Magestore_BarcodeSuccess') ? true : false;

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
                'scan_button' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'formElement' => 'container',
                                'componentType' => 'container',
                                'component' => 'Magestore_PurchaseOrderSuccess/js/form/element/scan-barcode-button',
                                'actions' => [],
                                'title' => __('Scan Barcode'),
                                'provider' => null,
                                'visible' => $showScanBarcodeButton,
                            ],
                        ],
                    ],
                ],
                'select_product_button' => $this->addButton(
                    'Select Products',
                    [
                        [
                            'targetName' => $this->scopeName . '.' . $this->groupContainer
                                . '.' . $this->children['received_product_select_modal'],
                            'actionName' => 'openModal'
                        ],
                        [
                        'targetName' => 'index = ' . $this->children['received_product_modal_select_listing'],
                        'actionName' => 'render'
                        ]
                    ]
                )
            ]
        ];
    }

    /**
     * Get received product scan barcode input
     *
     * @return array
     */
    public function getReceivedProductScanBarcodeInput()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => Container::NAME,
                        'componentType' => \Magento\Ui\Component\Form\Field::NAME,
                        'component' => 'Magestore_PurchaseOrderSuccess/js/form/element/barcode',
                        'label' => false,
                        'sortOrder' => 20,
                        'placeholder' => __('Scan product barcode here'),
                        'barcodeJson' => $this->getReceivedProductBarcodeJson(),
                        'sourceElement' => 'index = ' . $this->children['received_product_modal_select_listing'],
                        'destinationElement' => $this->scopeName . '.' . $this->groupContainer . '.' .
                            $this->children['dynamic_grid'],
                        'selectionsProvider' =>
                            $this->children['received_product_modal_select_listing']
                            . '.' . $this->children['received_product_modal_select_listing']
                            . '.purchase_order_item_template_columns.ids',
                        'qtyElement' => $this->scopeName . '.' . $this->groupContainer . '.' .
                            $this->children['dynamic_grid'] . '.%s.received_qty',
                        'inputElementName' => 'received_qty'
                    ],
                ],
            ],
        ];
    }

    /**
     * Get received product select modal
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
                                    'text' => __('Select'),
                                    'class' => 'action-primary',
                                    'actions' => [
                                        [
                                            'targetName' => 'index = '
                                                . $this->children['received_product_modal_select_listing'],
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
                $this->children['received_product_modal_select_listing'] => $this->getReceiveProductModalSelectListing()
            ]
        ];
    }

    /**
     * Get received product modal select listing
     *
     * @return array
     */
    public function getReceiveProductModalSelectListing()
    {
        $dataScope = 'received_product_modal_select_listing';
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'component' => 'Magestore_PurchaseOrderSuccess/js/form/components/insert-listing-select',
                        'autoRender' => false,
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
                        'dataProvider' => $this->children['received_product_modal_select_listing'],
                        'map' => $this->mapFields,
                        'links' => [
                            'insertData' => '${ $.provider }:${ $.dataProvider }',
                            '__disableTmpl' => [
                                'insertData' => false
                            ]
                        ],
                        'sortOrder' => 30,
                        'columnsHeader' => false,
                        'columnsHeaderAfterRender' => true
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

    /**
     * Get received product barcode json
     *
     * @return mixed
     */
    public function getReceivedProductBarcodeJson()
    {
        $result = [];
        $collection = $this->getReceivedProductBarcodeCollection();
        foreach ($collection->getItems() as $item) {
            $result[$item->getBarcode()] = $item->getData();
        }
        return \Magento\Framework\App\ObjectManager::getInstance()
            ->create(\Magento\Framework\Json\EncoderInterface::class)->encode($result);
    }

    /**
     * Get received product barcode collection
     *
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    public function getReceivedProductBarcodeCollection()
    {
        $collection = \Magento\Framework\App\ObjectManager::getInstance()
            ->create(\Magestore\BarcodeSuccess\Model\ResourceModel\Barcode\Collection::class);
        $condition = 'item.qty_orderred - item.qty_received';
        $purchaseId = $this->request->getParam('purchase_id', null);
        $collection->addFieldToSelect(['barcode']);
        $collection->getSelect()->joinLeft(
            ['item' => $collection->getTable('os_purchase_order_item')],
            'main_table.product_id = item.product_id',
            '*'
        );
        $collection->getSelect()
            ->columns(['available_qty' => new \Zend_Db_Expr($condition)]);
        if ($condition) {
            $collection->getSelect()->where(new \Zend_Db_Expr($condition) . ' > 0');
        }
        if ($purchaseId) {
            $collection->getSelect()->where('item.purchase_order_id = ?', $purchaseId);
        }
        return $collection;
    }
}
