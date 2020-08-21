<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Ui\DataProvider\ReturnOrder\Form\Modifier;

use Magento\Ui\Component\Container;
use Magento\Ui\Component\Modal;
use Magestore\PurchaseOrderSuccess\Model\ReturnOrder\Option\Status;

/**
 * Class TransferredProduct
 *
 * Used for transferred product
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
    protected $groupLabel = 'Delivered Items';

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
        'transferred_product_listing' => 'os_return_order_transferred_product_listing',
        'transferred_product_modal' => 'transferred_product_modal',
        'transferred_product_modal_form' => 'os_return_order_transferred_product_form'
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
     * Modify return order form meta
     *
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        if (!$this->getReturnOrderId() || $this->getCurrentReturnOrder()->getStatus() == Status::STATUS_PENDING) {
            return $meta;
        }
        $transferredProductMeta = $this->getTransferredProductMeta();
        $meta = array_replace_recursive(
            $meta,
            $transferredProductMeta
        );
        return $meta;
    }

    /**
     * Get transferred product meta
     *
     * @return array
     */
    public function getTransferredProductMeta()
    {
        $returnOrder = $this->getCurrentReturnOrder();
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
        if ($returnOrder->getStatus() != Status::STATUS_CANCELED
            && $returnOrder->getStatus() != Status::STATUS_COMPLETED
            && $returnOrder->getTotalQtyReturned() > $returnOrder->getTotalQtyTransferred()
        ) {
            $transferredProductMeta[$this->children['transferred_product_modal']] = [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => Modal::NAME,
                            'type' => 'container',
                            'options' => [
                                'onCancel' => 'actionCancel',
                                'title' => __('Delivery Items'),
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
                                        'return_id' => $this->getReturnOrderId(),
                                        'supplier_id' => $this->getCurrentReturnOrder()->getSupplierId()
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
        $returnOrder = $this->getCurrentReturnOrder();
        if ($returnOrder->getStatus() != Status::STATUS_CANCELED
            && $returnOrder->getStatus() != Status::STATUS_COMPLETED
            && $returnOrder->getTotalQtyReturned() > $returnOrder->getTotalQtyTransferred()
        ) {
            $children[$this->children['transferred_product_buttons']] = $this->getTransferredProductButton();
        }
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
                    'Return Product to Supplier',
                    [
                        [
                            'targetName' => $this->scopeName
                                . '.' . $this->children['transferred_product_modal'],
                            'actionName' => 'openModal'
                        ],
                        [
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
     * Get transferred product list
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
                            'return_id' => '${ $.provider }:data.return_id',
                            '__disableTmpl' => [
                                'supplier_id' => false,
                                'return_id' => false
                            ]
                        ],
                        'exports' => [
                            'supplier_id' => '${ $.externalProvider }:params.supplier_id',
                            'return_id' => '${ $.externalProvider }:params.return_id',
                            '__disableTmpl' => [
                                'supplier_id' => false,
                                'return_id' => false
                            ]
                        ],
                        'selectionsProvider' =>
                            $this->children[$dataScope]
                            . '.' . $this->children[$dataScope]
                            . '.return_order_item_transferred_template_columns.ids'
                    ]
                ]
            ]
        ];
    }
}
