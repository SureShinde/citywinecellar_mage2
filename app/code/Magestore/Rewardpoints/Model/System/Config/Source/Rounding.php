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
 * @package     Magestore_Affiliateplus
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

namespace Magestore\Rewardpoints\Model\System\Config\Source;

/**
 * RewardPoints Config Source Rounding Model
 */
class Rounding implements \Magento\Framework\Option\ArrayInterface
{
    const REFER_URL_PARAM_IDENTIFY = '1';
    const REFER_URL_PARAM_AFFILIATE_ID = '2';

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'round', 'label' => __('Normal')],
            ['value' => 'floor', 'label' => __('Rounding Down')],
            ['value' => 'ceil', 'label' => __('Rounding Up')],
        ];
    }
}
