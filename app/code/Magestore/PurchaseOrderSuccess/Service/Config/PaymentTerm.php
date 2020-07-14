<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Service\Config;

use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\PaymentTerm as PaymentTermOption;

/**
 * Class PaymentTerm
 * @package Magestore\PurchaseOrderSuccess\Service\Config
 */
class PaymentTerm extends AbstractConfig
{
    const PURCHASE_ORDER_CONFIG_PATH = 'purchaseordersuccess/payment_term/payment_term';

    /**
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderInterface $purchaseOrder
     * @return string
     */
    public function initNewConfig(\Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderInterface $purchaseOrder){
        if(!$purchaseOrder->getPaymentTerm())
            $purchaseOrder->setPaymentTerm(PaymentTermOption::OPTION_NONE_VALUE);
        if($purchaseOrder->getPaymentTerm() == PaymentTermOption::OPTION_NEW_VALUE){
            $purchaseOrder->setPaymentTerm($purchaseOrder->getData('new_payment_term'));
        }
        return $purchaseOrder->getPaymentTerm();
    }

    /**
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderInterface $purchaseOrder
     * @return bool
     */
    public function isNoneValueMethod($purchaseOrder){
        if($purchaseOrder->getPaymentTerm() == PaymentTermOption::OPTION_NONE_VALUE)
            return true;
        return false;
    }

    /**
     * Generate new element config.
     *
     * @param string $newConfig
     * @return array
     */
    public function generateNewConfig($newConfig){
        return [
            'name' => $newConfig,
            'description' => '',
            'status' => \Magestore\PurchaseOrderSuccess\Block\Adminhtml\Form\Field\Status::ENABLE_VALUE
        ];
    }
}