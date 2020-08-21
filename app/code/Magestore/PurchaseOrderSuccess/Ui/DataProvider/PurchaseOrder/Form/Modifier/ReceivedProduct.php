<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Ui\DataProvider\PurchaseOrder\Form\Modifier;

use Magento\Ui\Component\Container;
use Magento\Ui\Component\Modal;
use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Status;
use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Type;

/**
 * Class ReceivedProduct
 *
 * Used for received product
 */
class ReceivedProduct extends AbstractModifier
{
    /**
     * @var string
     */
    protected $groupContainer = 'received_product';

    /**
     * @var string
     */
    protected $groupLabel = 'Received Items';

    /**
     * @var int
     */
    protected $sortOrder = 30;

    /**
     * @var array
     */
    protected $children = [
        'received_product_buttons' => 'received_product_buttons',
        'received_product_container' => 'received_product_container',
        'received_product_listing' => 'os_purchase_order_received_product_listing',
        'received_product_modal' => 'received_product_modal',
        'received_product_modal_form' => 'os_purchase_order_received_product_form'
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
        if (!$this->getPurchaseOrderId() || $this->getCurrentPurchaseOrder()->getStatus() == Status::STATUS_PENDING ||
            ($this->getCurrentPurchaseOrder()->getType() == Type::TYPE_QUOTATION)) {
            return $meta;
        }

        $receivedProductMeta = $this->getReceivedProductMeta();

        $meta = array_replace_recursive(
            $meta,
            $receivedProductMeta
        );
        return $meta;
    }

    /**
     * Get received product meta
     *
     * @return array[]
     */
    public function getReceivedProductMeta()
    {
        $purchaseOrder = $this->getCurrentPurchaseOrder();

        $receivedProductMeta = [
            $this->groupContainer => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __($this->groupLabel),
                            'component' => 'Magestore_PurchaseOrderSuccess/js/form/components/fieldset',
                            'collapsible' => true,
                            'dataScope' => 'data',
                            'visible' => $this->getVisible(),
                            'opened' => false,
                            'componentType' => \Magento\Ui\Component\Form\Fieldset::NAME,
                            'sortOrder' => $this->getSortOrder(),
                            'actions' => [
                                [
                                    'targetName' => $this->scopeName . '.' . $this->groupContainer . '.' .
                                        $this->children['received_product_container'],
                                    'actionName' => 'render',
                                ]
                            ]
                        ],
                    ],
                ],
                'children' => $this->getReceivedProductChildren()
            ]
        ];

        if ($purchaseOrder->getStatus() == Status::STATUS_PROCESSING
            && $purchaseOrder->getTotalQtyOrderred() > $purchaseOrder->getTotalQtyReceived()
        ) {
            $receivedProductMeta[$this->children['received_product_modal']] = [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => Modal::NAME,
                            'type' => 'container',
                            'options' => [
                                'onCancel' => 'actionCancel',
                                'title' => __('Receive Items'),
                                'buttons' => [
                                    [
                                        'text' => __('Cancel'),
                                        'actions' => ['closeModal']
                                    ],
                                    [
                                        'text' => __('Save'),
                                        'class' => 'action-primary',
                                        'actions' => [
                                            [
                                                'targetName' => $this->children['received_product_modal_form']
                                                    . '.' . $this->children['received_product_modal_form'],
                                                'actionName' => 'save',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'children' => [
                    $this->children['received_product_modal_form'] => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'autoRender' => false,
                                    'componentType' => 'insertForm',
                                    'component' => 'Magestore_PurchaseOrderSuccess/js/form/components/insert-form',
                                    'ns' => $this->children['received_product_modal_form'],
                                    'sortOrder' => '25',
                                    'params' => [
                                        'purchase_id' => $this->getPurchaseOrderId(),
                                        'supplier_id' => $this->getCurrentPurchaseOrder()->getSupplierId()
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ];
        }
        return $receivedProductMeta;
    }

    /**
     * Add received product form fields
     *
     * @return array
     */
    public function getReceivedProductChildren()
    {
        $purchaseOrder = $this->getCurrentPurchaseOrder();
        if ($purchaseOrder->getStatus() == Status::STATUS_PROCESSING
            && $purchaseOrder->getTotalQtyOrderred() > $purchaseOrder->getTotalQtyReceived()
        ) {
            $children[$this->children['received_product_buttons']] = $this->getReceivedProductButton();
        }
        $children[$this->children['received_product_container']] = $this->getReceivedProductList();

        return $children;
    }

    /**
     * Get received product buttons
     *
     * @return array
     */
    public function getReceivedProductButton()
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
                'received_all_product' => $this->addButton(
                    'Receive All Items',
                    [],
                    $this->urlBuilder->getUrl(
                        'purchaseordersuccess/purchaseOrder/received',
                        ['purchase_id' => $this->getPurchaseOrderId(), 'all' => 'true']
                    )
                ),
                'received_products' => $this->addButton(
                    'Receive Items',
                    [
                        [
                            'targetName' => $this->scopeName
                                . '.' . $this->children['received_product_modal'],
                            'actionName' => 'openModal'
                        ],
                        [
                        'targetName' => $this->scopeName
                            . '.' . $this->children['received_product_modal']
                            . '.' . $this->children['received_product_modal_form'],
                        'actionName' => 'render'
                        ]
                    ]
                ),

            ],
        ];
    }

    /**
     * Get received product list.
     *
     * @return array
     */
    public function getReceivedProductList()
    {
        $dataScope = 'received_product_listing';
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'component' => 'Magestore_PurchaseOrderSuccess/js/form/components/insert-listing',
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
                            . '.purchase_order_item_received_template_columns.ids'
                    ]
                ]
            ]
        ];
    }
}
