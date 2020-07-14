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
 * Class TransferredProduct
 * @package Magestore\PurchaseOrderSuccess\Ui\DataProvider\PurchaseOrder\Form\Modifier
 */
class TransferredProduct extends AbstractModifier
{
    /**
     * @var string
     */
    protected $groupContainer = 'transferred_product';

    /**
     * @var string
     */
    protected $groupLabel = 'Transferred Items';

    /**
     * @var int
     */
    protected $sortOrder = 60;

    /**
     * @var array
     */
    protected $children = [
        'transferred_product_buttons' => 'transferred_product_buttons',
        'transferred_product_container' => 'transferred_product_container',
        'transferred_product_listing' => 'os_purchase_order_transferred_product_listing',
        'transferred_product_modal' => 'transferred_product_modal',
        'transferred_product_modal_form' => 'os_purchase_order_transferred_product_form'
    ];

    /**
     * modify data
     *
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
            ($this->getCurrentPurchaseOrder()->getType() == Type::TYPE_QUOTATION))
            return $meta;
        $transferredProductMeta = $this->getTransferredProductMeta();
        $meta = array_replace_recursive(
            $meta,
            $transferredProductMeta
        );
        return $meta;
    }

    public function getTransferredProductMeta()
    {
        $purchaseOrder = $this->getCurrentPurchaseOrder();
        $transferredProductMeta = [
            $this->groupContainer => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __($this->groupLabel),
                            'component' => 'Magestore_PurchaseOrderSuccess/js/form/components/fieldset',
                            'collapsible' => true,
                            'dataScope' => 'data',
                            'visible' => $this->getVisible(),
                            'opened' => $this->getOpened(),
                            'componentType' => \Magento\Ui\Component\Form\Fieldset::NAME,
                            'sortOrder' => $this->getSortOrder(),
                            'actions' => [
                                [
                                    'targetName' => $this->scopeName . '.' . $this->groupContainer . '.' .
                                        $this->children['transferred_product_container'],
                                    'actionName' => 'render',
                                ],
                            ]
                        ],
                    ],
                ],
                'children' => $this->getTransferredProductChildren()
            ],
        ];
        if ($purchaseOrder->getStatus() != Status::STATUS_CANCELED
            && $purchaseOrder->getTotalQtyReceived() >
            $purchaseOrder->getTotalQtyTransferred() + $purchaseOrder->getTotalQtyReturned()
        ) {
            $transferredProductMeta[$this->children['transferred_product_modal']] = [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => Modal::NAME,
                            'type' => 'container',
                            'options' => [
                                'onCancel' => 'actionCancel',
                                'title' => __('Transfer Items'),
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
                                                'targetName' => $this->children['transferred_product_modal_form']
                                                    . '.' . $this->children['transferred_product_modal_form'],
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
                    $this->children['transferred_product_modal_form'] => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'autoRender' => false,
                                    'componentType' => 'insertForm',
                                    'component' => 'Magestore_PurchaseOrderSuccess/js/form/components/insert-form',
                                    'ns' => $this->children['transferred_product_modal_form'],
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
        return $transferredProductMeta;
    }

    /**
     * Add transferred product form fields
     *
     * @return array
     */
    public function getTransferredProductChildren()
    {
        $purchaseOrder = $this->getCurrentPurchaseOrder();
        if ($purchaseOrder->getStatus() != Status::STATUS_CANCELED
            && $purchaseOrder->getTotalQtyReceived() >
            $purchaseOrder->getTotalQtyTransferred() + $purchaseOrder->getTotalQtyReturned()
        )
            $children[$this->children['transferred_product_buttons']] = $this->getTransferredProductButton();
        $children[$this->children['transferred_product_container']] = $this->getTransferredProductList();
        return $children;
    }

    /**
     * Get transferred product button
     *
     * @return array
     */
    public function getTransferredProductButton()
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
                'transferred_products' => $this->addButton(
                    'Transfer Product to Source',
                    [
                        [
                            'targetName' => $this->scopeName
                                . '.' . $this->children['transferred_product_modal'],
                            'actionName' => 'openModal'
                        ], [
                        'targetName' => $this->scopeName
                            . '.' . $this->children['transferred_product_modal']
                            . '.' . $this->children['transferred_product_modal_form'],
                        'actionName' => 'render'
                    ]
                    ]
                )
            ],
        ];
    }

    /**
     * get transferred product list
     *
     * @return array
     */
    public function getTransferredProductList()
    {
        $dataScope = 'transferred_product_listing';
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
                            'purchase_id' => '${ $.provider }:data.purchase_order_id'
                        ],
                        'exports' => [
                            'supplier_id' => '${ $.externalProvider }:params.supplier_id',
                            'purchase_id' => '${ $.externalProvider }:params.purchase_id'
                        ],
                        'selectionsProvider' =>
                            $this->children[$dataScope]
                            . '.' . $this->children[$dataScope]
                            . '.purchase_order_item_transferred_template_columns.ids'
                    ]
                ]
            ]
        ];
    }
}
