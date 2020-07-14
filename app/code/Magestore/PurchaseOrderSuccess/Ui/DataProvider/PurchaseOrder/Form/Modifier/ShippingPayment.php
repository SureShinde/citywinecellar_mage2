<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Ui\DataProvider\PurchaseOrder\Form\Modifier;

use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\ShippingMethod;
use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\PaymentTerm;
use Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderInterface;
use Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Invoice\Payment;

/**
 * Class ShippingPayment
 * @package Magestore\PurchaseOrderSuccess\Ui\DataProvider\PurchaseOrder\Form\Modifier
 */
class ShippingPayment extends AbstractModifier
{    
    /**
     * @var string
     */
    protected $groupContainer = 'shipping_payment';

    /**
     * @var string
     */
    protected $groupLabel = 'Shipping and Payment';

    /**
     * @var string
     */
    protected $scopeName = 'os_purchase_order_form.os_purchase_order_form';

    /**
     * @var int
     */
    protected $sortOrder = 70;

    /**
     * @var array
     */
    protected $shippingMethods;

    /**
     * @var array
     */
    protected $paymentTerms;
    
    /**
     * modify data
     *
     * @return array
     */
    public function modifyData(array $data)
    {
        if($id = $this->request->getParam('id', null)){
            $data = $this->addAdditionalValue(
                $data, $id, PurchaseOrderInterface::SHIPPING_METHOD, $this->shippingMethods, ShippingMethod::OPTION_NEW_VALUE
            );
            $data = $this->addAdditionalValue(
                $data, $id, PurchaseOrderInterface::PAYMENT_TERM, $this->paymentTerms, PaymentTerm::OPTION_NEW_VALUE
            );
        }
        return $data;
    }
    
    public function addAdditionalValue($data, $purchaseId, $primaryField, $options, $newValue){
        $purchaseData = $data[$purchaseId];
        if(isset($purchaseData[$primaryField]) && $purchaseData[$primaryField] != ''){
            $value = $this->searchSubArray($options, 'value', $purchaseData[$primaryField]);
            if(!is_array($value)){
                $purchaseData['new_'.$primaryField] = $purchaseData[$primaryField];
                $purchaseData[$primaryField] = $newValue;
                $data[$purchaseData[PurchaseOrderInterface::PURCHASE_ORDER_ID]] = $purchaseData;
            }
        }
        return $data;
    }

    /**
     * Search an subarray with key and value itself
     *
     * @param array $array
     * @param string $key
     * @param mixed $value
     * @return array|null
     */
    public function searchSubArray($array, $key, $value) {
        foreach ($array as $subarray){
            if (isset($subarray[$key]) && $subarray[$key] == $value)
                return $subarray;
        }
    }

    /**
     * Modify purchase order form meta
     * 
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta){
        if(!$this->request->getParam('id',null)){
            return $meta;
        }
        $meta = array_replace_recursive(
            $meta,
            [
                $this->groupContainer => [
                    'children' => $this->getPaymentShippingChildren(),
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __($this->groupLabel),
                                'collapsible' => true,
                                'dataScope' => 'data',
                                'visible' => $this->getVisible(),
                                'opened' => $this->getOpened(),
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
    public function getPaymentShippingChildren(){
        $this->shippingMethods = $this->objectManager
            ->get('Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\ShippingMethod')->getOptionArray();
        $this->paymentTerms = $this->objectManager
            ->get('Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\PaymentTerm')->getOptionArray();
        $orderSource = $this->objectManager
            ->get('Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\OrderSource')->getOptionArray();
        $children = [
            'shipping_address' => $this->addFormFieldTextArea('Shipping Address', 10),
            'shipping_method' => $this->addFormFieldSelect(
                'Shipping Method', $this->shippingMethods, 20, false, 
                ShippingMethod::OPTION_NONE_VALUE, '', $this->switcherShipping()
            ),
            'new_shipping_method' => $this->addFormFieldText('New Shipping Method', 'input', 30),
            'shipping_cost' => $this->addFormFieldText('Shipping Cost', 'input', 40),
            'started_at' => $this->addFormFieldDate('Start Shipping Date', 50),
            'expected_at' => $this->addFormFieldDate('Expected Delivery Date', 60),
            'payment_term' => $this->addFormFieldSelect(
                'Payment Term',  $this->paymentTerms, 70, false, 
                PaymentTerm::OPTION_NONE_VALUE, '', $this->switcherPaymentTerm()
            ),
            'new_payment_term' => $this->addFormFieldText('New Payment Term', 'input', 80),
            'placed_via' => $this->addFormFieldSelect('Sales Place Via', $orderSource, 90),
        ];
        return $children;
    }

    /**
     * Get switcher config for shipping method select field
     * 
     * @return array
     */
    public function switcherShipping(){
        $rule = [];
        $rule = $this->addSwitcherConfigRule(
            $rule, $this->shippingMethods, ShippingMethod::OPTION_NEW_VALUE, 'new_shipping_method'
        );
        return $rule;
    }

    /**
     * Get switcher config for payment term
     * 
     * @return array
     */
    public function switcherPaymentTerm(){
        $rule = [];
        $rule = $this->addSwitcherConfigRule(
            $rule, $this->paymentTerms, PaymentTerm::OPTION_NEW_VALUE, 'new_payment_term'
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