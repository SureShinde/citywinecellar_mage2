<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\OrderSuccess\Plugin\Order\View\History;


class AfterGetSubmitUrl{

    /**
     * @param \Magento\Sales\Block\Adminhtml\Order\View\History $historyBlock
     * @param string $url
     * 
     * @return string
     */
    public function afterGetSubmitUrl(\Magento\Sales\Block\Adminhtml\Order\View\History $historyBlock, $url)
    {
        if(in_array($historyBlock->getRequest()->getModuleName(), ['fulfilsuccess', 'ordersuccess'])) {
            return $historyBlock->getUrl('sales/order/addComment', ['order_id' => $historyBlock->getOrder()->getId()]);
        }
        return $url;
    }

}
