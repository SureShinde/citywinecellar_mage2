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
 * Class InvoiceList
 *
 * Used for invoice list
 */
class InvoiceList extends AbstractModifier
{
    /**
     * @var string
     */
    protected $groupContainer = 'invoice_list';

    /**
     * @var string
     */
    protected $groupLabel = 'Invoices';

    /**
     * @var int
     */
    protected $sortOrder = 20;

    /**
     * @var array
     */
    protected $children = [
        'invoice_list_buttons' => 'invoice_list_buttons',
        'invoice_list_container' => 'invoice_list_container',
        'invoice_list_listing' => 'os_purchase_order_invoice_listing',
        'invoice_modal' => 'invoice_modal',
        'invoice_modal_form' => 'os_purchase_order_invoice_form'
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
        if (!$this->getPurchaseOrderId() ||
            in_array($this->getCurrentPurchaseOrder()->getStatus(), [Status::STATUS_PENDING]) ||
            ($this->getCurrentPurchaseOrder()->getType() == Type::TYPE_QUOTATION)) {
            return $meta;
        }
        $meta = array_replace_recursive(
            $meta,
            [
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
                                            $this->children['invoice_list_container'],
                                        'actionName' => 'render',
                                    ],
                                ]
                            ],
                        ],
                    ],
                    'children' => $this->getInvoiceChildren()
                ],
            ]
        );
        return $meta;
    }

    /**
     * Set fieldset sort order
     *
     * @return int
     */
    public function getSortOrder()
    {
        if ($this->getCurrentPurchaseOrder()->getStatus() == Status::STATUS_PROCESSING) {
            return 55;
        }
        return $this->sortOrder;
    }

    /**
     * Add invoice form fields
     *
     * @return array
     */
    public function getInvoiceChildren()
    {
        $purchaseOrder = $this->getCurrentPurchaseOrder();
        if ($purchaseOrder->getStatus() != Status::STATUS_CANCELED
            && $purchaseOrder->getTotalQtyOrderred() != $purchaseOrder->getTotalQtyBilled()) {
            $children[$this->children['invoice_list_buttons']] = $this->getInvoiceButton();
        }
        $children[$this->children['invoice_list_container']] = $this->getInvoiceList();
        return $children;
    }

    /**
     * Get invoice button
     *
     * @return array
     */
    public function getInvoiceButton()
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
                'invoice_button' => $this->addButton(
                    'Create an Invoice',
                    [
                        [
                            'targetName' => $this->scopeName . '.' . $this->groupContainer
                                . '.' . $this->children['invoice_list_buttons']
                                . '.' . $this->children['invoice_modal'],
                            'actionName' => 'openModal'
                        ],
                        [
                        'targetName' => $this->scopeName . '.' . $this->groupContainer
                            . '.' . $this->children['invoice_list_buttons']
                            . '.' . $this->children['invoice_modal']
                            . '.' . $this->children['invoice_modal_form'],
                        'actionName' => 'render'
                        ]
                    ]
                ),
                $this->children['invoice_modal'] => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Modal::NAME,
                                'type' => 'container',
                                'options' => [
                                    'onCancel' => 'actionCancel',
                                    'title' => __(
                                        'Create an Invoice (Purchase Order #%1)',
                                        $this->getCurrentPurchaseOrder()->getPurchaseCode()
                                    ),
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
                                                    'targetName' => $this->children['invoice_modal_form']
                                                        . '.' . $this->children['invoice_modal_form'],
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
                        $this->children['invoice_modal_form'] => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'autoRender' => false,
                                        'componentType' => 'insertForm',
                                        'component' => 'Magestore_PurchaseOrderSuccess/js/form/components/insert-form',
                                        'ns' => $this->children['invoice_modal_form'],
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
                ]
            ]
        ];
    }

    /**
     * Get invoice list
     *
     * @return array
     */
    public function getInvoiceList()
    {
        $dataScope = 'invoice_list_listing';
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
                            . '.purchase_order_invoice_template_columns.ids'
                    ]
                ]
            ]
        ];
    }
}
