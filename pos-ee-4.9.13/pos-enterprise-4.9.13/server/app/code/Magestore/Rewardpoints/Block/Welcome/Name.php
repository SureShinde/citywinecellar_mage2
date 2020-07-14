<?php
namespace Magestore\Rewardpoints\Block\Welcome;

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

/**
 * Class Name
 * @package Magestore\Rewardpoints\Block\Welcome
 */
class Name extends \Magento\Framework\View\Element\Template {

    /**
     * Name constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magestore\Rewardpoints\Helper\Point $helperPoint
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magestore\Rewardpoints\Helper\Point $helperPoint
    )
    {
        parent::__construct($context, []);
        $this->helperPoint = $helperPoint;

    }

    public function _toHtml() {
        parent::_toHtml();
        return $this->helperPoint->getPluralName();
    }

}
