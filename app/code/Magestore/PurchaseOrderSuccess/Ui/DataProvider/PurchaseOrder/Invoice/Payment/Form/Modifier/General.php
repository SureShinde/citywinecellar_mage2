<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Ui\DataProvider\PurchaseOrder\Invoice\Payment\Form\Modifier;

use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\PaymentMethod;
/**
 * Class General
 * @package Magestore\PurchaseOrderSuccess\Ui\DataProvider\PurchaseOrder\Form\Modifier
 */
class General extends \Magestore\PurchaseOrderSuccess\Ui\DataProvider\Modifier\AbstractModifier
{
    /**
     * @var int $purchaseId
     */
    protected $purchaseId;

    /**
     * @var int 
     */
    protected $purchaseInvoiceId;

    /**
     * @var array
     */
    protected $paymentMethods;

    /**
     * @var string
     */
    protected $scopeName = 'os_purchase_order_invoice_payment_form.os_purchase_order_invoice_payment_form';
    
    /**
     * @var string
     */
    protected $groupContainer = 'general_information';

    /**
     * @var string
     */
    protected $groupLabel = 'General Information';

    /**
     * @var int
     */
    protected $sortOrder = 70;

    /**
     * @return int|mixed
     */
    public function getPurchaseOrderId(){
        if(!$this->purchaseId){
            $this->purchaseId = $this->request->getParam('purchase_id', null);
        }
        return $this->purchaseId;
    }

    /**
     * @return int|mixed
     */
    public function getPurchaseOrderInvoiceId(){
        if(!$this->purchaseInvoiceId){
            $this->purchaseInvoiceId = $this->request->getParam('invoice_id', null);
        }
        return $this->purchaseInvoiceId;
    }
    
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
        $meta = array_replace_recursive(
            $meta,
            [
                $this->groupContainer => [
                    'children' => $this->getGeneralChildren(),
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => '',
                                'collapsible' => false,
                                'dataScope' => 'data',
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
    public function getGeneralChildren(){
        $this->paymentMethods = $this->objectManager
            ->get('Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\PaymentMethod')->getOptionArray();
        $children = [
            'purchase_order_invoice_id' => $this->addFormFieldText('', 'hidden', 10),
            'payment_at' => $this->addFormFieldDate('Payment Date', 20, true),
            'payment_method' =>  $this->addFormFieldSelect(
                'Payment Method', $this->paymentMethods, 30, true, null, '', $this->switcherPayment()
            ),
            'new_payment_method' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => \Magento\Ui\Component\Form\Field::NAME,
                            'label' => __('New Payment Method'),
                            'dataType' => 'text',
                            'formElement' => 'input',
                            'sortOrder' => 40,
                            'visible' => false,
                            'validation' => [
                                'required-entry' => true,
                            ],
                        ],
                    ],
                ],
            ],
            'payment_amount' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => \Magento\Ui\Component\Form\Field::NAME,
                            'label' => __('Payment Amount'),
                            'dataType' => 'text',
                            'formElement' => 'input',
                            'sortOrder' => 50,
                            'validation' => [
                                'required-entry' => true,
                                'validate-number' => true,
                                'alidate-greater-than-zero' => true
                            ],
                        ],
                    ],
                ],
            ],
            'description' => $this->addFormFieldTextArea('Description', 60)
        ];
        return $children;
    }

    
    /**
     * Get switcher config for shipping method select field
     *
     * @return array
     */
    public function switcherPayment(){
        $rule = [];
        $rule = $this->addSwitcherConfigRule(
            $rule, $this->paymentMethods, PaymentMethod::OPTION_NEW_VALUE, 'new_payment_method'
        );
        return $rule;
    }

    
    public function addSwitcherConfigRule($rule, $optionArray, $newValue, $target){
        foreach ($optionArray as $index => $method){
            if($method['value'] == $newValue){
                $rule[$index] = [
                    'value' => $method['value'],
                    'actions' => [
                        0 => [
                            'target' => $this->scopeName.'.'.$this->groupContainer.'.'.$target,
                            'callback' => 'show'
                        ]
                    ]
                ];
            }else{
                $rule[$index] = [
                    'value' => $method['value'],
                    'actions' => [
                        0 => [
                            'target' => $this->scopeName.'.'.$this->groupContainer.'.'.$target,
                            'callback' => 'hide'
                        ]
                    ]
                ];
            }
        }
        return $rule;
    }
}