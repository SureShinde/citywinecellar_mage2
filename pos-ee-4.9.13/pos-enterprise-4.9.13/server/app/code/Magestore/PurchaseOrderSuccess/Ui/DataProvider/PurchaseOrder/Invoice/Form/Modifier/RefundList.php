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
 * Class RefundList
 * @package Magestore\PurchaseOrderSuccess\Ui\DataProvider\PurchaseOrder\Invoice\Form\Modifier
 */
class RefundList extends AbstractModifier
{    
    /**
     * @var string
     */
    protected $groupContainer = 'refund_list';

    /**
     * @var string
     */
    protected $groupLabel = 'Refund List';

    /**
     * @var int
     */
    protected $sortOrder = 40;
    
    protected $children = [
        'button_set' => 'button_set',
        'invoice_refund_list_listing' => 'os_purchase_order_invoice_refund_listing',
        'register_refund_modal' => 'register_refund_modal',
        'register_refund_modal_form' => 'os_purchase_order_invoice_refund_form'
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
    public function modifyMeta(array $meta){
        if(!$this->getCurrentInvoice())
            return $meta;
        $meta = array_replace_recursive(
            $meta,
            [
                $this->groupContainer => [
                    'children' => $this->getRefundListChildren(),
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __($this->groupLabel),
                                'component' => 'Magestore_PurchaseOrderSuccess/js/form/components/fieldset',
                                'collapsible' => true,
                                'visible' => $this->getVisible(),
                                'opened' => false,
                                'componentType' => \Magento\Ui\Component\Form\Fieldset::NAME,
                                'sortOrder' => $this->getSortOrder(),
                                'actions' => [
                                    [
                                        'targetName' => $this->scopeName . '.' . $this->groupContainer . '.' .
                                            $this->children['invoice_refund_list_listing'],
                                        'actionName' => 'render',
                                    ]
                                ]
                            ]
                        ],
                    ],
                ],
            ]
        );
        return $meta;   
    }

    /**
     * Add invoice refund form fields
     * 
     * @return array
     */
    public function getRefundListChildren(){
        $invoice = $this->getCurrentInvoice();
        $children = [];
        if($invoice->getGrandTotalInclTax() - $invoice->getTotalDue() > $invoice->getTotalRefund())
            $children[$this->children['button_set']] = $this->getRefundButtons();
        $children[$this->children['invoice_refund_list_listing']] = $this->getInvoiceRefundListing();
        return $children;
    }

    /**
     * Get invoice refund buttons
     *
     * @return array
     */
    public function getRefundButtons(){
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
                'register_refund_button' => $this->addButton(
                    'Refund',
                    [
                        [
                            'targetName' => $this->scopeName . '.' . $this->groupContainer
                                . '.' . $this->children['button_set']
                                . '.' . $this->children['register_refund_modal'],
                            'actionName' => 'openModal'
                        ],[
                            'targetName' => $this->scopeName . '.' . $this->groupContainer
                                . '.' . $this->children['button_set']
                                . '.' . $this->children['register_refund_modal']
                                . '.' . $this->children['register_refund_modal_form'],
                            'actionName' => 'render'
                        ]
                    ]
                ),
                $this->children['register_refund_modal'] => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Modal::NAME,
                                'type' => 'container',
                                'options' => [
                                    'onCancel' => 'actionCancel',
                                    'title' => __('Refund Invoice'),
                                    'buttons' => [
                                        [
                                            'text' => __('Cancel'),
                                            'actions' => ['closeModal']
                                        ],
                                        [
                                            'text' => __('Submit Refund'),
                                            'class' => 'action-primary',
                                            'actions' => [
                                                [
                                                    'targetName' => $this->children['register_refund_modal_form']
                                                        . '.' . $this->children['register_refund_modal_form'],
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
                        $this->children['register_refund_modal_form'] => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'autoRender' => false,
                                        'componentType' => 'insertForm',
                                        'component' => 'Magestore_PurchaseOrderSuccess/js/form/components/insert-form',
                                        'ns' => $this->children['register_refund_modal_form'],
                                        'sortOrder' => '25',
                                        'params' => [
                                            'purchase_id' => $this->getCurrentInvoice()->getPurchaseOrderId(),
                                            'invoice_id' => $this->getCurrentInvoice()->getPurchaseOrderInvoiceId()
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
     * Get invoice refund listing
     * 
     * @return array
     */
    public function getInvoiceRefundListing(){
        $dataScope = 'invoice_refund_list_listing';
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'component' => 'Magestore_PurchaseOrderSuccess/js/form/components/insert-listing',
                        'autoRender' => false,
                        'componentType' => 'insertListing',
                        'dataScope' => $this->children[$dataScope],
                        'externalProvider' => $this->children[$dataScope]. '.' . $this->children[$dataScope]
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
                            'purchase_id' => '${ $.provider }:data.purchase_order_id'
                        ],
                        'exports' => [
                            'invoice_id' => '${ $.externalProvider }:params.invoice_id',
                            'purchase_id' => '${ $.externalProvider }:params.purchase_id'
                        ],
                        'selectionsProvider' =>
                            $this->children[$dataScope]
                            . '.' . $this->children[$dataScope]
                            . '.purchase_order_invoice_refund_template_columns.ids'
                    ]
                ]
            ]
        ];
    }
}