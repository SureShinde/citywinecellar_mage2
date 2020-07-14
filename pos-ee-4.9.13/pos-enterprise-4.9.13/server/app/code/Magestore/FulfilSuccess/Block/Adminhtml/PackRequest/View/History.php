<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\FulfilSuccess\Block\Adminhtml\PackRequest\View;

/**
 * Class History
 * @package Magestore\FulfilSuccess\Block\Adminhtml\PackRequest\View
 */
class History extends \Magento\Sales\Block\Adminhtml\Order\View\History
{
    /**
     * Submit URL getter
     *
     * @return string
     */
    public function getSubmitUrl()
    {
        return $this->getUrl('sales/order/addComment', ['order_id' => $this->getOrder()->getId()]);
    }

}
