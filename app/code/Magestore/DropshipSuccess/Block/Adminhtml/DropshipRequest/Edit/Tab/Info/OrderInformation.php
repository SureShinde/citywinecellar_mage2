<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\DropshipSuccess\Block\Adminhtml\DropshipRequest\Edit\Tab\Info;

use Magento\Sales\Model\Order\Address;
use Magestore\DropshipSuccess\Block\Adminhtml\DropshipRequest\Edit\Tab\AbstractDropshipRequestTab;

/**
 * Class OrderInformation
 * @package Magestore\DropshipSuccess\Block\Adminhtml\DropshipRequest\Edit\Tab\Info
 */
class OrderInformation extends AbstractDropshipRequestTab
{

    /**
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        /** @var \Magestore\DropshipSuccess\Model\DropshipRequest $dropshipRequest */
        $dropshipRequest = $this->getDropshipRequest();
        $orderId = $dropshipRequest->getOrderId();
        return $this->orderRepository->get($orderId);
    }

    /**
     * Returns string with formatted address
     *
     * @param Address $address
     * @return null|string
     */
    public function getFormattedAddress(Address $address)
    {
        return $this->addressRenderer->format($address, 'html');
    }
}
