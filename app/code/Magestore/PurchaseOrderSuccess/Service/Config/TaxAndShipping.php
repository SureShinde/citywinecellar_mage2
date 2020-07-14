<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Service\Config;

use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\ShippingMethod as ShippingMethodOption;

/**
 * Class TaxAndShipping
 * @package Magestore\PurchaseOrderSuccess\Service\Config
 */
class TaxAndShipping extends AbstractConfig
{
    const DEFAULT_SHIPPING_TYPE_CONFIG_PATH = 'purchaseordersuccess/tax_and_shipping/shipping_price';

    const DEFAULT_SHIPPING_COST_CONFIG_PATH = 'purchaseordersuccess/tax_and_shipping/default_shipping_cost';

    const DEFAULT_TAX_TYPE_CONFIG_PATH = 'purchaseordersuccess/tax_and_shipping/customer_tax';

    const DEFAULT_TAX_CONFIG_PATH = 'purchaseordersuccess/tax_and_shipping/default_tax';

    /**
     * @return int
     */
    public function getShippingType(){
        return $this->scopeConfig->getValue(self::DEFAULT_SHIPPING_TYPE_CONFIG_PATH);
    }

    /**
     * @return float
     */
    public function getDefaultShippingCost(){
        return $this->scopeConfig->getValue(self::DEFAULT_SHIPPING_COST_CONFIG_PATH);
    }

    /**
     * @return int
     */
    public function getTaxType(){
        return $this->scopeConfig->getValue(self::DEFAULT_TAX_TYPE_CONFIG_PATH);
    }

    /**
     * Get Default Tax
     *
     * @return float
     */
    public function getDefaultTax(){
        return $this->scopeConfig->getValue(self::DEFAULT_TAX_CONFIG_PATH);
    }
}