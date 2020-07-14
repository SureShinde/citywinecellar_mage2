<?php
/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

namespace Magestore\Rewardpoints\Model\Total\Quote;

/**
 * Class Point
 * @package Magestore\Rewardpoints\Model\Total\Quote
 */
class EarningPoint extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    protected $_code = 'rewardpointsearning';

    /**
     * add point label
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return Magestore_RewardPoints_Model_Total_Quote_Label
     */
    public function fetch(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Model\Quote\Address\Total $total
    )
    {
        if ($quote->getRewardpointsEarn() != 0) {
            return [
                'code' => $this->getCode(),
                'title' => __('You will earn'),
                'value' => $quote->getRewardpointsEarn(),
            ];
        }
        return [];
    }
}
