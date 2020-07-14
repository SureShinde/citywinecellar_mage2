<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\DropshipSuccess\Block\Supplier;

use \Magento\Framework\App\ObjectManager;
use Magento\Sales\Model\Order\Address;

/**
 * Sales order history block
 */
class ViewDropship extends AbstractSupplier
{
    /**
     * @var string
     */
    protected $_template = 'supplier/viewdropship.phtml';

    /**
     * @var
     */
    protected $dropships;

    /**
     * @return \Magestore\DropshipSuccess\Api\Data\DropshipRequestInterface
     */
    public function getDropshipRequestById()
    {
        $dropshipId = $this->getRequest()->getParam('dropship_id');
        if ($dropshipId) {
            return $this->dropshipRequestRepositoryInterface->getById($dropshipId);
        }
    }

    /**
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        /** @var \Magestore\DropshipSuccess\Model\DropshipRequest $dropshipRequest */
        $dropshipRequest = $this->getDropshipRequestById();
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

    /**
     * @return array
     */
    public function getDropshipStatus()
    {
        return $this->dropshipRequestInterface->getStatusOption();
    }
}
