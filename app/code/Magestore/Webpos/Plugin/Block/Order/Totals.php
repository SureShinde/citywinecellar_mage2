<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Plugin\Block\Order;


/**
 * Class Totals
 * @package Magestore\Webpos\Block\Adminhtml\Order
 */
class Totals extends \Magento\Sales\Block\Order\Totals
{

    const POS_CUSTOM_DISCOUNT = "POS_CUSTOM_DISCOUNT";
    const PERCENT_PRICE_TYPE = '%';

    /**
     * @param \Magento\Sales\Block\Order\Totals $subject
     * @param array $totals
     * @return array
     */
    public function afterGetTotals(\Magento\Sales\Block\Order\Totals $subject, array $totals)
    {
        foreach ($totals as $total){
            if($total->getCode() == 'discount'){
                if ($subject->getOrder()->getAppliedRuleIds() == self::POS_CUSTOM_DISCOUNT && $subject->getOrder()->getOsPosCustomDiscountAmount()) {
                    $label = "Custom Discount";
                    if ($subject->getOrder()->getOsPosCustomDiscountType() == self::PERCENT_PRICE_TYPE) {
                        $label .= sprintf(" (%.2f%%)", $subject->getOrder()->getOsPosCustomDiscountAmount());
                    }
                    $total->setLabel($label);
                }
            }
        }
        return $totals;
    }
}