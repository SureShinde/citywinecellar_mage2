<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Sales\Order;

use Magento\Sales\Api\Data\OrderInterface;
use Magestore\Webpos\Model\Source\Adminhtml\Since;


/**
 * Class OrderRepository
 * @package Magestore\Webpos\Model\Sales\Order
 */
class StatusRepository implements \Magestore\Webpos\Api\Sales\Order\StatusRepositoryInterface
{
    /**
     * @var /Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory
     */
    protected $statusCollectionFactory;

    /**
     * @var \Magento\Framework\Api\SearchResults
     */
    protected $statusResultInterface;

    /**
     * OrderRepository constructor.
     * @param /Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory $statusCollectionFactory
     * @param \Magento\Framework\Api\SearchResults $statusResultInterface
     */
    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory $statusCollectionFactory,
        \Magento\Framework\Api\SearchResults $statusResultInterface
    ) {
        $this->statusCollectionFactory = $statusCollectionFactory;
        $this->statusResultInterface = $statusResultInterface;
    }

    /**
     * @return \Magento\Framework\Api\SearchResults|void
     */
    public function getStatuses() {
        $collection = $this->statusCollectionFactory->create();
        $collection->joinStates();
//        foreach ($collection as $status) {
//            $statuses
//        }
        $statuses = $this->statusResultInterface;
        $statuses->setItems($collection->getData());
        $statuses->setTotalCount($collection->getSize());
        return $statuses;
    }


}
