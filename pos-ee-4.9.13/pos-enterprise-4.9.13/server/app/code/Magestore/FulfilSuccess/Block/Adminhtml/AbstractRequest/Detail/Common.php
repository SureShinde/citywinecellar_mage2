<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Block\Adminhtml\AbstractRequest\Detail;

class Common extends \Magento\Sales\Block\Adminhtml\Order\View\Info
{
    protected $alternativeTemplate = '';
    /**
     * Retrieve required options from parent
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function _beforeToHtml()
    {
        if($this->alternativeTemplate) {
            $this->setTemplate($this->alternativeTemplate);
        }
        if (!$this->getParentBlock()) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Please correct the parent block for this block.')
            );
        }
        $this->setOrder($this->getParentBlock()->getOrder());

        $orderInfo = $this->getParentBlock()->getOrderInfoData();
        if ($orderInfo && count($orderInfo)) {
            foreach ($orderInfo as $k => $v) {
                $this->setDataUsingMethod($k, $v);
            }
        }
    }
}
