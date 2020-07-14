<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Block\Adminhtml\AbstractRequest\Detail\Order;

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